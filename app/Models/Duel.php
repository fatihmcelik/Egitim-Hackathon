<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Duel extends Model
{
    protected $fillable = [
        'topic_id', 'challenger_id', 'opponent_id', 
        'is_ghost', 'status', 'winner_id'
    ];

    protected $casts = [
        'is_ghost' => 'boolean',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function challenger()
    {
        return $this->belongsTo(User::class, 'challenger_id');
    }

    public function opponent()
    {
        return $this->belongsTo(User::class, 'opponent_id');
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function answers()
    {
        return $this->hasMany(DuelAnswer::class);
    }
}