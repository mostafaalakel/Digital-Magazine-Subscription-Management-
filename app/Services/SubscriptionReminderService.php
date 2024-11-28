<?php

namespace App\Services;

use App\Jobs\SubscriptionReminderJob;
use App\Models\Subscription;

class SubscriptionReminderService
{
    public function getSubscriptionsToRemind()
    {
        return Subscription::where('end_date', now()->addDays(7))
            ->where('status', 'active')
            ->with('user')
            ->get();
    }

    public function sendReminder($subscription)
    {
        dispatch(new SubscriptionReminderJob($subscription->user, $subscription));
    }
}
