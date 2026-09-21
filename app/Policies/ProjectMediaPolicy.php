<?php

namespace App\Policies;

use App\Models\ProjectMedia;
use App\Models\User;

class ProjectMediaPolicy
{
    /**
     * Gate on every download and playback URL. Internal working files are
     * flagged visible_to_client = false and stay invisible to the client
     * even on their own project.
     */
    public function view(User $user, ProjectMedia $media): bool
    {
        if ($user->hasPermission('projects.view')) {
            return true;
        }

        return $media->project->client_id === $user->id && $media->visible_to_client;
    }

    public function delete(User $user, ProjectMedia $media): bool
    {
        if ($user->hasPermission('projects.manage')) {
            return true;
        }

        // A client can retract something they uploaded themselves, as long
        // as we have not started working on it.
        return $media->project->client_id === $user->id
            && $media->uploaded_by === $user->id
            && $media->source === ProjectMedia::SOURCE_CLIENT
            && $media->project->status === \App\Models\Project::STATUS_ACTIVE;
    }
}
