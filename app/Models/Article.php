<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Article extends Model
{

    use HasFactory, LogsActivity;

    protected $fillable = [
        'magazine_id',
        'title',
        'content',
        'publish_date'
    ];

    public function magazine()
    {
        return $this->belongsTo(Magazine::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['magazine_id', 'title', 'content', 'publish_date'])
            ->useLogName('article')
            ->logOnlyDirty();
    }

    public function tapActivity(Activity $activity)
    {
        if ($activity->event === 'created') {
            $activity->description = "Article $this->title created ";
        } elseif ($activity->event === 'updated') {
            $activity->description = "Article $this->title updated.";
        } elseif ($activity->event === 'deleted') {
            $activity->description = "Article $this->title deleted.";
        }
    }
}
