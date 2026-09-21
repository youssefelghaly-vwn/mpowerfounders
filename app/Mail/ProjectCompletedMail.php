<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Final hand-off: the project reached a stage flagged is_final. */
class ProjectCompletedMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Project $project) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "{$this->project->title} is ready");
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.projects.completed',
            with: [
                'projectUrl' => route('portal.projects.show', $this->project),
                'deliverables' => $this->project->media()
                    ->deliverables()
                    ->visibleToClient()
                    ->get(),
            ],
        );
    }
}
