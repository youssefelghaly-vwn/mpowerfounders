<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Sent when access is withdrawn, so it is never a silent lockout. */
class AccountSuspendedMail extends Mailable
{
    use SerializesModels;

    public function __construct(public User $user, public ?string $note = null) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your MPower Founders account access has changed');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.clients.account-suspended');
    }
}
