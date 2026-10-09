<?php

namespace App\Http\Controllers;

use App\Models\Duel;
use App\Models\Topic;
use App\Models\User;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DuelController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $topics = Topic::all(); 

        /* 
         * HACKATHON DEMO YETENEKLERİ (İPTAL EDİLDİ)
         * Eğer jüriye sunum yaparken hesabın tamamen "0" XP görünsün istiyorsan 
         * bu kod böyle kalabilir. Eğer dolu görünsün dersen aşağıdaki /* işaretlerini silebilirsin.
         */
        /*
        if ($user->skills()->count() === 0) {
            $demoSkills = [
                ['name' => 'Laravel Algoritmaları', 'icon' => '🐘', 'score' => rand(200, 800)],
                ['name' => 'Python Veri Yapıları', 'icon' => '🐍', 'score' => rand(100, 500)],
                ['name' => 'Hata Ayıklama (Debug)', 'icon' => '🐛', 'score' => rand(400, 700)],
            ];
            foreach ($demoSkills as $skillData) {
                $skill = Skill::firstOrCreate(['name' => $skillData['name']], ['topic_id' => $topics->first()->id ?? 1, 'icon' => $skillData['icon']]);
                $user->skills()->attach($skill->id, ['score' => $skillData['score']]);
            }
        }
        */

        $mySkills = $user->skills()->get();
        
        // HATA ÇÖZÜMÜ: total_xp olarak topla, sırala ve VALUES() ile anahtarları 0,1,2 diye yeniden diz (Sıra bozulmasın)
        $users = User::with(['skills', 'topics'])->get();
        $leaderboard = $users->map(function ($u) {
            $u->total_xp = $u->skills->sum('pivot.score') + $u->topics->sum('pivot.xp');
            return $u;
        })->sortByDesc('total_xp')->values()->take(5);

        return view('duel.index', compact('mySkills', 'leaderboard', 'topics'));
    }

    public function botMatch(Request $request)
    {
        $user = Auth::user();
        $opponent = User::where('id', '!=', $user->id)->inRandomOrder()->first();
        $topicId = $request->input('topic_id') ?? Topic::first()->id ?? 1; 

        $duel = Duel::create([
            'topic_id' => $topicId,
            'challenger_id' => $user->id,
            'opponent_id' => $opponent->id ?? $user->id,
            'is_ghost' => true,
            'status' => 'active',
        ]);

        return redirect()->route('arena.show', $duel->id);
    }

    public function randomMatch(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $topicId = $request->input('topic_id') ?? Topic::first()->id ?? 1;
        $myAverageScore = $user->skills()->avg('skill_user.score') ?? 500;

        $pendingDuel = Duel::where('topic_id', $topicId)
            ->where('status', 'pending')
            ->where('is_ghost', false)
            ->whereNull('opponent_id')
            ->where('challenger_id', '!=', $user->id)
            ->whereHas('challenger.skills', function($q) use ($myAverageScore) {
                $q->whereBetween('skill_user.score', [$myAverageScore - 300, $myAverageScore + 300]);
            })->first();

        if ($pendingDuel) {
            $pendingDuel->update(['opponent_id' => $user->id, 'status' => 'active']);
            return redirect()->route('arena.show', $pendingDuel->id);
        }

        $duel = Duel::create([
            'topic_id' => $topicId,
            'challenger_id' => $user->id,
            'opponent_id' => null,
            'is_ghost' => false,
            'status' => 'pending',
        ]);

        return redirect()->route('arena.show', ['duel' => $duel->id, 'mode' => 'random']);
    }

    public function createRoom(Request $request)
    {
        $topicId = $request->input('topic_id') ?? Topic::first()->id ?? 1;
        $duel = Duel::create([
            'topic_id' => $topicId,
            'challenger_id' => Auth::id(),
            'opponent_id' => null,
            'is_ghost' => false,   
            'status' => 'pending', 
        ]);
        return redirect()->route('arena.show', ['duel' => $duel->id, 'mode' => 'invite']);
    }

    public function joinRoom(Request $request)
    {
        $duel = Duel::find($request->input('room_id'));
        if (!$duel || $duel->status !== 'pending' || $duel->opponent_id !== null) {
            return back()->with('error', 'Oda kodu geçersiz veya dolu.');
        }
        if ($duel->challenger_id !== Auth::id()) {
            $duel->update(['opponent_id' => Auth::id(), 'status' => 'active']);
        }
        return redirect()->route('arena.show', $duel->id);
    }

    public function checkRoomStatus(Duel $duel)
    {
        return response()->json(['status' => $duel->status]);
    }

    public function show(Duel $duel)
    {
        if (Auth::id() !== $duel->challenger_id && Auth::id() !== $duel->opponent_id && $duel->opponent_id !== null) {
            abort(403);
        }

        $topic = Topic::with('regions.questions')->find($duel->topic_id);
        $questions = collect();
        if ($topic) {
            foreach ($topic->regions as $region) { $questions = $questions->merge($region->questions); }
        }
        
        if ($questions->isEmpty()) {
            $questions = collect([(object)['id' => 1, 'body' => 'Geliştiricilerin düellosu başlıyor! Hazır mısın?', 'options' => ['Evet', 'Kesinlikle', 'Tabii ki', 'Her Zaman'], 'correct_index' => 1]]);
        } else {
            $questions = $questions->shuffle()->take(3);
        }

        return view('duel.show', compact('duel', 'questions'));
    }

    public function submitAnswer(Request $request, Duel $duel)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isWin = $request->input('win', false);

        $topicUser = $user->topics()->where('topic_user.topic_id', $duel->topic_id)->first();
        if (!$topicUser) {
            $user->topics()->attach($duel->topic_id, ['xp' => 0, 'current_title_id' => 1]);
        }

        if ($isWin) {
            $duel->update(['status' => 'completed', 'winner_id' => $user->id]);
            
            $user->topics()->updateExistingPivot($duel->topic_id, ['xp' => DB::raw('xp + 50')]);
            $skill = $user->skills()->first();
            if ($skill) {
                $user->skills()->updateExistingPivot($skill->id, ['score' => DB::raw('score + 50')]);
            }
            return response()->json(['status' => 'success', 'xp_gained' => 50]);
        } else {
            $duel->update(['status' => 'completed', 'winner_id' => $duel->opponent_id ?? $duel->challenger_id]); 
            
            $currentXp = DB::table('topic_user')->where('user_id', $user->id)->where('topic_id', $duel->topic_id)->value('xp');
            if ($currentXp >= 20) {
                $user->topics()->updateExistingPivot($duel->topic_id, ['xp' => DB::raw('xp - 20')]);
            } else {
                $user->topics()->updateExistingPivot($duel->topic_id, ['xp' => 0]); 
            }
            return response()->json(['status' => 'success', 'xp_lost' => 20]);
        }
    }
}