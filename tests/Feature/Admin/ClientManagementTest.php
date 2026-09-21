<?php

namespace Tests\Feature\Admin;

use App\Mail\AccountActivatedMail;
use App\Mail\AccountSuspendedMail;
use App\Mail\ClientInvitationMail;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        Mail::fake();

        $this->admin = User::factory()->withRole('admin')->create();
    }

    public function test_activating_a_pending_account_attaches_the_client_role_and_sends_the_welcome_email(): void
    {
        $client = User::factory()->pending()->create();

        $this->actingAs($this->admin)
            ->put(route('admin.clients.status', $client), [
                'status' => User::STATUS_ACTIVE,
                'note' => 'Looking forward to working with you.',
                'notify' => '1',
            ])
            ->assertRedirect(route('admin.clients.show', $client));

        $client->refresh();

        $this->assertSame(User::STATUS_ACTIVE, $client->status);
        $this->assertTrue($client->isClient());
        $this->assertNotNull($client->activated_at);
        $this->assertSame($this->admin->id, $client->activated_by);

        Mail::assertSent(AccountActivatedMail::class, fn ($mail) => $mail->hasTo($client->email));
    }

    public function test_activation_can_be_done_quietly(): void
    {
        $client = User::factory()->pending()->create();

        $this->actingAs($this->admin)->put(route('admin.clients.status', $client), [
            'status' => User::STATUS_ACTIVE,
            'notify' => '0',
        ]);

        $this->assertTrue($client->refresh()->isActive());
        Mail::assertNotSent(AccountActivatedMail::class);
    }

    public function test_re_activating_an_active_account_does_not_send_a_second_welcome(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($this->admin)->put(route('admin.clients.status', $client), [
            'status' => User::STATUS_ACTIVE,
            'notify' => '1',
        ]);

        Mail::assertNotSent(AccountActivatedMail::class);
    }

    public function test_an_admin_can_add_a_client_who_receives_a_password_link(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.clients.store'), [
                'name' => 'Ruth Adeyemi',
                'email' => 'ruth@example.com',
                'company' => 'Adeyemi Studio',
                'welcome_note' => 'Your first edit is on us.',
            ])
            ->assertRedirect();

        $client = User::where('email', 'ruth@example.com')->firstOrFail();

        $this->assertSame(User::STATUS_ACTIVE, $client->status);
        $this->assertTrue($client->isClient());

        Mail::assertSent(ClientInvitationMail::class, function ($mail) use ($client) {
            return $mail->hasTo($client->email)
                && str_contains($mail->setPasswordUrl, 'reset-password');
        });
    }

    public function test_suspending_an_account_locks_the_user_out_and_emails_them(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($this->admin)->put(route('admin.clients.status', $client), [
            'status' => User::STATUS_SUSPENDED,
            'note' => 'Invoice overdue.',
            'notify' => '1',
        ]);

        $this->assertSame(User::STATUS_SUSPENDED, $client->refresh()->status);
        Mail::assertSent(AccountSuspendedMail::class, fn ($mail) => $mail->hasTo($client->email));

        // A live session is ended by the 'active' middleware on the next request.
        $this->actingAs($client)->get(route('portal.dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_a_client_cannot_reach_the_admin_panel(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client)->get(route('admin.clients.index'))->assertForbidden();
    }

    public function test_a_producer_can_manage_clients_but_not_roles(): void
    {
        $producer = User::factory()->withRole('producer')->create();

        $this->actingAs($producer)->get(route('admin.clients.index'))->assertOk();
        $this->actingAs($producer)->get(route('admin.roles.index'))->assertForbidden();
    }
}
