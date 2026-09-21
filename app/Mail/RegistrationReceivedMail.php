<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to the applicant the moment they register. Registration does not
 * grant access here — this sets the expectation that a human reviews the
 * account before they can upload anything.
 */
class RegistrationReceivedMail extends Mailable
{
    use SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'We received your MPower Founders application');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.auth.registration-received');
    }
}
