<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'region_id', 'body', 'options', 'correct_index', 
        'explanation', 'difficulty', 'is_approved', 'is_ai_generated'
    ];

    protected $casts = [
        'options' => 'array', // Veritabanındaki JSON'u otomatik PHP dizisine çevirir
        'is_approved' => 'boolean',
        'is_ai_generated' => 'boolean',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function attempts()
    {
        return $this->morphMany(Attempt::class, 'attemptable');
    }
}