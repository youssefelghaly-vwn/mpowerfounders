<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registration is an application, not an activation: a new user lands here
 * as 'pending' with no role attached, and stays locked out until someone
 * on our side activates them (which is what attaches the 'client' role and
 * sends the welcome email). These columns carry that state.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('password')->index();
            $table->string('company')->nullable()->after('status');
            $table->string('phone')->nullable()->after('company');
            // What the applicant told us about themselves at sign-up, and
            // what we noted internally afterwards. Kept apart on purpose:
            // the first is shown back to them, the second never is.
            $table->text('about')->nullable()->after('phone');
            $table->text('admin_notes')->nullable()->after('about');
            $table->timestamp('activated_at')->nullable()->after('admin_notes');
            $table->foreignId('activated_by')->nullable()->after('activated_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('activated_by');
            $table->dropColumn(['status', 'company', 'phone', 'about', 'admin_notes', 'activated_at']);
        });
    }
};
