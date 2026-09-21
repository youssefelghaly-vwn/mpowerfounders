<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The welcome email. Goes out when an admin activates an account that
 * already has a password (i.e. the user registered themselves). Accounts
 * created by us instead get ClientInvitationMail, which carries a
 * password-set link.
 */
class AccountActivatedMail extends Mailable
{
    use SerializesModels;

    public function __construct(public User $user, public ?string $note = null) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your MPower Founders account is live');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.clients.account-activated',
            with: ['portalUrl' => route('portal.dashboard')],
        );
    }
}
