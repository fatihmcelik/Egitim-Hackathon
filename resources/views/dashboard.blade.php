<x-game-layout>
    @php $has = fn ($n) => \Illuminate\Support\Facades\Route::has($n); @endphp

    <div class="space-y-6">

        <!-- ÜST: karşılama + devam et -->
        <section class="rounded-3xl border border-white/10 bg-gradient-to-br from-[#10204a] to-[#0b1226] p-6 sm:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-white">
                    Hoş geldin, <span class="text-sky-300">{{ $user->name }}</span> 👋
                </h1>
                <p class="text-gray-400 mt-2">
                    @if($topic)
                        <span class="text-gray-200 font-semibold">{{ $topic->name }}</span> maceranda kaldığın yerden devam et.
                    @else
                        Henüz bir haritan yok. Yapay zekâ ile ilk haritanı oluştur.
                    @endif
                </p>
            </div>

            <div class="w-full lg:w-[26rem] rounded-2xl border border-white/10 bg-[#0a1128]/80 p-5">
                @if($topic)
                    <div class="flex justify-between text-sm text-gray-300 mb-2">
                        <span>Harita ilerlemen</span>
                        <span class="font-semibold text-white">%{{ $progress }}</span>
                    </div>
                    <div class="h-2.5 rounded-full bg-white/10 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-400" style="width: {{ $progress }}%"></div>
                    </div>

                    @if($nextRegion)
                        <p class="mt-4 text-sm text-gray-400">
                            {{ $nextRegion->user_status === 'fogged' ? 'Tekrar etmen gereken bölge' : 'Sıradaki bölge' }}
                        </p>
                        <p class="font-bold text-white">{{ $nextRegion->order }}. {{ $nextRegion->name }}</p>
                        <a href="{{ route('region.show', $nextRegion->id) }}"
                           class="mt-4 inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 transition shadow-[0_0_20px_rgba(37,99,235,0.35)]">
                            {{ $nextRegion->user_status === 'fogged' ? 'Sisi Temizle' : 'Devam Et' }} →
                        </a>
                    @else
                        <p class="mt-4 text-emerald-300 font-semibold">🎉 Bu haritadaki tüm bölgeleri tamamladın!</p>
                        <a href="{{ route('topics.index') }}" class="mt-3 inline-block text-sky-300 hover:text-sky-200 font-semibold">Yeni harita üret →</a>
                    @endif
                @else
                    <a href="{{ route('topics.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 transition">
                        ✨ İlk Haritanı Oluştur
                    </a>
                @endif
            </div>
        </section>

        <!-- ORTA: unvan ve XP -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <section class="rounded-3xl border border-white/10 bg-[#0b1226]/80 p-6">
                <div class="text-sm text-gray-400 mb-1">Unvanın</div>
                <div class="text-2xl font-extrabold text-white">👑 {{ $title?->name ?? 'Çaylak' }}</div>
                @if($topic)
                    <div class="mt-4 h-2 rounded-full bg-white/10 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-purple-500 to-indigo-400" style="width: {{ $titleProgress }}%"></div>
                    </div>
                    <p class="mt-2 text-sm text-gray-400">
                        @if($nextTitle)
                            Sıradaki unvan <span class="text-gray-200 font-semibold">{{ $nextTitle->name }}</span> için {{ max(0, $nextTitle->min_xp - $topicXp) }} XP kaldı.
                        @else
                            Bu konudaki en yüksek unvandasın.
                        @endif
                    </p>
                @endif
            </section>

            <section class="rounded-3xl border border-white/10 bg-[#0b1226]/80 p-6">
                <div class="text-sm text-gray-400 mb-1">Toplam XP</div>
                <div class="text-2xl font-extrabold text-white">✨ {{ $totalXp }}</div>
                @if($topic)
                    <p class="mt-4 text-sm text-gray-400"><span class="text-gray-200 font-semibold">{{ $topic->name }}</span> haritasından {{ $topicXp }} XP.</p>
                @endif
            </section>
        </div>

        <!-- ALT: unutulma riski + yetenekler -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <section class="rounded-3xl border border-white/10 bg-[#0b1226]/80 p-6">
                <h2 class="text-xl font-bold text-white mb-4">🌫️ Unutulma Riski</h2>

                @forelse($fogRisks as $r)
                    <div class="flex items-center justify-between gap-4 rounded-2xl border border-red-400/30 bg-red-900/10 px-4 py-3 mb-3">
                        <div>
                            <div class="font-semibold text-white">{{ $r->order }}. {{ $r->name }}</div>
                            <div class="text-sm text-red-300">
                                {{ $r->user_status === 'fogged' ? 'Sislendi, unutmaya başlıyorsun' : '1 gün içinde sislenecek' }}
                            </div>
                        </div>
                        <a href="{{ route('region.show', $r->id) }}" class="shrink-0 px-4 py-2 rounded-xl text-sm font-bold text-white bg-red-500/70 hover:bg-red-500 transition">Tekrar Et</a>
                    </div>
                @empty
                    <p class="text-gray-400">Şu an unutulma riski olan bölge yok. Her şey taze 🌿</p>
                @endforelse
            </section>

            <section class="rounded-3xl border border-white/10 bg-[#0b1226]/80 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-white">🧬 Yeteneklerin</h2>
                    @if($has('profile.show'))
                        <a href="{{ route('profile.show') }}" class="text-sm text-sky-300 hover:text-sky-200">Tümünü gör →</a>
                    @endif
                </div>

                <div class="space-y-4">
                    @foreach($skills as $skill)
                        @php $pct = min(100, ($skill['score'] / 1000) * 100); @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="text-gray-200">{{ $skill['icon'] }} {{ $skill['name'] }}</span>
                                <span class="text-gray-400 font-mono">{{ $skill['score'] }}</span>
                            </div>
                            <div class="h-2.5 rounded-full bg-white/10 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-cyan-400 to-blue-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- SINIF -->
        <section class="rounded-3xl border border-white/10 bg-[#0b1226]/80 p-6">
            @if($classroom)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                    <div>
                        <h2 class="text-xl font-bold text-white">🏫 {{ $classroom->name }}</h2>
                        <p class="text-sm text-gray-400">Sınıf toplam XP: {{ $classXp }}</p>
                    </div>
                    <div class="sm:w-64">
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="text-gray-200 font-semibold">Sınıf Seviyesi {{ $classLevel }}</span>
                            <span class="text-gray-400">%{{ $classProgress }}</span>
                        </div>
                        <div class="h-2.5 rounded-full bg-white/10 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-orange-500" style="width: {{ $classProgress }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    @foreach($board as $i => $row)
                        <div class="flex items-center justify-between rounded-xl px-4 py-2.5 {{ $row->id === $user->id ? 'bg-indigo-600/20 border border-indigo-400/40' : 'bg-white/5' }}">
                            <div class="flex items-center gap-3">
                                <span class="w-6 text-center font-bold text-gray-300">{{ $i + 1 }}</span>
                                <span class="text-white">{{ $row->name }}{{ $row->id === $user->id ? ' (sen)' : '' }}</span>
                            </div>
                            <span class="font-mono text-gray-300">{{ (int) $row->total_xp }} XP</span>
                        </div>
                    @endforeach
                </div>
            @else
                <h2 class="text-xl font-bold text-white mb-1">🏫 Sınıfına Katıl</h2>
                <p class="text-sm text-gray-400 mb-4">Sınıf arkadaşlarınla yarış, sınıfın seviyesini birlikte yükseltin.</p>
                <x-join-class />
            @endif
        </section>

        <!-- ARENA -->
        @if($has('arena.index'))
            <section class="rounded-3xl border border-white/10 bg-gradient-to-r from-[#2a1646] to-[#0b1226] p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white">⚔️ Arena</h2>
                    <p class="text-sm text-gray-400">Bilgini botlara veya gerçek oyunculara karşı test et.</p>
                </div>
                <a href="{{ route('arena.index') }}" class="px-6 py-3 rounded-xl font-bold text-white bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-500 hover:to-fuchsia-500 transition text-center">Arena'ya Git</a>
            </section>
        @endif
    </div>
</x-game-layout>