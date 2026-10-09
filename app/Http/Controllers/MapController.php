<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $topic = $user->topics()->latest()->first();

        if (!$topic) {
            return redirect()->route('topics.index');
        }

        $regions = $topic->regions()->orderBy('order')->get()->map(function ($region) use ($user) {
            $pivot = \Illuminate\Support\Facades\DB::table('region_user')
                ->where('user_id', $user->id)
                ->where('region_id', $region->id)
                ->first();

            if ($pivot) {
                // Eğer tamamlandıysa ve üzerinden 3 gün geçmişse SİSLİ yap
                if ($pivot->status === 'completed' && $pivot->last_reviewed_at < now()->subDays(3)) {
                    $region->user_status = 'fogged';
                } else {
                    $region->user_status = $pivot->status;
                }
            } else {
                $region->user_status = 'locked';
            }

            return $region;
        });

        return view('map.index', compact('topic', 'regions'));
    }
}
