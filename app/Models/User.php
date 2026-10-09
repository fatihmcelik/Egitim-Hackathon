<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'classroom_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function topics()
    {
        return $this->belongsToMany(Topic::class)->withPivot('xp', 'current_title_id')->withTimestamps();
    }

    public function regions()
    {
        return $this->belongsToMany(Region::class)->withPivot('status', 'best_score', 'last_reviewed_at')->withTimestamps();
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class)->withPivot('score')->withTimestamps();
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }
}