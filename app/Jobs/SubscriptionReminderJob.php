<?php

namespace App\Jobs;

use App\Mail\SubscriptionReminderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SubscriptionReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $subscription;


    public function __construct($user, $subscription)
    {
        $this->user = $user;
        $this->subscription = $subscription;
    }


    public function handle()
    {
        Mail::to($this->user->email)->send(new SubscriptionReminderMail($this->user, $this->subscription));
    }

}
