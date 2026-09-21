<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

/**
 * Two audiences, one model: staff act through the 'projects.*' permissions,
 * a client only ever reaches their own projects.
 */
class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('projects.view') || $user->isClient();
    }

    public function view(User $user, Project $project): bool
    {
        return $user->hasPermission('projects.view') || $project->client_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('projects.manage') || $user->isClient();
    }

    public function update(User $user, Project $project): bool
    {
        return $user->hasPermission('projects.manage');
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasPermission('projects.manage');
    }

    /**
     * Clients may keep adding material to a project of their own until it
     * is delivered or cancelled; after that they ask us to reopen it.
     */
    public function upload(User $user, Project $project): bool
    {
        if ($user->hasPermission('projects.manage')) {
            return true;
        }

        return $project->client_id === $user->id
            && in_array($project->status, [Project::STATUS_ACTIVE, Project::STATUS_ON_HOLD], true);
    }
}
