<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class ProjectMedia extends Model
{
    use HasFactory;

    public const SOURCE_CLIENT = 'client';

    public const SOURCE_TEAM = 'team';

    public const KIND_SOURCE = 'source';

    public const KIND_DELIVERABLE = 'deliverable';

    public const KIND_REFERENCE = 'reference';

    public const KINDS = [
        self::KIND_SOURCE => 'Source footage',
        self::KIND_DELIVERABLE => 'Deliverable',
        self::KIND_REFERENCE => 'Reference',
    ];

    protected $table = 'project_media';

    protected $fillable = [
        'project_id',
        'pipeline_stage_id',
        'uploaded_by',
        'source',
        'kind',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'description',
        'visible_to_client',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'visible_to_client' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopeVisibleToClient(Builder $query): Builder
    {
        return $query->where('visible_to_client', true);
    }

    public function scopeDeliverables(Builder $query): Builder
    {
        return $query->where('kind', self::KIND_DELIVERABLE);
    }

    public function isVideo(): bool
    {
        return Str::startsWith((string) $this->mime_type, 'video/');
    }

    public function isAudio(): bool
    {
        return Str::startsWith((string) $this->mime_type, 'audio/');
    }

    public function kindLabel(): string
    {
        return self::KINDS[$this->kind] ?? Str::headline($this->kind);
    }

    public function humanSize(): string
    {
        return $this->size > 0 ? (string) Number::fileSize($this->size, precision: 1) : '—';
    }

    /**
     * A short-lived signed URL straight to the object store, so video bytes
     * never round-trip through PHP. Local/public disks can't sign, so they
     * fall back to a plain URL and, failing that, to the streaming download
     * route.
     */
    public function temporaryUrl(?int $minutes = null): ?string
    {
        $disk = Storage::disk($this->disk);
        $expiry = now()->addMinutes($minutes ?? (int) config('media.temporary_url_minutes', 30));

        try {
            return $disk->temporaryUrl($this->path, $expiry);
        } catch (\Throwable) {
            try {
                return $disk->url($this->path);
            } catch (\Throwable) {
                return null;
            }
        }
    }
}
