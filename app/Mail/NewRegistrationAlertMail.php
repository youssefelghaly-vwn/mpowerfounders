<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Internal: a new account is sitting in the queue waiting to be activated. */
class NewRegistrationAlertMail extends Mailable
{
    use SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New account request: '.$this->user->name);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.new-registration',
            with: ['reviewUrl' => route('admin.clients.show', $this->user)],
        );
    }
}
