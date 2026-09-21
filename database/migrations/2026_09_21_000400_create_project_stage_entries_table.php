<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The per-stage workspace for a project: one row the first time a project
 * enters a stage, carrying our own notes for that stage and acting as the
 * bucket the stage's uploads (cuts, thumbnails, revisions) belong to.
 *
 * Re-entering a stage reuses the row rather than creating a second one —
 * the audit trail of moves lives in project_stage_histories, this is the
 * working surface.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_stage_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pipeline_stage_id')->constrained()->cascadeOnDelete();
            // Internal working notes.
            $table->text('notes')->nullable();
            // Written for the client, surfaced in the portal timeline.
            $table->text('client_summary')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('entered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'pipeline_stage_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_stage_entries');
    }
};
