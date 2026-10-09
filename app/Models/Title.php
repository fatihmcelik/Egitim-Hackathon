<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    protected $fillable = ['topic_id', 'level', 'name', 'icon', 'min_xp'];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}