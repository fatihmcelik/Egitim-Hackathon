<x-game-layout :bleed="true">
    @php
        $cfg   = config('map');
        $slots = $cfg['slots'];

        $hasBg    = file_exists(public_path($cfg['background']));
        $hasFog   = file_exists(public_path($cfg['fog']));
        $iconFile = 'images/topics/' . $topic->slug . '.png';
        $hasTopicIcon = file_exists(public_path($iconFile));

        // Bölge -> ada konumu eşlemesi (sıraya göre)
        $items = [];
        foreach ($regions->values() as $i => $r) {
            if (! isset($slots[$i])) { break; }
            $items[] = ['region' => $r, 'x' => $slots[$i][0], 'y' => $slots[$i][1]];
        }

        $styles = [
            'completed' => [
                'box'   => 'border-emerald-400/80 shadow-[0_0_22px_rgba(52,211,153,0.35)]',
                'sub'   => 'text-emerald-300',
                'num'   => 'text-white',
                'label' => '✓ Tamamlandı',
            ],
            'open' => [
                'box'   => 'border-sky-400 shadow-[0_0_40px_rgba(56,189,248,0.7)]',
                'sub'   => 'text-sky-300',
                'num'   => 'text-white',
                'label' => 'Devam ediyor',
            ],
            'fogged' => [
                'box'   => 'border-red-400/70 border-dashed shadow-[0_0_22px_rgba(248,113,113,0.3)]',
                'sub'   => 'text-red-300',
                'num'   => 'text-gray-200',
                'label' => 'Unutuluyor! Tekrar et',
            ],
            'locked' => [
                'box'   => 'border-white/15',
                'sub'   => 'text-gray-400',
                'num'   => 'text-gray-300',
                'label' => 'Henüz açılmadı',
            ],
        ];

        $lineColors = [
            'completed' => '#e5e7eb',
            'fogged'    => '#f87171',
            'open'      => '#38bdf8',
            'locked'    => '#64748b',
        ];
    @endphp

    <div class="relative w-full h-full min-h-[640px] lg:min-h-0 overflow-hidden">

        <!-- ZEMİN -->
        @if($hasBg)
            <img src="{{ asset($cfg['background']) }}" alt="" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-[#050914]/15"></div>
        @else
            <div class="absolute inset-0"
                 style="background: radial-gradient(60% 50% at 75% 40%, rgba(37,99,235,.28), transparent), radial-gradient(50% 50% at 30% 85%, rgba(16,185,129,.18), transparent), linear-gradient(180deg, #071426, #04101c);"></div>
            @foreach($items as $it)
                <div class="absolute -translate-x-1/2 -translate-y-1/2 w-72 h-44 rounded-[50%] bg-emerald-700/25 blur-2xl"
                     style="left: {{ $it['x'] }}%; top: {{ $it['y'] }}%;"></div>
            @endforeach
        @endif

        <!-- NOKTALI YOLLAR -->
        <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
            @for($i = 0; $i < count($items) - 1; $i++)
                @php
                    $a  = $items[$i];
                    $b  = $items[$i + 1];
                    $cx = ($a['x'] + $b['x']) / 2 + ($b['y'] - $a['y']) * 0.12;
                    $cy = ($a['y'] + $b['y']) / 2 - ($b['x'] - $a['x']) * 0.12;
                    $stroke = $lineColors[$a['region']->user_status] ?? '#64748b';
                @endphp
                <path d="M {{ $a['x'] }} {{ $a['y'] }} Q {{ $cx }} {{ $cy }} {{ $b['x'] }} {{ $b['y'] }}"
                      fill="none" stroke="{{ $stroke }}" stroke-width="2.5" stroke-dasharray="6 8"
                      stroke-linecap="round" vector-effect="non-scaling-stroke" opacity="0.85"/>
            @endfor
        </svg>

        <!-- BAŞLIK -->
        <div class="absolute top-5 left-5 lg:left-[17.5rem] z-10 flex items-center gap-4">
            @if($hasTopicIcon)
                <img src="{{ asset($iconFile) }}" alt="" class="w-14 h-14 object-contain">
            @else
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-2xl font-black shadow-[0_0_25px_rgba(79,70,229,0.5)]">
                    {{ mb_strtoupper(mb_substr($topic->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h1 class="text-3xl font-extrabold text-white leading-tight">{{ $topic->name }}</h1>
                <p class="text-sm text-gray-300">Sıfırdan ustalığa doğru {{ count($items) }} aşamalı öğrenme haritan</p>
            </div>
        </div>

        <!-- GENEL İLERLEME -->
        <div class="absolute top-5 right-5 z-10 hidden md:block w-64 rounded-2xl border border-white/15 bg-[#0a1128]/80 backdrop-blur px-5 py-3">
            <div class="text-sm text-gray-300 mb-2">Genel İlerleme</div>
            <div class="flex items-center gap-4">
                <div class="flex-1 h-2.5 rounded-full bg-white/10 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-400" style="width: {{ $progress }}%"></div>
                </div>
                <div class="text-lg font-semibold text-white">%{{ $progress }}</div>
            </div>
        </div>

        <!-- BÖLGE DÜĞÜMLERİ -->
        @foreach($items as $it)
            @php
                $r      = $it['region'];
                $status = $r->user_status;
                $st     = $styles[$status] ?? $styles['locked'];
                $clickable = in_array($status, ['completed', 'open', 'fogged'], true);
                $href   = $clickable ? route('region.show', $r->id) : null;

                $sub = $st['label'];
                if ($status === 'completed' && $r->days_left !== null && $r->days_left <= 1) {
                    $sub = '✓ Tamamlandı · sis riski';
                }
            @endphp

            <div class="absolute z-10 -translate-x-1/2 -translate-y-1/2" style="left: {{ $it['x'] }}%; top: {{ $it['y'] }}%;">

                <!-- Parlama / sis efektleri -->
                @if($status === 'open')
                    <div class="absolute -inset-10 rounded-full bg-sky-400/25 blur-2xl animate-pulse pointer-events-none"></div>
                @endif
                @if($status === 'fogged' || $status === 'locked')
                    @if($hasFog)
                        <img src="{{ asset($cfg['fog']) }}" alt="" class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[28rem] max-w-none pointer-events-none opacity-90">
                    @else
                        <div class="absolute -inset-x-16 -inset-y-12 rounded-full blur-2xl pointer-events-none
                                    {{ $status === 'fogged' ? 'bg-slate-200/25' : 'bg-slate-900/45' }}"></div>
                    @endif

                    <!-- Kilit -->
                    <div class="absolute left-1/2 -top-14 -translate-x-1/2 w-11 h-11 rounded-xl bg-[#0a1128]/90 border border-white/20 flex items-center justify-center text-gray-200">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/><circle cx="12" cy="15.5" r="1" fill="currentColor"/></svg>
                    </div>
                @endif

                <!-- Kart -->
                @if($href)
                    <a href="{{ $href }}" class="relative flex items-center gap-3 px-5 py-3 rounded-2xl bg-[#0a1128]/90 backdrop-blur border-2 {{ $st['box'] }} min-w-[11rem] hover:scale-105 transition">
                @else
                    <div class="relative flex items-center gap-3 px-5 py-3 rounded-2xl bg-[#0a1128]/90 backdrop-blur border-2 {{ $st['box'] }} min-w-[11rem] cursor-not-allowed">
                @endif
                        <span class="text-2xl font-bold {{ $st['num'] }}">{{ $r->order }}</span>
                        <span class="leading-tight">
                            <span class="block font-semibold text-white">{{ $r->name }}</span>
                            <span class="block text-sm {{ $st['sub'] }}">{{ $sub }}</span>
                        </span>
                @if($href)
                    </a>
                @else
                    </div>
                @endif
            </div>
        @endforeach

        <!-- ALT AÇIKLAMA -->
        <div class="absolute bottom-4 left-4 lg:left-[17.5rem] right-4 z-10 flex flex-wrap items-center gap-x-8 gap-y-2 rounded-2xl border border-white/10 bg-[#0a1128]/85 backdrop-blur px-5 py-3 text-sm">
            <span class="flex items-center gap-2 text-emerald-300"><span class="w-3.5 h-3.5 rounded-full bg-emerald-500 ring-4 ring-emerald-500/25"></span>Tamamlandı</span>
            <span class="flex items-center gap-2 text-sky-300"><span class="w-3.5 h-3.5 rounded-full bg-sky-500 ring-4 ring-sky-500/25"></span>Devam Ediyor</span>
            <span class="flex items-center gap-2 text-gray-300"><span class="w-3.5 h-3.5 rounded-full bg-gray-400 ring-4 ring-gray-400/25"></span>Unutulma Riski (Sis)</span>
            <span class="flex items-center gap-2 text-gray-300">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/></svg>
                Kilitli
            </span>
        </div>
    </div>
</x-game-layout>