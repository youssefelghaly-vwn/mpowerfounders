<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\ProjectMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Our side published files against a stage and marked them client-visible
 * — a first cut, a revision, a thumbnail set.
 *
 * @property Collection<int, ProjectMedia> $media
 */
class ProjectMediaAddedMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Project $project, public Collection $media) {}

    public function envelope(): Envelope
    {
        $count = $this->media->count();

        return new Envelope(
            subject: $count === 1
                ? "A new file is ready on {$this->project->title}"
                : "{$count} new files are ready on {$this->project->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.projects.media-added',
            with: ['projectUrl' => route('portal.projects.show', $this->project)],
        );
    }
}
