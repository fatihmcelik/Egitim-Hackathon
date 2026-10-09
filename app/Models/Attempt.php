<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    protected $fillable = [
        'user_id', 'region_id', 'attemptable_id', 'attemptable_type', 
        'score', 'passed'
    ];

    protected $casts = [
        'passed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function attemptable()
    {
        // Bu denemenin bir Question'a mı yoksa CodingTask'e mi ait olduğunu otomatik bulur
        return $this->morphTo(); 
    }
}