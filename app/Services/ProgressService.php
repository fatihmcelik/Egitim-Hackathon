<?php

namespace App\Services;

use App\Models\Attempt;
use App\Models\Question;
use App\Models\Region;
use App\Models\Title;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProgressService
{
    private const PASS_RATIO       = 0.6; // bölgeyi geçmek için doğru oranı
    private const XP_PER_CORRECT   = 20;  // doğru cevap başına XP
    private const XP_FIRST_BONUS   = 40;  // bölgeyi ilk kez tamamlama bonusu
    private const XP_REPLAY        = 10;  // tekrar çözümde sabit XP (XP farmlanmasın)

    /**
     * Tek bir sorunun denemesini kaydeder (öğretmen analizleri için).
     */
    public function recordAnswer(User $user, Question $question, bool $isCorrect): void
    {
        Attempt::create([
            'user_id'          => $user->id,
            'region_id'        => $question->region_id,
            'attemptable_id'   => $question->id,
            'attemptable_type' => $question->getMorphClass(),
            'score'            => $isCorrect ? 10 : 0,
            'passed'           => $isCorrect,
        ]);
    }

    /**
     * Quiz bittiğinde çağrılır. Geçtiyse XP verir, unvanı kontrol eder,
     * bölgeyi tamamlar (sis sayacını sıfırlar) ve sıradaki bölgeyi açar.
     * Arayüzün kullanması için sonuç dizisi döner.
     */
    public function finishRegion(User $user, Region $region, int $correct, int $total): array
    {
        $total  = max($total, 1);
        $passed = ($correct / $total) >= self::PASS_RATIO;

        $result = [
            'passed'               => $passed,
            'correct'              => $correct,
            'total'                => $total,
            'xp_gained'            => 0,
            'leveled_up'           => false,
            'old_title'            => null,
            'new_title'            => null,
            'next_region_unlocked' => false,
        ];

        if (! $passed) {
            return $result;
        }

        $alreadyCompleted = DB::table('region_user')
            ->where('user_id', $user->id)
            ->where('region_id', $region->id)
            ->where('status', 'completed')
            ->exists();

        $xp = $alreadyCompleted
            ? self::XP_REPLAY
            : ($correct * self::XP_PER_CORRECT) + self::XP_FIRST_BONUS;

        $result['xp_gained'] = $xp;

        // Unvan / XP
        $titleInfo = $this->addXp($user, $region->topic_id, $xp);
        $result['leveled_up'] = $titleInfo['leveled_up'];
        $result['old_title']  = $titleInfo['old_title'];
        $result['new_title']  = $titleInfo['new_title'];

        // Bölgeyi tamamla ve sis sayacını bugüne çek (tekrar çözmek sisi temizler)
        $user->regions()->syncWithoutDetaching([
            $region->id => [
                'status'           => 'completed',
                'last_reviewed_at' => now(),
            ],
        ]);

        // Sıradaki bölgeyi aç (zaten kaydı varsa dokunma, tamamlanmışı geri çevirme)
        $next = Region::where('topic_id', $region->topic_id)
            ->where('order', '>', $region->order)
            ->orderBy('order')
            ->first();

        if ($next) {
            $nextHasRow = DB::table('region_user')
                ->where('user_id', $user->id)
                ->where('region_id', $next->id)
                ->exists();

            if (! $nextHasRow) {
                $user->regions()->attach($next->id, ['status' => 'open']);
                $result['next_region_unlocked'] = true;
            }
        }

        return $result;
    }

    /**
     * XP ekler, unvan değişti mi hesaplar.
     */
    private function addXp(User $user, int $topicId, int $amount): array
    {
        $row = DB::table('topic_user')
            ->where('user_id', $user->id)
            ->where('topic_id', $topicId)
            ->first();

        if (! $row) {
            $user->topics()->syncWithoutDetaching([$topicId]);

            $row = DB::table('topic_user')
                ->where('user_id', $user->id)
                ->where('topic_id', $topicId)
                ->first();
        }

        $oldXp = (int) ($row->xp ?? 0);
        $newXp = $oldXp + $amount;

        $oldTitle = $this->titleFor($topicId, $oldXp);
        $newTitle = $this->titleFor($topicId, $newXp);

        DB::table('topic_user')
            ->where('user_id', $user->id)
            ->where('topic_id', $topicId)
            ->update([
                'xp'               => $newXp,
                'current_title_id' => $newTitle?->id ?? $row->current_title_id,
            ]);

        $leveledUp = $newTitle && (! $oldTitle || $oldTitle->id !== $newTitle->id);

        return [
            'leveled_up' => (bool) $leveledUp,
            'old_title'  => $oldTitle?->name,
            'new_title'  => $newTitle?->name,
        ];
    }

    /**
     * Verilen XP'ye uyan en yüksek unvanı döner (unvan hesaplanır, saklanmaz).
     */
    private function titleFor(int $topicId, int $xp): ?Title
    {
        return Title::where('topic_id', $topicId)
            ->where('min_xp', '<=', $xp)
            ->orderByDesc('min_xp')
            ->first();
    }
}