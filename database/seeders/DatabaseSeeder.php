<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Both seeders are idempotent (updateOrCreate throughout), so this is
     * safe to re-run after adding a permission or a stage.
     *
     * The first admin account is deliberately not seeded — create it with
     * `php artisan mpower:create-admin`, since public registration only
     * ever creates pending client accounts.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            PipelineStageSeeder::class,
        ]);
    }
}
