<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subscription;

    public function __construct($user, $subscription)
    {
        $this->user = $user;
        $this->subscription = $subscription;
    }

    public function build()
    {
        $subject = 'Reminder: Your subscription is about to expire!';

        return $this->subject($subject)
            ->view('emails.subscription_reminder')
            ->with([
                'user' => $this->user,
                'subscription' => $this->subscription,
            ]);
    }
}
