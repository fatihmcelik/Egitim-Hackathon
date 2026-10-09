<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudySession extends Model
{
    protected $fillable = [
        'requester_id', 'receiver_id', 'type', 'location_or_link', 'scheduled_at', 'status'
    ];
}