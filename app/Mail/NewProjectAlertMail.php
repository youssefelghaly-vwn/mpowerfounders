<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Internal: new footage has landed and needs picking up. */
class NewProjectAlertMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Project $project) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "New upload from {$this->project->client->name} — {$this->project->reference}");
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.new-project',
            with: ['projectUrl' => route('admin.projects.show', $this->project)],
        );
    }
}
