<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        // Kullanıcıyı ve yeteneklerini çek
        $user = User::with('skills')->find(Auth::id());
        
        $mapXp = DB::table('topic_user')->where('user_id', $user->id)->sum('xp');
        $skillXp = DB::table('skill_user')->where('user_id', $user->id)->sum('score');
        $totalXp = $mapXp + $skillXp;
        
        // XP'ye göre Unvan Hesaplama
        $title = 'Siber Çırak';
        if ($totalXp >= 3000) $title = 'Siber Efsane';
        elseif ($totalXp >= 1500) $title = 'Yazılım Ustası';
        elseif ($totalXp >= 500) $title = 'Kod Şövalyesi';

        return view('profile.index', compact('user', 'totalXp', 'title'));
    }
}