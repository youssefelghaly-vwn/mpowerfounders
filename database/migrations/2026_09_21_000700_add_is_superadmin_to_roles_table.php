<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A role flagged is_superadmin grants every ability in the admin panel
 * without any permission being attached to it — see the Gate::before hook
 * in AppServiceProvider. Existing installs get the flag on the seeded
 * 'admin' role, which already held every permission anyway.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_superadmin')->default(false)->after('description')->index();
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('is_superadmin');
        });
    }
};
