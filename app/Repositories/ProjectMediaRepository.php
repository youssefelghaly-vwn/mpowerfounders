<?php

namespace App\Repositories;

use App\Models\ProjectMedia;

class ProjectMediaRepository
{
    public function create(array $data): ProjectMedia
    {
        return ProjectMedia::create($data);
    }

    public function update(ProjectMedia $media, array $data): ProjectMedia
    {
        $media->update($data);

        return $media;
    }

    public function delete(ProjectMedia $media): void
    {
        $media->delete();
    }
}
