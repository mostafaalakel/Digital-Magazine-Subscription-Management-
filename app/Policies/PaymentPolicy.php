<?php

namespace App\Policies;

use App\Models\User;

class PaymentPolicy
{
    public function create(User $user)
    {
        return $user->isSubscriber();
    }

    public function view(User $user)
    {
        return $user->isSubscriber();
    }

    public function manage(User $user)
    {
        return $user->isAdmin();
    }

}
