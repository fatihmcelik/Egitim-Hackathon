<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected string $apiKey;
    protected string $model;
    protected string $fallbackModel;

    public function __construct()
    {
        $this->apiKey        = (string) config('services.gemini.key');
        $this->model         = (string) config('services.gemini.model');
        $this->fallbackModel = (string) config('services.gemini.fallback_model');
    }

    protected function endpoint(string $model): string
    {
        return "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
    }

    /**
     * Gemini'ye istek atar.
     * - 500/502/503/504 (geçici hata): bekleyip tekrar dener.
     * - 429 (günlük kota dolu), 404, 400, 403: tekrar denemez, yedek modele geçer.
     * Başarılıysa yanıt metnini, değilse null döner.
     */
    protected function askGemini(string $prompt): ?string
    {
        if ($this->apiKey === '' || $this->model === '') {
            Log::error('Gemini ayarı eksik: GEMINI_API_KEY veya GEMINI_MODEL .env içinde tanımlı değil.');
            return null;
        }

        $thinking = (string) config('services.gemini.thinking');
        $models   = array_values(array_filter(array_unique([$this->model, $this->fallbackModel])));

        foreach ($models as $model) {
            $useThinking = $thinking !== '';

            for ($attempt = 1; $attempt <= 3; $attempt++) {
                try {
                    $request = Http::timeout(90)->withHeaders([
                        'Content-Type'   => 'application/json',
                        'x-goog-api-key' => $this->apiKey,
                    ]);

                    // Yerel SSL sertifika hatası için; sadece local ortamda doğrulamayı kapat.
                    if (app()->isLocal()) {
                        $request = $request->withoutVerifying();
                    }

                    $generationConfig = ['responseMimeType' => 'application/json'];

                    if ($useThinking) {
                        $generationConfig['thinkingConfig'] = ['thinkingLevel' => $thinking];
                    }

                    $response = $request->post($this->endpoint($model), [
                        'contents'         => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => $generationConfig,
                    ]);

                    if ($response->successful()) {
                        $text = $response->json('candidates.0.content.parts.0.text');

                        if (is_string($text) && $text !== '') {
                            return $text;
                        }

                        Log::error("Gemini boş içerik döndürdü ({$model}): " . $response->body());
                        break; // bu modeli bırak, diğerine geç
                    }

                    $status = $response->status();
                    Log::error("Gemini API Hatası ({$model}, deneme {$attempt}): " . $response->body());

                    // Düşünme ayarı bu modelde geçersizse, ayarsız aynı denemeyi tekrarla
                    if ($status === 400 && $useThinking && str_contains(strtolower($response->body()), 'think')) {
                        $useThinking = false;
                        $attempt--;
                        continue;
                    }

                    // Sadece geçici sunucu hatalarında tekrar dene.
                    // 429 günlük kota dolmuş demektir; beklemek boşuna, sonraki modele geç.
                    if (in_array($status, [500, 502, 503, 504], true) && $attempt < 3) {
                        sleep(2 * $attempt);
                        continue;
                    }

                    break; // kalıcı hata veya denemeler bitti: sonraki modele geç
                } catch (\Throwable $e) {
                    Log::error("Gemini İstek Hatası ({$model}, deneme {$attempt}): " . $e->getMessage());

                    if ($attempt < 3) {
                        sleep(2 * $attempt);
                        continue;
                    }

                    break;
                }
            }
        }

        return null;
    }

    /**
     * Gemini'nin metnini diziye çevirir.
     * Markdown sarmalını temizler; geçersiz kaçış dizileri (ör. \$) varsa onarır.
     */
    protected function decodeJson(string $content): ?array
    {
        $clean = trim(preg_replace('/```json|```/i', '', $content));
        $data  = json_decode($clean, true);

        if (! is_array($data)) {
            $valid = ['"', '\\', '/', 'b', 'f', 'n', 'r', 't', 'u'];

            // Geçerli kaçışlara dokunma, geçersizlerde ters eğik çizgiyi ikile.
            $fixed = preg_replace_callback('/\\\\(.)/s', function ($m) use ($valid) {
                return in_array($m[1], $valid, true) ? $m[0] : '\\\\' . $m[1];
            }, $clean);

            $data = json_decode($fixed, true);
        }

        if (! is_array($data)) {
            Log::error('Gemini JSON çözümlenemedi: ' . $clean);
            return null;
        }

        return $data;
    }

    // 1. HARİTA ÜRETME FONKSİYONU
    public function generateTopicMap(string $promptText): ?array
    {
        $prompt = "Sen uzman bir yazılım eğitmenisin. Öğrenci şu konuyu öğrenmek istiyor: '{$promptText}'.
        Bu konu için 5 aşamalı (kolaydan zora doğru) bir oyunlaştırılmış öğrenme haritası oluştur.
        Tüm metinler Türkçe olsun. x ve y değerleri 0 ile 100 arasında birer tam sayı olsun
        (haritadaki konumun yüzdesi); bölgeler birbirinden uzak ve sırayla ilerleyen bir yol oluştursun.
        Çıktı KESİNLİKLE aşağıdaki JSON formatında olmalı, hiçbir ekstra metin içermemeli:
        {
            \"topic_name\": \"Konunun havalı adı\",
            \"description\": \"Konunun maceracı kısa açıklaması\",
            \"regions\": [
                {
                    \"name\": \"1. Bölgenin Adı (örn: Syntax Ormanı)\",
                    \"description\": \"Bölgenin hikayeli açıklaması\",
                    \"icon\": \"⚔️\",
                    \"x\": 20,
                    \"y\": 80
                }
            ]
        }";

        $content = $this->askGemini($prompt);

        if ($content === null) {
            return null;
        }

        $data = $this->decodeJson($content);

        if (! $data || empty($data['regions']) || ! is_array($data['regions'])) {
            Log::error('Gemini harita yapısı beklenen formatta değil.');
            return null;
        }

        return $data;
    }

    // 2. BÖLGE İÇERİĞİ (KARTLAR, QUİZ, KOD GÖREVİ) ÜRETME FONKSİYONU
    public function generateRegionContent(string $topicName, string $regionName, int $step = 1, int $totalSteps = 5): ?array
    {
        $level = $step <= 1 ? 'başlangıç' : ($step >= $totalSteps ? 'ileri' : 'orta');

        $template = <<<'PROMPT'
Sen deneyimli bir eğitmensin. Konu: "{KONU}". Bölüm: "{BOLUM}" (haritanın {ADIM}. adımı, toplam {TOPLAM} adım; seviye: {SEVIYE}).
Bu bölüm için öğrenciye şunları hazırla. Tüm metinler Türkçe olsun, bilgiler doğru olsun:

1. cards: 6 bilgi kartı. Her kartın title alanı kısa, desc alanı 2-4 cümle olsun. Önemli terimleri ve kısa kod parçalarını ters tırnak (`) içine al.
2. quizzes: 8 çoktan seçmeli soru, kolaydan zora. Her sorunun 4 şıkkı olsun, doğru cevap kesin ve tartışmasız olsun.
   - correct: doğru şıkkın 0'dan başlayan sıra numarası.
   - hint: cevabı söylemeyen, doğru yöne iten kısa bir ipucu.
   - explanation: doğru cevabın neden doğru olduğunu anlatan 1-2 cümle.
3. code_task: öğrenciye bir FONKSİYON yazdıran kodlama görevi.
   - Konu programlama veya yazılımla ilgili değilse code_task değerini null yap.
   - Görev, bölümün konusuna uygun ve seviyeye uygun zorlukta olsun. Çok basit "değişken tanımla" tarzı görevlerden kaçın; ilk bölümde bile küçük bir mantık (koşul, döngü, metin veya liste işlemi) gerektirsin.
   - function: İngilizce camelCase fonksiyon adı.
   - instructions: görevin ne yapması gerektiğini ve örnek bir girdi/çıktıyı Türkçe anlat.
   - cases: 4 veya 5 test. Her testte args (fonksiyona verilecek argümanlar dizisi) ve expected (beklenen sonuç) olsun. Değerler yalnızca JSON'daki sayı, metin, true/false veya dizi olsun. Uç durumları da ekle.
   - starter: javascript ve python için fonksiyon iskeleti (gövdesi boş, yorum satırıyla yönlendirmeli).
   - hint: görev için kısa ipucu.

Kurallar: Kod ve metinlerde ters eğik çizgi gerektiren ifadeler (düzenli ifade gibi) kullanma. Çıktı yalnızca aşağıdaki biçimde geçerli JSON olsun; fazladan metin veya markdown ekleme. Örnekteki içerik sadece biçim içindir, kopyalama:
{
  "cards": [ { "title": "Kısa başlık", "desc": "Açıklama" } ],
  "quizzes": [ { "q": "Soru", "options": ["A", "B", "C", "D"], "correct": 2, "hint": "İpucu", "explanation": "Açıklama" } ],
  "code_task": {
    "function": "ciftMi",
    "instructions": "Verilen sayı çiftse true, değilse false döndüren ciftMi fonksiyonunu yaz. Örnek: ciftMi(4) true döner.",
    "cases": [ { "args": [4], "expected": true }, { "args": [7], "expected": false } ],
    "starter": { "javascript": "function ciftMi(n) {\n  // kodunu buraya yaz\n}", "python": "def ciftMi(n):\n    # kodunu buraya yaz\n    pass" },
    "hint": "Kalan işlemini düşün."
  }
}
PROMPT;

        $prompt = str_replace(
            ['{KONU}', '{BOLUM}', '{ADIM}', '{TOPLAM}', '{SEVIYE}'],
            [$topicName, $regionName, (string) $step, (string) $totalSteps, $level],
            $template
        );

        $content = $this->askGemini($prompt);

        if ($content === null) {
            return null;
        }

        $data = $this->decodeJson($content);

        if (! $data) {
            return null;
        }

        $clean = $this->sanitizeRegionContent($data);

        if (! $clean) {
            Log::error('Gemini bölge içeriği beklenen formatta değil.');
            return null;
        }

        return $clean;
    }

    /**
    
     * şıkları karıştırır (doğru cevap hep aynı yerde olmasın).
     */
    protected function sanitizeRegionContent(array $data): ?array
    {
        $cards = [];
        foreach ((array) ($data['cards'] ?? []) as $c) {
            if (is_array($c) && ! empty($c['title']) && ! empty($c['desc'])) {
                $cards[] = ['title' => (string) $c['title'], 'desc' => (string) $c['desc']];
            }
        }

        $quizzes = [];
        foreach ((array) ($data['quizzes'] ?? []) as $q) {
            if (! is_array($q) || empty($q['q']) || ! isset($q['options']) || ! is_array($q['options'])) {
                continue;
            }

            $raw = array_values($q['options']);
            $options = array_values(array_filter($raw, 'is_scalar'));
            $correct = $q['correct'] ?? null;

            if (
                count($options) !== count($raw) || count($options) < 2
                || ! is_numeric($correct) || (int) $correct < 0 || (int) $correct >= count($options)
            ) {
                continue;
            }

            $pairs = [];
            foreach ($options as $i => $opt) {
                $pairs[] = ['t' => (string) $opt, 'c' => $i === (int) $correct];
            }
            shuffle($pairs);

            $texts = [];
            $newCorrect = 0;
            foreach ($pairs as $i => $p) {
                $texts[] = $p['t'];
                if ($p['c']) {
                    $newCorrect = $i;
                }
            }

            $quizzes[] = [
                'q'           => (string) $q['q'],
                'options'     => $texts,
                'correct'     => $newCorrect,
                'hint'        => (string) ($q['hint'] ?? ''),
                'explanation' => (string) ($q['explanation'] ?? ''),
            ];
        }

        if (count($cards) < 1 || count($quizzes) < 1) {
            return null;
        }

        return [
            'cards'     => $cards,
            'quizzes'   => $quizzes,
            'code_task' => $this->sanitizeCodeTask($data['code_task'] ?? null),
        ];
    }

    protected function sanitizeCodeTask($task): ?array
    {
        if (! is_array($task)) {
            return null;
        }

        $fn = $task['function'] ?? '';
        if (! is_string($fn) || ! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $fn) || empty($task['instructions'])) {
            return null;
        }

        $cases = [];
        foreach ((array) ($task['cases'] ?? []) as $c) {
            if (is_array($c) && isset($c['args']) && is_array($c['args']) && array_key_exists('expected', $c)) {
                $cases[] = ['args' => array_values($c['args']), 'expected' => $c['expected']];
            }
        }

        if (count($cases) < 2) {
            return null;
        }

        $starterRaw = is_array($task['starter'] ?? null) ? $task['starter'] : [];
        $starter = [];
        foreach (['javascript', 'python'] as $lang) {
            $code = $starterRaw[$lang] ?? null;
            if (is_string($code) && trim($code) !== '') {
                $starter[$lang] = $code;
            }
        }

        return [
            'instructions' => (string) $task['instructions'],
            'function'     => $fn,
            'cases'        => $cases,
            'starter'      => $starter,
            'hint'         => (string) ($task['hint'] ?? ''),
        ];
    }
}
