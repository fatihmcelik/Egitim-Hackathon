<?php

namespace App\Http\Controllers;

use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    private const FOG_AFTER_DAYS = 3;
    private const CLASS_LEVEL_XP = 500;

    public function index(Request $request)
    {
        $user   = $request->user();
        $topics = $user->topics()->get();

        $totalXp = (int) $topics->sum(fn ($t) => (int) $t->pivot->xp);

        // Aktif konu (harita sayfasıyla aynı mantık)
        $topic = null;
        if ($id = session('current_topic_id')) {
            $topic = $topics->firstWhere('id', $id);
        }
        $topic = $topic ?? $topics->sortByDesc('id')->first();

        $regions       = collect();
        $progress      = 0;
        $nextRegion    = null;
        $fogRisks      = collect();
        $title         = null;
        $nextTitle     = null;
        $titleProgress = 0;
        $topicXp       = 0;

        if ($topic) {
            $topicXp = (int) $topic->pivot->xp;

            $title     = Title::where('topic_id', $topic->id)->where('min_xp', '<=', $topicXp)->orderByDesc('min_xp')->first();
            $nextTitle = Title::where('topic_id', $topic->id)->where('min_xp', '>', $topicXp)->orderBy('min_xp')->first();

            if ($nextTitle) {
                $base  = (int) ($title->min_xp ?? 0);
                $span  = max(1, $nextTitle->min_xp - $base);
                $titleProgress = (int) min(100, round(($topicXp - $base) / $span * 100));
            } else {
                $titleProgress = 100;
            }

            // Bölge durumları (MapController ile aynı kural)
            $slotCount = max(1, count(config('map.slots', range(1, 5))));
            $rows = DB::table('region_user')->where('user_id', $user->id)->get()->keyBy('region_id');

            $regions = $topic->regions()->orderBy('order')->take($slotCount)->get()->map(function ($region) use ($rows) {
                $pivot = $rows->get($region->id);
                $region->days_left = null;

                if (! $pivot) {
                    $region->user_status = 'locked';
                    return $region;
                }

                $region->user_status = $pivot->status;

                if ($pivot->status === 'completed' && $pivot->last_reviewed_at) {
                    $elapsed = (int) floor(abs(Carbon::parse($pivot->last_reviewed_at)->diffInDays(now())));

                    if ($elapsed >= self::FOG_AFTER_DAYS) {
                        $region->user_status = 'fogged';
                    } else {
                        $region->days_left = self::FOG_AFTER_DAYS - $elapsed;
                    }
                }

                return $region;
            });

            $done = $regions->filter(fn ($r) => in_array($r->user_status, ['completed', 'fogged'], true))->count();
            $progress = $regions->count() > 0 ? (int) round($done / $regions->count() * 100) : 0;

            $nextRegion = $regions->firstWhere('user_status', 'open') ?? $regions->firstWhere('user_status', 'fogged');

            $fogRisks = $regions->filter(fn ($r) =>
                $r->user_status === 'fogged'
                || ($r->user_status === 'completed' && $r->days_left !== null && $r->days_left <= 1)
            )->values();
        }

        // Yetenek barları (aynı isimliler toplanır)
        $skills = $user->skills()->get()->groupBy('name')->map(fn ($g) => [
            'name'  => $g->first()->name,
            'icon'  => $g->first()->icon,
            'score' => min(1000, (int) $g->sum(fn ($s) => (int) $s->pivot->score)),
        ])->values();

        if ($skills->isEmpty()) {
            $skills = collect([
                ['name' => 'Konu Bilgisi',  'icon' => '📚', 'score' => 0],
                ['name' => 'Problem Çözme', 'icon' => '🧩', 'score' => 0],
                ['name' => 'Hafıza',        'icon' => '🧠', 'score' => 0],
            ]);
        }

        // Sınıf: sıralama ve sınıf seviyesi
        $classroom = $user->classroom;
        $board = collect();
        $classXp = 0;
        $classLevel = 1;
        $classProgress = 0;

        if ($classroom) {
            $board = DB::table('users')
                ->leftJoin('topic_user', 'topic_user.user_id', '=', 'users.id')
                ->where('users.classroom_id', $classroom->id)
                ->where('users.role', 'student')
                ->groupBy('users.id', 'users.name')
                ->select('users.id', 'users.name', DB::raw('COALESCE(SUM(topic_user.xp), 0) as total_xp'))
                ->orderByDesc('total_xp')
                ->limit(5)
                ->get();

            $classXp = (int) DB::table('topic_user')
                ->join('users', 'users.id', '=', 'topic_user.user_id')
                ->where('users.classroom_id', $classroom->id)
                ->sum('topic_user.xp');

            $classLevel    = intdiv($classXp, self::CLASS_LEVEL_XP) + 1;
            $classProgress = (int) round(($classXp % self::CLASS_LEVEL_XP) / self::CLASS_LEVEL_XP * 100);
        }

        return view('dashboard', compact(
            'user', 'topic', 'totalXp', 'topicXp', 'title', 'nextTitle', 'titleProgress',
            'regions', 'progress', 'nextRegion', 'fogRisks', 'skills',
            'classroom', 'board', 'classXp', 'classLevel', 'classProgress'
        ));
    }
}