<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Puts uploaded files on the object store (S3 in production, MinIO
 * locally) and hands back the row attributes ProjectMedia needs.
 *
 * Objects are keyed projects/{reference}/{stage-or-source}/{ulid}.{ext} —
 * the ULID keeps two files of the same name from colliding, and the
 * original filename is kept separately on the row for display and for the
 * Content-Disposition header on download.
 */
class MediaStorageService
{
    public function disk(): string
    {
        return (string) config('media.disk', 's3');
    }

    /**
     * @return array<string, mixed> attributes ready for ProjectMedia::create()
     */
    public function store(UploadedFile $file, Project $project, ?int $stageId = null): array
    {
        $disk = $this->disk();
        $extension = Str::lower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
        $folder = $stageId ? 'stage-'.$stageId : 'source';
        $name = (string) Str::ulid().'.'.$extension;

        // putFileAs streams the temp file up rather than reading it into
        // memory — necessary once uploads are measured in gigabytes.
        $path = Storage::disk($disk)->putFileAs(
            "projects/{$project->reference}/{$folder}",
            $file,
            $name,
        );

        if ($path === false) {
            throw new \RuntimeException("Could not store [{$file->getClientOriginalName()}] on the [{$disk}] disk.");
        }

        return [
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType() ?: $file->getMimeType(),
            'size' => $file->getSize() ?: 0,
        ];
    }

    public function delete(string $disk, string $path): void
    {
        Storage::disk($disk)->delete($path);
    }
}
