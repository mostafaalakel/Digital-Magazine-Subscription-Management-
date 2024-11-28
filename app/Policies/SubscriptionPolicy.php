<?php

namespace App\Policies;

use App\Models\User;

class SubscriptionPolicy
{
    public function create(User $user)
    {
        return $user->isSubscriber() || $user->isAdmin();
    }

    public function manage(User $user)
    {
        return $user->isAdmin();
    }

}
