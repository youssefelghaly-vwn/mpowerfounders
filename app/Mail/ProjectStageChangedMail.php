<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\ProjectStageHistory;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The pipeline progress email — "your project moved from Editing to
 * Review". Only sent for stages flagged notifies_client.
 */
class ProjectStageChangedMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Project $project, public ProjectStageHistory $history) {}

    public function envelope(): Envelope
    {
        $stage = $this->history->toStage?->name ?? 'the next stage';

        return new Envelope(subject: "{$this->project->title} is now in {$stage}");
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.projects.stage-changed',
            with: ['projectUrl' => route('portal.projects.show', $this->project)],
        );
    }
}
