<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

/**
 * Creates the first (or another) admin account.
 *
 * Needed because public registration only ever produces a pending client
 * account — there is no way to bootstrap a staff login through the UI, and
 * seeding a default admin with a known password would be worse.
 */
class CreateAdminUser extends Command
{
    protected $signature = 'mpower:create-admin
                            {--name= : Display name}
                            {--email= : Email address}
                            {--password= : Password (prompted for when omitted)}
                            {--role=admin : Role slug to attach}';

    protected $description = 'Create an activated staff account for the admin panel';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Name');
        $email = $this->option('email') ?: $this->ask('Email');
        $password = $this->option('password') ?: $this->secret('Password');
        $roleSlug = $this->option('role');

        $validator = Validator::make(compact('name', 'email', 'password'), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $role = Role::where('slug', $roleSlug)->first();

        if (! $role) {
            $this->error("Role [{$roleSlug}] does not exist. Run `php artisan db:seed` first.");

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => User::STATUS_ACTIVE,
            'activated_at' => now(),
        ]);

        $user->roles()->syncWithoutDetaching([$role->id]);

        $this->info("Created {$user->email} with the [{$role->name}] role.");

        return self::SUCCESS;
    }
}
