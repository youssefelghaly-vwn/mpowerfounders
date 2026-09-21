<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The production pipeline itself — "Intake", "Preparing", "Editing",
 * "Review", "Delivered", ... Admin-managed rather than a PHP enum, because
 * the shape of the pipeline is a business decision that changes without a
 * deploy. Order is by `position`; `is_default` marks where new projects
 * enter, `is_final` marks the stages that count a project as delivered.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            // Instructions shown to the client while a project sits here,
            // e.g. "Our editors are cutting your first draft."
            $table->text('client_message')->nullable();
            $table->unsignedInteger('position')->default(0)->index();
            $table->string('color', 32)->default('slate');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_final')->default(false);
            // Whether entering this stage emails the client at all. Some
            // internal stages are noise from the client's point of view.
            $table->boolean('notifies_client')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipeline_stages');
    }
};
