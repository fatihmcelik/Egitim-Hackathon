<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'region_id', 'order', 'title', 'body', 
        'video_id', 'video_start', 'video_end'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}