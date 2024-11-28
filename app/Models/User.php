<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_SUBSCRIBER = 'subscriber';
    const ROLE_PUBLISHER = 'publisher';
    const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];
    protected $hidden = ['password'];

    public function isSubscriber()
    {
        return $this->role === self::ROLE_SUBSCRIBER;
    }

    public function isPublisher()
    {
        return $this->role === self::ROLE_PUBLISHER;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
