<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Magazine extends Model
{
    use HasFactory ,LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'monthly_price',
        'yearly_price',
        'release_date'
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'monthly_price', 'yearly_price', 'release_date'])
            ->useLogName('magazine')
            ->logOnlyDirty();
    }

    public function tapActivity(Activity $activity)
    {
        if ($activity->event === 'created') {
            $activity->description = "Magazine $this->name created";
        } elseif ($activity->event === 'updated') {
            $activity->description = "Magazine $this->name updated.";
        } elseif ($activity->event === 'deleted') {
            $activity->description = "Magazine $this->name deleted.";
        }
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
