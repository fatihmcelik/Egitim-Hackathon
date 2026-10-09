<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = [
        'topic_id',
        'name',
        'slug',
        'order',
        'x',
        'y',
        'icon',
        'description',
        'prerequisite_region_id',
        'is_ai_generated',
        'cards',
        'quizzes',
        'code_task'
    ];

    protected $casts = [
        'is_ai_generated' => 'boolean',
        'cards' => 'array',
        'quizzes' => 'array',
        'code_task' => 'array',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function prerequisite()
    {
        return $this->belongsTo(Region::class, 'prerequisite_region_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function codingTasks()
    {
        return $this->hasMany(CodingTask::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('status', 'best_score', 'last_reviewed_at')->withTimestamps();
    }
}
