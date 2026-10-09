<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Topic;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TopicController extends Controller
{
    /**
     * Hazır (seeder) konuya yönlendiren takma adlar.
     * Not: seeder'daki Yazılım konusunun slug'ı "software" değilse sağ taraftaki değeri değiştir.
     */
    private const ALIASES = [
        'yazilim'     => 'software',
        'yazilim-ogren' => 'software',
        'kodlama'     => 'software',
        'programlama' => 'software',
        'software'    => 'software',
    ];

    public function index()
    {
        $topics = Topic::all();
        return view('topics.index', compact('topics'));
    }

    public function store(Request $request)
    {
        // Gemini yanıtı PHP'nin varsayılan 30 sn sınırını aşabilir
        set_time_limit(180);

        $request->validate(['prompt' => 'required|string|max:100']);

        $query = trim($request->input('prompt'));
        $slug  = Str::slug($query, '-', 'tr');

        if ($slug === '') {
            return back()->with('error', 'Lütfen geçerli bir konu adı yaz.');
        }

        // Hazır konuya takma ad varsa onu kullan (ör. "Yazılım" -> software)
        $slug = self::ALIASES[$slug] ?? $slug;

        // 1. Konu zaten var mı? (slug veya ad ile)
        $existingTopic = Topic::where('slug', $slug)->orWhere('name', $query)->first();

        if ($existingTopic) {
            $this->enrollUser($request->user(), $existingTopic);
            return redirect()->route('map.index')->with('success', 'Mevcut bir haritaya katıldın!');
        }

        // 2. Yoksa Gemini ile üret
        $data = app(AiService::class)->generateTopicMap($query);

        if (! $data) {
            return back()->with('error', 'Yapay zekâ haritayı oluşturamadı, lütfen biraz sonra tekrar dene.');
        }

        try {
            $topic = DB::transaction(function () use ($data, $slug, $query) {
                $newTopic = Topic::create([
                    'name'            => $data['topic_name'] ?? $query,
                    'slug'            => $slug,
                    'description'     => $data['description'] ?? null,
                    'is_ai_generated' => true,
                ]);

                $previousId = null;

                // En fazla 8 bölge al
                $regions = array_slice(array_values($data['regions']), 0, 8);

                foreach ($regions as $i => $r) {
                    $region = Region::create([
                        'topic_id'               => $newTopic->id,
                        'name'                   => $r['name'] ?? ('Bölge ' . ($i + 1)),
                        'slug'                   => $slug . '-' . ($i + 1),
                        'order'                  => $i + 1,
                        // Haritadan taşmasın: 5 ile 95 arasına sıkıştır
                        'x'                      => max(5, min(95, (int) ($r['x'] ?? 50))),
                        'y'                      => max(5, min(95, (int) ($r['y'] ?? 50))),
                        'icon'                   => $r['icon'] ?? '📍',
                        'description'            => $r['description'] ?? null,
                        'prerequisite_region_id' => $previousId,
                        'is_ai_generated'        => true,
                    ]);

                    $previousId = $region->id;
                }

                return $newTopic;
            });
        } catch (\Throwable $e) {
            Log::error('Kayıt Hatası: ' . $e->getMessage());

            // Aynı anda başka bir istek konuyu oluşturmuş olabilir
            $topic = Topic::where('slug', $slug)->first();

            if (! $topic) {
                return back()->with('error', 'Veritabanına kaydedilirken bir sorun oluştu.');
            }
        }

        // 3. Kullanıcıyı konuya bağla, ilk bölgeyi aç
        $this->enrollUser($request->user(), $topic);

        return redirect()->route('map.index')->with('success', 'Haritan başarıyla çizildi!');
    }

    // Konu listesindeki hazır "Maceraya Başla" butonları için
    public function enroll(Request $request, Topic $topic)
    {
        $this->enrollUser($request->user(), $topic);

        return redirect()->route('map.index')->with('success', 'Maceraya başladın!');
    }

    /**
     * Kullanıcıyı konuya bağlar ve (daha önce kaydı yoksa) ilk bölgeyi açar.
     * Mevcut ilerlemeyi (tamamlanmış bölge vb.) asla sıfırlamaz.
     */
    private function enrollUser($user, Topic $topic): void
    {
        $user->topics()->syncWithoutDetaching([$topic->id]);

        $firstRegion = $topic->regions()->orderBy('order')->first();

        if (! $firstRegion) {
            return;
        }

        $hasRow = $user->regions()->where('regions.id', $firstRegion->id)->exists();

        if (! $hasRow) {
            $user->regions()->attach($firstRegion->id, ['status' => 'open']);
        }
    }
}