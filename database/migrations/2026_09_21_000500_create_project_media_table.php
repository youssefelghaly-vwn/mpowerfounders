<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Every file on the platform: the client's raw uploads and everything our
 * side produces for a stage. Files live on S3/MinIO — only the object key
 * (`path`) and the disk it sits on are stored here, never the bytes.
 *
 * `pipeline_stage_id` is null for the client's original submission and set
 * for anything uploaded against a specific stage. `visible_to_client`
 * keeps internal working files out of the portal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pipeline_stage_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            // 'client' | 'team' — who the file came from, independent of
            // which account clicked upload (an admin can upload on a
            // client's behalf, and that is still a client source file).
            $table->string('source')->default('client');
            // 'source' | 'deliverable' | 'reference'
            $table->string('kind')->default('source');
            $table->string('disk');
            $table->string('path', 2048);
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->text('description')->nullable();
            $table->boolean('visible_to_client')->default(true);
            $table->timestamps();

            $table->index(['project_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_media');
    }
};
