<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A project is one client request: "here is my raw podcast/video, here is
 * what I want done with it". Files hang off it (project_media), and it
 * moves through the pipeline one stage at a time (pipeline_stage_id), with
 * every move recorded in project_stage_histories.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            // Short human-quotable handle used in emails and the UI.
            $table->string('reference', 16)->unique();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            // Null when the client submitted it themselves; set when an
            // admin created the project on the client's behalf.
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pipeline_stage_id')->nullable()->constrained('pipeline_stages')->nullOnDelete();
            $table->string('title');
            $table->text('brief')->nullable();
            $table->string('type')->default('video');
            $table->string('status')->default('active')->index();
            $table->date('due_date')->nullable();
            $table->timestamp('stage_changed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
