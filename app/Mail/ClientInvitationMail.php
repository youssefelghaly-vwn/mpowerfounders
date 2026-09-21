<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

/**
 * Welcome email for a client we created ourselves. They never chose a
 * password, so this carries a password-set link built from a real
 * password-reset token — same broker, same expiry, no second token table.
 */
class ClientInvitationMail extends Mailable
{
    use SerializesModels;

    public string $setPasswordUrl;

    public int $expiryMinutes;

    public function __construct(public User $user, string $token, public ?string $note = null)
    {
        $this->setPasswordUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));

        $guard = Config::get('auth.defaults.passwords');
        $this->expiryMinutes = (int) Config::get("auth.passwords.{$guard}.expire", 60);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to MPower Founders — set your password');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.clients.invitation');
    }
}
