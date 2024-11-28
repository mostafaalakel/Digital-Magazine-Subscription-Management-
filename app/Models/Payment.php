<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'amount',
        'payment_method',
        'payment_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'subscription_id', 'amount', 'payment_method', 'payment_date'])
            ->useLogName('payment')
            ->logOnlyDirty();
    }

    public function tapActivity(Activity $activity)
    {
        if ($activity->event === 'created') {
            $activity->description = "Payment of $this->amount made by user ID $this->user_id using $this->payment_method";
        }
    }

}
