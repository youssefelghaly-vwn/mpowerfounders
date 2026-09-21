<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\ClientRepository;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Single door for every transactional email in the app.
 *
 * Delivery failures are logged, never thrown: a dead SMTP host must not
 * roll back the thing that triggered the email. Activating a client or
 * moving a project to the next stage has to stick whether or not the mail
 * server is reachable, and the log line is enough to resend by hand.
 */
class MailService
{
    public function __construct(private readonly ClientRepository $clients) {}

    /**
     * @param  User|string|array<int, User|string>  $recipients
     */
    public function send(User|string|array $recipients, Mailable $mailable): void
    {
        $addresses = collect(is_array($recipients) ? $recipients : [$recipients])
            ->map(fn ($recipient) => $recipient instanceof User ? $recipient->email : $recipient)
            ->filter()
            ->unique()
            ->values();

        if ($addresses->isEmpty()) {
            return;
        }

        try {
            Mail::to($addresses->all())->send($mailable);
        } catch (\Throwable $exception) {
            Log::error('Transactional email failed to send.', [
                'mailable' => $mailable::class,
                'recipients' => $addresses->all(),
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Internal alerts: everyone holding a non-client role, plus any extra
     * addresses configured in mail.admin_addresses.
     */
    public function sendToStaff(Mailable $mailable): void
    {
        $recipients = $this->clients->staff()
            ->pluck('email')
            ->merge(config('mail.admin_addresses', []))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $this->send($recipients, $mailable);
    }
}
