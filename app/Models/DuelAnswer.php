<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DuelAnswer extends Model
{
    protected $fillable = [
        'duel_id', 'user_id', 'question_id', 'is_correct', 'time_ms'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function duel()
    {
        return $this->belongsTo(Duel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}