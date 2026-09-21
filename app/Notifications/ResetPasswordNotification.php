<?php

namespace App\Notifications;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Notifications\Notification;

/**
 * Replaces Laravel's default password-reset notification so the email
 * uses our own branded template (see App\Mail\ResetPasswordMail and
 * resources/views/emails/reset-password.blade.php) instead of the
 * framework's plain markdown mail component.
 *
 * Wired in via User::sendPasswordResetNotification() — see the note in
 * ResetPasswordMail's sibling files for the one-line addition needed
 * there.
 */
class ResetPasswordNotification extends Notification
{
    public function __construct(private readonly string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): ResetPasswordMail
    {
        /** @var User $notifiable */
        return new ResetPasswordMail($notifiable, $this->token);
    }
}