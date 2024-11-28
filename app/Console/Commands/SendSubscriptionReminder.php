<?php

namespace App\Console\Commands;

use App\Services\SubscriptionReminderService;
use Illuminate\Console\Command;

class SendSubscriptionReminder extends Command
{
    protected $signature = 'subscriptions:send-reminders';
    protected $description = 'Send email reminders to users before their subscriptions expire.';
    protected $reminderService;

    public function __construct(SubscriptionReminderService $reminderService)
    {
        parent::__construct();
        $this->reminderService = $reminderService;
    }

    public function handle()
    {
        $subscriptions = $this->reminderService->getSubscriptionsToRemind();

        if ($subscriptions->isEmpty()) {
            $this->info('No subscriptions need reminders.');
            return;
        }

        foreach ($subscriptions as $subscription) {
            $this->reminderService->sendReminder($subscription);
            $this->info("Reminder Job dispatched for user: {$subscription->user->email}");
        }
    }
}
