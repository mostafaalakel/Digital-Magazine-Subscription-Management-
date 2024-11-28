<?php

namespace App\Policies;

use App\Models\User;

class CommentPolicy
{
    public function create(User $user)
    {
        return $user->isSubscriber();
    }

    public function manage(User $user){
        return $user->isSubscriber() || $user->isAdmin();
    }
}
