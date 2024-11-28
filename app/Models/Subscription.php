<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Subscription extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'magazine_id',
        "type",
        'start_date',
        'end_date',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function magazine()
    {
        return $this->belongsTo(Magazine::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'magazine_id', 'type', 'start_date', 'end_date', 'status'])
            ->useLogName('subscription')
            ->logOnlyDirty();
    }

    public function tapActivity(Activity $activity)
    {
        if ($activity->event === 'created') {
            $activity->description = "Subscription for user ID $this->user_id created for magazine ID $this->magazine_id";
        } elseif ($activity->event === 'updated') {
            $activity->description = "Subscription for user ID $this->user_id updated.";
        } elseif ($activity->event === 'deleted') {
            $activity->description = "Subscription for user ID $this->user_id deleted.";
        }
    }
}
