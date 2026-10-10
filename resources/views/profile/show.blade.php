<x-game-layout>
    @php
        $user = $user ?? auth()->user();
        $topicRows = $user->topics()->get();
        $best = $topicRows->sortByDesc(fn($t) => (int) $t->pivot->xp)->first();
        $totalXp = (int) $topicRows->sum(fn($t) => (int) $t->pivot->xp);

        // Unvan XP'den hesaplanır
$titleName = $best
    ? \App\Models\Title::where('topic_id', $best->id)
        ->where('min_xp', '<=', (int) $best->pivot->xp)
        ->orderByDesc('min_xp')
        ->first()?->name
    : null;

// Aynı isimli yetenekler (farklı konulardan) tek barda toplanır
$maxScore = 1000;
$skills = $user
    ->skills()
    ->get()
    ->groupBy('name')
    ->map(function ($group) use ($maxScore) {
        return [
            'name' => $group->first()->name,
            'icon' => $group->first()->icon,
            'score' => min($maxScore, (int) $group->sum(fn($s) => (int) $s->pivot->score)),
        ];
    })
    ->values();

// Henüz puan yoksa barlar 0 ile görünsün
if ($skills->isEmpty()) {
    $skills = collect([
        ['name' => 'Konu Bilgisi', 'icon' => '📚', 'score' => 0],
        ['name' => 'Problem Çözme', 'icon' => '🧩', 'score' => 0],
        ['name' => 'Hafıza', 'icon' => '🧠', 'score' => 0],
            ]);
        }
    @endphp

    <div class="space-y-6">

        <!-- PROFİL ÜST KART -->
        <section
            class="rounded-3xl border border-white/10 bg-[#0b1226]/80 p-8 sm:p-10 relative overflow-hidden flex flex-col md:flex-row items-center gap-8">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/15 to-transparent pointer-events-none"></div>

            <div
                class="w-28 h-28 shrink-0 rounded-full p-[3px] bg-gradient-to-br from-indigo-400 to-cyan-400 relative z-10 shadow-[0_0_30px_rgba(37,99,235,0.4)]">
                @if (file_exists(public_path('images/avatar.png')))
                    <img src="{{ asset('images/avatar.png') }}" alt=""
                        class="w-full h-full rounded-full object-cover">
                @else
                    <div
                        class="w-full h-full rounded-full bg-[#0b1226] flex items-center justify-center font-black text-white text-5xl">
                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <div class="relative z-10 text-center md:text-left flex-1">
                <h1 class="text-4xl font-black text-white">{{ $user->name }}</h1>

                <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-4">
                    <span
                        class="bg-blue-500/20 text-blue-300 border border-blue-500/30 px-4 py-2 rounded-full text-sm font-bold">
                        👑 Unvan: {{ $titleName ?? 'Çaylak' }}
                    </span>
                    <span
                        class="bg-purple-500/20 text-purple-300 border border-purple-500/30 px-4 py-2 rounded-full text-sm font-bold">
                        ✨ Toplam XP: {{ $totalXp }}
                    </span>
                    <span
                        class="bg-green-500/20 text-green-300 border border-green-500/30 px-4 py-2 rounded-full text-sm font-bold">
                        🏫 Sınıf: {{ $user->classroom?->name ?? 'Bir sınıfa katılmadın' }}
                    </span>
                </div>
            </div>
        </section>

        @if (!$user->classroom_id)
            <section class="rounded-3xl border border-white/10 bg-[#0b1226]/80 p-6">
                <h3 class="text-xl font-bold text-white mb-3">🏫 Sınıfına Katıl</h3>
                <x-join-class />
            </section>
        @endif

        <!-- YETENEK BARLARI -->
        <section class="rounded-3xl border border-white/10 bg-[#0b1226]/80 p-8 sm:p-10">
            <h3 class="text-2xl font-bold text-white mb-2">🧬 Yetenek Profilin</h3>
            <p class="text-sm text-gray-400 mb-8">Bölgeleri tamamladıkça ve sisli bölgeleri tekrar ettikçe puanların
                artar.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                @foreach ($skills as $skill)
                    @php
                        $percent = min(100, ($skill['score'] / $maxScore) * 100);
                        $color =
                            $percent > 70
                                ? 'from-cyan-400 to-blue-500'
                                : ($percent > 40
                                    ? 'from-blue-400 to-indigo-500'
                                    : 'from-indigo-400 to-purple-600');
                    @endphp
                    <div>
                        <div class="flex justify-between items-end mb-3">
                            <span class="text-white text-lg font-bold flex items-center gap-2">{{ $skill['icon'] }}
                                {{ $skill['name'] }}</span>
                            <span
                                class="text-sm font-mono text-gray-400 bg-[#070B14] px-3 py-1 rounded-lg border border-white/5">{{ $skill['score'] }}
                                / {{ $maxScore }} YP</span>
                        </div>
                        <div class="w-full bg-[#070B14] rounded-full h-4 overflow-hidden border border-white/5">
                            <div class="bg-gradient-to-r {{ $color }} h-4 rounded-full transition-all duration-1000"
                                style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-game-layout>
