<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_ON_HOLD = 'on_hold';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_ON_HOLD => 'On hold',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    public const TYPES = [
        'video' => 'Video',
        'podcast' => 'Podcast',
        'shorts' => 'Short-form clips',
        'other' => 'Other',
    ];

    protected $fillable = [
        'reference',
        'client_id',
        'created_by',
        'pipeline_stage_id',
        'title',
        'brief',
        'type',
        'status',
        'due_date',
        'stage_changed_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'stage_changed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->reference)) {
                $project->reference = static::generateReference();
            }
        });
    }

    /** Collision-checked so a duplicate never trips the unique index. */
    public static function generateReference(): string
    {
        do {
            $reference = 'MP-'.Str::upper(Str::random(6));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    public function stageEntries(): HasMany
    {
        return $this->hasMany(ProjectStageEntry::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProjectMedia::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ProjectStageHistory::class)->latest('id');
    }

    public function scopeForClient(Builder $query, User $client): Builder
    {
        return $query->where('client_id', $client->id);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? Str::headline($this->status);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? Str::headline($this->type);
    }
}
