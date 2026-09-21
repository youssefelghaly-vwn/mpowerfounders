<?php

namespace Tests\Feature\Auth;

use App\Mail\NewRegistrationAlertMail;
use App\Mail\RegistrationReceivedMail;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        Mail::fake();
    }

    public function test_registration_creates_a_pending_account_with_no_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Dana Cho',
            'email' => 'dana@example.com',
            'company' => 'Cho Media',
            'about' => 'Weekly founder interviews.',
            'password' => 'password-is-long',
            'password_confirmation' => 'password-is-long',
        ]);

        $response->assertRedirect(route('register.pending'));

        $user = User::where('email', 'dana@example.com')->firstOrFail();

        $this->assertSame(User::STATUS_PENDING, $user->status);
        $this->assertCount(0, $user->roles);
        $this->assertNull($user->activated_at);

        // Registering must not log anyone in — there is nothing to use yet.
        $this->assertGuest();
    }

    public function test_registration_emails_the_applicant_and_the_team(): void
    {
        $staff = User::factory()->withRole('admin')->create();

        $this->post('/register', [
            'name' => 'Dana Cho',
            'email' => 'dana@example.com',
            'password' => 'password-is-long',
            'password_confirmation' => 'password-is-long',
        ]);

        Mail::assertSent(RegistrationReceivedMail::class, fn ($mail) => $mail->hasTo('dana@example.com'));
        Mail::assertSent(NewRegistrationAlertMail::class, fn ($mail) => $mail->hasTo($staff->email));
    }

    public function test_a_pending_account_cannot_sign_in(): void
    {
        $user = User::factory()->pending()->create(['password' => 'password-is-long']);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password-is-long',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_a_suspended_account_cannot_sign_in(): void
    {
        $user = User::factory()->suspended()->create(['password' => 'password-is-long']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password-is-long',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_an_active_client_lands_in_the_portal_and_staff_in_the_admin_panel(): void
    {
        $client = User::factory()->client()->create(['password' => 'password-is-long']);

        $this->post('/login', ['email' => $client->email, 'password' => 'password-is-long'])
            ->assertRedirect(route('portal.dashboard'));

        $this->post('/logout');

        $staff = User::factory()->withRole('admin')->create(['password' => 'password-is-long']);

        $this->post('/login', ['email' => $staff->email, 'password' => 'password-is-long'])
            ->assertRedirect(route('admin.dashboard'));
    }
}
