<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_ai_generated'];

    protected $casts = [
        'is_ai_generated' => 'boolean',
    ];

    public function titles()
    {
        return $this->hasMany(Title::class);
    }

    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('xp', 'current_title_id')->withTimestamps();
    }
}