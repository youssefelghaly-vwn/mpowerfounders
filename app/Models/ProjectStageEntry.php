<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectStageEntry extends Model
{
    protected $fillable = [
        'project_id',
        'pipeline_stage_id',
        'notes',
        'client_summary',
        'assigned_to',
        'entered_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'entered_at' => 'datetime',
            'completed_at' => 'datetime',
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

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Files uploaded against this stage of this project. Matched on both
     * keys because ProjectMedia hangs off the project, not off the entry.
     */
    public function media(): HasMany
    {
        return $this->hasMany(ProjectMedia::class, 'project_id', 'project_id')
            ->where('pipeline_stage_id', $this->pipeline_stage_id);
    }
}
