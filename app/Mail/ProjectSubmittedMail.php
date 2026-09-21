<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Receipt for the client: we have the files, here is what happens next. */
class ProjectSubmittedMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Project $project) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "We've received your upload — {$this->project->reference}");
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.projects.submitted',
            with: ['projectUrl' => route('portal.projects.show', $this->project)],
        );
    }
}
