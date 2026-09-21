<?php

namespace App\Services;

use App\Mail\AccountActivatedMail;
use App\Mail\AccountSuspendedMail;
use App\Mail\ClientInvitationMail;
use App\Mail\NewRegistrationAlertMail;
use App\Mail\RegistrationReceivedMail;
use App\Models\Role;
use App\Models\User;
use App\Repositories\ClientRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * Account lifecycle: apply -> activate -> (suspend / reinstate).
 *
 * A registration is an application. The account is created 'pending' with
 * no role, which is what keeps it out of both the portal and the admin
 * panel. Activation is the moment the 'client' role goes on and the
 * welcome email goes out — see activate().
 */
class ClientService
{
    public function __construct(
        private readonly ClientRepository $clients,
        private readonly MailService $mail,
    ) {}

    /** Public sign-up. Creates the pending account and raises both emails. */
    public function register(array $data): User
    {
        $user = $this->clients->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'company' => $data['company'] ?? null,
            'phone' => $data['phone'] ?? null,
            'about' => $data['about'] ?? null,
            'status' => User::STATUS_PENDING,
        ]);

        $this->mail->send($user, new RegistrationReceivedMail($user));
        $this->mail->sendToStaff(new NewRegistrationAlertMail($user));

        return $user;
    }

    /**
     * A client we add ourselves. They pick no password, so the account is
     * created with an unusable random one and the welcome email carries a
     * password-set link built from a genuine reset token.
     */
    public function createByAdmin(array $data, User $actor): User
    {
        $user = DB::transaction(function () use ($data, $actor) {
            $user = $this->clients->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(Str::random(40)),
                'company' => $data['company'] ?? null,
                'phone' => $data['phone'] ?? null,
                'about' => $data['about'] ?? null,
                'admin_notes' => $data['admin_notes'] ?? null,
                'status' => User::STATUS_ACTIVE,
                'activated_at' => now(),
                'activated_by' => $actor->id,
            ]);

            $this->attachClientRole($user);

            return $user;
        });

        $token = Password::broker()->createToken($user);

        $this->mail->send($user, new ClientInvitationMail($user, $token, $data['welcome_note'] ?? null));

        return $user;
    }

    /**
     * Flip a pending (or suspended) account live: attach the client role,
     * stamp who activated it, and send the welcome email. Accounts that
     * have never had a usable password get the invitation email instead,
     * so they have a way in.
     */
    public function activate(User $user, User $actor, ?string $note = null, bool $notify = true): User
    {
        $wasActive = $user->isActive();

        DB::transaction(function () use ($user, $actor) {
            $this->clients->update($user, [
                'status' => User::STATUS_ACTIVE,
                'activated_at' => $user->activated_at ?? now(),
                'activated_by' => $user->activated_by ?? $actor->id,
            ]);

            $this->attachClientRole($user);
        });

        // Re-activating an already-active account is a no-op as far as the
        // client is concerned — don't send them a second welcome.
        if ($notify && ! $wasActive) {
            $this->mail->send($user, new AccountActivatedMail($user->refresh(), $note));
        }

        return $user->refresh();
    }

    public function suspend(User $user, ?string $note = null, bool $notify = true): User
    {
        $this->clients->update($user, ['status' => User::STATUS_SUSPENDED]);

        if ($notify) {
            $this->mail->send($user, new AccountSuspendedMail($user->refresh(), $note));
        }

        return $user->refresh();
    }

    public function update(User $user, array $data): User
    {
        return $this->clients->update($user, collect($data)
            ->only(['name', 'email', 'company', 'phone', 'about', 'admin_notes'])
            ->all());
    }

    /** Re-send the password-set link for a client who never got in. */
    public function resendInvitation(User $user, ?string $note = null): User
    {
        $token = Password::broker()->createToken($user);

        $this->mail->send($user, new ClientInvitationMail($user, $token, $note));

        return $user;
    }

    /**
     * Idempotent — a user who already holds the role (or holds a staff
     * role as well) keeps every role they have.
     */
    private function attachClientRole(User $user): void
    {
        $role = Role::firstOrCreate(
            ['slug' => 'client'],
            ['name' => 'Client', 'description' => 'Uploads projects and follows them through production.'],
        );

        $user->roles()->syncWithoutDetaching([$role->id]);
    }
}
