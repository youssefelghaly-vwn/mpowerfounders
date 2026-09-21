<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class ResetPasswordMail extends Mailable
{
    use SerializesModels;

    public string $resetUrl;

    public int $expiryMinutes;

    public function __construct(public User $user, string $token)
    {
        // Same URL shape Laravel's own ResetPassword notification builds —
        // named route + relative URI, resolved to an absolute URL.
        $this->resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));

        $guard = Config::get('auth.defaults.passwords');
        $this->expiryMinutes = (int) Config::get("auth.passwords.{$guard}.expire", 60);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            // Set here rather than relying on the caller to chain ->to() —
            // Notification::toMail() hands this straight to $message->send(),
            // it never calls ->to() itself, so without this the mailer has
            // no recipient at all.
            to: [$this->user->email],
            subject: 'Reset your MPower Founders password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.reset-password',
            with: [
                'user' => $this->user,
                'resetUrl' => $this->resetUrl,
                'expiryMinutes' => $this->expiryMinutes,
            ],
        );
    }
}