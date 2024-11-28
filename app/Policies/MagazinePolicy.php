<?php

namespace App\Policies;

use App\Models\Magazine;
use App\Models\User;

class MagazinePolicy
{


    public function create(User $user)
    {
        return $user->isPublisher() || $user->isAdmin();
    }

    public function delete(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user)
    {
        return $user->isPublisher() || $user->isAdmin();
    }
}

