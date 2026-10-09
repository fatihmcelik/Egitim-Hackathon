<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CodingTask extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'region_id', 'title', 'description', 'starter_code', 
        'test_cases', 'hint', 'is_approved', 'is_ai_generated'
    ];

    protected $casts = [
        'test_cases' => 'array', // JSON formatlı test senaryolarını diziye çevirir
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