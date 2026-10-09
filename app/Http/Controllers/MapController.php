<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    // Tamamlanan bölge bu kadar gün tekrar edilmezse sislenir
    private const FOG_AFTER_DAYS = 3;

    public function index(Request $request)
    {
        $user  = $request->user();
        $topic = $this->currentTopic($request);

        if (! $topic) {
            return redirect()->route('topics.index');
        }

        // Hangi konunun açık olduğunu hatırla (haritadan çıkıp dönünce aynı konu gelsin)
        session(['current_topic_id' => $topic->id]);

        // Haritada kaç ada varsa o kadar bölge gösterilir
        $slotCount = max(1, count(config('map.slots', [])));

        $progressRows = DB::table('region_user')
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('region_id');

        $regions = $topic->regions()
            ->orderBy('order')
            ->take($slotCount)
            ->get()
            ->map(function ($region) use ($progressRows) {
                $pivot = $progressRows->get($region->id);

                $region->days_left = null;

                if (! $pivot) {
                    $region->user_status = 'locked';
                    return $region;
                }

                $region->user_status = $pivot->status;

                // Tamamlanmış ve uzun süredir tekrar edilmemişse SİSLİ
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

        // Genel ilerleme: tamamlanan (sisli olanlar dahil) bölgelerin oranı
        $done = $regions->filter(fn ($r) => in_array($r->user_status, ['completed', 'fogged'], true))->count();
        $progress = $regions->count() > 0 ? (int) round($done / $regions->count() * 100) : 0;

        return view('map.index', compact('topic', 'regions', 'progress'));
    }

    /**
     * Gösterilecek konu: ?topic=ID, yoksa oturumdaki son konu, yoksa en yeni konu.
     */
    private function currentTopic(Request $request)
    {
        $user = $request->user();

        $id = $request->query('topic') ?? session('current_topic_id');

        if ($id) {
            $topic = $user->topics()->where('topics.id', $id)->first();

            if ($topic) {
                return $topic;
            }
        }

        return $user->topics()->orderByDesc('topics.id')->first();
    }
}