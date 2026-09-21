<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PipelineStageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Guards against a screen existing but being unreachable — a section
 * added to routes/admin.php with no matching entry in the sidebar fails
 * here rather than being found by a user who cannot get to it.
 */
class NavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(PipelineStageSeeder::class);
    }

    /** @return array<int, string> every admin landing screen, by route name */
    private function adminLandingRoutes(): array
    {
        return collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn (?string $name) => $name === 'admin.dashboard'
                || (str_starts_with((string) $name, 'admin.') && str_ends_with((string) $name, '.index')))
            ->values()
            ->all();
    }

    public function test_the_admin_sidebar_links_to_every_admin_section(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->roles()->attach(Role::create(['name' => 'Owner', 'is_superadmin' => true]));

        $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'))->assertOk();

        $routes = $this->adminLandingRoutes();
        $this->assertNotEmpty($routes);

        foreach ($routes as $name) {
            $response->assertSee(route($name), escape: false);
        }
    }

    public function test_the_sidebar_hides_sections_the_user_cannot_open(): void
    {
        $producer = User::factory()->withRole('producer')->create();

        $this->actingAs($producer)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.projects.index'), escape: false)
            ->assertDontSee(route('admin.permissions.index'), escape: false);
    }

    public function test_the_admin_panel_links_back_to_the_public_site(): void
    {
        $admin = User::factory()->withRole('admin')->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('View public site');
    }

    public function test_the_portal_nav_covers_the_client_screens(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client)
            ->get(route('portal.dashboard'))
            ->assertOk()
            ->assertSee(route('portal.projects.index'), escape: false)
            ->assertSee(route('portal.projects.create'), escape: false);
    }

    public function test_the_public_site_offers_a_guest_a_way_in(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('login'), escape: false)
            ->assertSee(route('register'), escape: false);
    }

    public function test_the_public_site_points_a_signed_in_user_at_their_dashboard(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client)
            ->get('/')
            ->assertOk()
            ->assertSee(route('portal.dashboard'), escape: false)
            ->assertSee('Log out')
            ->assertDontSee(route('login'), escape: false);

        $staff = User::factory()->withRole('producer')->create();

        $this->actingAs($staff)
            ->get('/')
            ->assertOk()
            ->assertSee(route('admin.dashboard'), escape: false);
    }

    public function test_the_sign_in_page_links_to_registration(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('register'), escape: false);
    }
}
