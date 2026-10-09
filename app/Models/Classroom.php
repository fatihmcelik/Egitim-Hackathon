<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'teacher_id',
        'code',
    ];

    // Sınıfın sahibi olan öğretmen (User tablosuyla ilişki)
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Bu sınıfa kayıtlı olan öğrenciler
    public function students()
    {
        return $this->hasMany(User::class, 'classroom_id');
    }
}