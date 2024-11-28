<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Comment extends Model
{
    use HasFactory , LogsActivity;

    protected $fillable = ['article_id', 'user_id', 'content'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['article_id', 'user_id', 'content'])
            ->useLogName('comment')
            ->logOnlyDirty();
    }

    public function tapActivity(Activity $activity)
    {
        if ($activity->event === 'created') {
            $activity->description = "Comment by user ID $this->user_id added to article ID $this->article_id";
        } elseif ($activity->event === 'updated') {
            $activity->description = "Comment by user ID $this->user_id updated";
        } elseif ($activity->event === 'deleted') {
            $activity->description = "Comment by user ID $this->user_id deleted.";
        }
    }

}
