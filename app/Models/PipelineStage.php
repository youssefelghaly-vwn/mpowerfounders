<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PipelineStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'client_message',
        'position',
        'color',
        'is_default',
        'is_final',
        'notifies_client',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'is_default' => 'boolean',
            'is_final' => 'boolean',
            'notifies_client' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Same slug-on-save convention as Role and Permission.
        static::saving(function (PipelineStage $stage) {
            if (empty($stage->slug)) {
                $stage->slug = Str::slug($stage->name);
            }
        });
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ProjectStageEntry::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProjectMedia::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }

    /**
     * Where new projects start. Falls back to the first stage in the
     * pipeline so a mis-configured board (nothing flagged default) still
     * accepts submissions instead of silently dropping them nowhere.
     */
    public static function default(): ?self
    {
        return static::where('is_default', true)->ordered()->first()
            ?? static::ordered()->first();
    }

    public function next(): ?self
    {
        return static::where('position', '>', $this->position)
            ->ordered()
            ->first();
    }

    /** Tailwind classes for the stage pill, keyed by the stored colour name. */
    public function badgeClasses(): string
    {
        return match ($this->color) {
            'amber' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            'blue' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
            'violet' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
            'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            'rose' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
            default => 'bg-slate-100 text-slate-700 ring-slate-500/20',
        };
    }
}
