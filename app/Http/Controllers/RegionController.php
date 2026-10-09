<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Services\AiService;
use App\Services\ProgressService;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    // Bölge ekranını gösterir; içerik hiçbir yerde yoksa yapay zekâdan üretir
    public function show(Region $region, AiService $aiService)
    {
        set_time_limit(180);

        $region->load('topic', 'lessons');
        $content = $this->contentFor($region);

        if (! $content) {
            $totalSteps = max(1, $region->topic->regions()->count());

            $generated = $aiService->generateRegionContent(
                $region->topic->name,
                $region->name,
                (int) $region->order,
                $totalSteps
            );

            if (! $generated) {
                return redirect()->route('map.index')
                    ->with('error', 'Yapay zekâ içeriği üretemedi, biraz sonra düğüme tekrar tıkla.');
            }

            $region->update([
                'cards'     => $generated['cards'],
                'quizzes'   => $generated['quizzes'],
                'code_task' => $generated['code_task'] ?? [],
            ]);

            $content = $this->contentFor($region);
        }

        return view('region.show', compact('region', 'content'));
    }

    // Bölüm bitince çağrılır: XP, unvan ve kilit açma ProgressService'te
    public function submitAnswer(Request $request, Region $region, ProgressService $progress)
    {
        $data = $request->validate([
            'correct' => 'required|integer|min:0|max:100',
            'total'   => 'required|integer|min:1|max:100',
        ]);

        // Soru sayısını mümkünse sunucudan al, istemciye tamamen güvenme
        $content    = $this->contentFor($region->load('topic', 'lessons'));
        $knownTotal = count($content['quizzes'] ?? []);
        $total      = $knownTotal > 0 ? $knownTotal : $data['total'];
        $correct    = min($data['correct'], $total);

        $result = $progress->finishRegion($request->user(), $region, $correct, $total);

        if (! $result['passed']) {
            return redirect()->route('map.index')->with(
                'error',
                "Bu bölümde {$result['correct']}/{$result['total']} soruyu ilk denemede bildin. Geçmek için en az %60 gerekir, bölgeyi tekrar dene!"
            );
        }

        $message = "Bölge tamamlandı! +{$result['xp_gained']} XP.";
        if ($result['leveled_up'] && $result['new_title']) {
            $message .= " Yeni unvanın: {$result['new_title']} 🎉";
        }
        if ($result['next_region_unlocked']) {
            $message .= ' Yeni bir bölgenin kilidi açıldı.';
        }

        return redirect()->route('map.index')->with('success', $message);
    }

    /**
     * Ekranın beklediği biçimde içerik döner:
     * 1) regions tablosundaki JSON sütunları (yapay zekâ üretimi),
     * 2) yoksa seeder'ın yazdığı lessons / questions / coding_tasks tabloları,
     * 3) hiçbiri yoksa null.
     */
    private function contentFor(Region $region): ?array
    {
        if (! empty($region->cards) && ! empty($region->quizzes)) {
            return [
                'cards'     => $region->cards,
                'quizzes'   => $region->quizzes,
                'code_task' => $region->code_task ?? [],
            ];
        }

        $lessons   = $region->lessons->sortBy('order');
        $questions = $region->questions()->get()
            ->filter(fn ($q) => ($q->is_approved ?? true));

        if ($lessons->isEmpty() || $questions->isEmpty()) {
            return null;
        }

        $cards = $lessons->map(fn ($l) => [
            'title' => $l->title,
            'desc'  => $l->body,
        ])->values()->all();

        $quizzes = $questions->map(function ($q) {
            $options = is_array($q->options) ? $q->options : (json_decode($q->options, true) ?: []);

            return [
                'q'           => $q->body,
                'options'     => array_values($options),
                'correct'     => (int) $q->correct_index,
                'hint'        => $q->hint ?? '',
                'explanation' => $q->explanation ?? '',
            ];
        })->values()->all();

        $codeTask = [];
        $taskModel = $region->codingTasks()->first();

        if ($taskModel) {
            $tc = is_array($taskModel->test_cases)
                ? $taskModel->test_cases
                : (json_decode($taskModel->test_cases, true) ?: []);

            $codeTask = [
                'instructions' => trim(($taskModel->title ? $taskModel->title . ': ' : '') . $taskModel->description),
                'function'     => $tc['function'] ?? null,
                'cases'        => $tc['cases'] ?? [],
                'starter'      => ['javascript' => (string) ($taskModel->starter_code ?? '')],
                'hint'         => (string) ($taskModel->hint ?? ''),
            ];
        }

        return ['cards' => $cards, 'quizzes' => $quizzes, 'code_task' => $codeTask];
    }
}