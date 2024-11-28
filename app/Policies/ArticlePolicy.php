<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function viewAny(User $user)
    {
        return $user->isSubscriber() || $user->isPublisher() || $user->isAdmin();
    }

    public function view(User $user)
    {
        return $user->isSubscriber() || $user->isPublisher() || $user->isAdmin();
    }

    public function create(User $user)
    {
        return $user->isPublisher() || $user->isAdmin();
    }

    public function update(User $user, Article $article)
    {
        return $user->isPublisher() || $user->isAdmin();
    }

    public function delete(User $user, Article $article)
    {
        return $user->isAdmin();
    }
}
