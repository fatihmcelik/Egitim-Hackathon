@props(['bleed' => false])
@php
    use Illuminate\Support\Facades\Route;

    $brand = ['Kod', 'Macerası'];

    $user    = auth()->user();
    $rows    = $user ? $user->topics()->get() : collect();
    $best    = $rows->sortByDesc(fn ($t) => (int) $t->pivot->xp)->first();
    $totalXp = (int) $rows->sum(fn ($t) => (int) $t->pivot->xp);
    $titleId = $best?->pivot->current_title_id;
    $title   = $titleId ? \App\Models\Title::find($titleId)?->name : null;

    $has  = fn (string $path) => file_exists(public_path($path));
    $link = fn (string $name) => Route::has($name) ? route($name) : '#';

    $icons = [
        'home'    => '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h5v-6h4v6h5V10"/>',
        'new'     => '<rect x="4" y="4" width="16" height="16" rx="3"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/>',
        'maps'    => '<rect x="4" y="3" width="16" height="18" rx="3"/><path d="M9 8h2M9 12h2M9 16h2M14 8h2M14 12h2M14 16h2"/>',
        'arena'   => '<path d="M8 4h8v5a4 4 0 01-8 0V4z"/><path d="M8 6H5a2 2 0 002 3M16 6h3a2 2 0 01-2 3M12 13v4M9 20h6M10 17h4"/>',
        'profile' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="10" r="3"/><path d="M6.5 18.5c1.2-2.2 3-3 5.5-3s4.3.8 5.5 3"/>',
        'teacher' => '<path d="M3 8l9-4 9 4-9 4-9-4z"/><path d="M7 10.5V15c0 1.5 2.2 3 5 3s5-1.5 5-3v-4.5"/>',
        'admin'   => '<path d="M12 3l8 3v6c0 4.5-3.2 7.8-8 9-4.8-1.2-8-4.5-8-9V6l8-3z"/>',
    ];

    $nav = [
        ['label' => 'Ana Sayfa',   'href' => $link('dashboard'),    'active' => request()->routeIs('dashboard'),  'icon' => 'home'],
        ['label' => 'Yeni Harita', 'href' => $link('topics.index'), 'active' => request()->routeIs('topics.*'),  'icon' => 'new'],
        ['label' => 'Haritalarım', 'href' => $link('map.index'),    'active' => request()->routeIs('map.*'),     'icon' => 'maps'],
        ['label' => 'Arena',       'href' => $link('arena.index'),  'active' => request()->routeIs('arena.*'),   'icon' => 'arena'],
        ['label' => 'Profil',      'href' => $link('profile.edit'), 'active' => request()->routeIs('profile.*'), 'icon' => 'profile'],
    ];

    if ($user && $user->role === 'teacher') {
        $nav[] = ['label' => 'Öğretmen', 'href' => $link('teacher.dashboard'), 'active' => request()->routeIs('teacher.*'), 'icon' => 'teacher'];
    }
    if ($user && $user->role === 'admin') {
        $nav[] = ['label' => 'Yönetim', 'href' => $link('admin.reports'), 'active' => request()->routeIs('admin.*'), 'icon' => 'admin'];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $brand[0] . $brand[1] }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans antialiased text-white min-h-screen bg-[#050914]"
      style="background-image: radial-gradient(60rem 30rem at 80% -10%, rgba(79,70,229,.18), transparent), radial-gradient(40rem 30rem at 0% 100%, rgba(14,165,233,.10), transparent);">

<div class="p-3 sm:p-6 space-y-3">

    <!-- Kullanıcı kartı (pencerenin üstünde) -->
    @if($user)
        <div class="flex items-center justify-end gap-4">
            <div class="text-right leading-tight">
                <div class="font-bold text-white">{{ $user->name }}</div>
                <div class="text-sm text-gray-400">{{ $title ?? 'Çaylak' }} &bull; {{ $totalXp }} XP</div>
            </div>
            <div class="w-12 h-12 rounded-full p-[2px] bg-gradient-to-br from-indigo-400 to-cyan-400">
                @if($has('images/avatar.png'))
                    <img src="{{ asset('images/avatar.png') }}" alt="" class="w-full h-full rounded-full object-cover">
                @else
                    <div class="w-full h-full rounded-full bg-[#0b1226] flex items-center justify-center font-bold text-lg">
                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            @if(Route::has('logout'))
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-gray-500 hover:text-white transition">Çıkış</button>
                </form>
            @endif
        </div>
    @endif

    <!-- Mesajlar (hata/başarı) -->
    @if(session('error'))
        <div class="rounded-2xl border border-red-500/40 bg-red-900/20 text-red-200 px-5 py-3">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="rounded-2xl border border-emerald-500/40 bg-emerald-900/20 text-emerald-200 px-5 py-3">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-2xl border border-red-500/40 bg-red-900/20 text-red-200 px-5 py-3">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <!-- UYGULAMA PENCERESİ -->
    <div class="relative w-full flex flex-col lg:flex-row rounded-[2rem] border border-white/10 bg-[#070c1c]/90 shadow-[0_0_80px_rgba(37,99,235,0.12)] overflow-hidden {{ $bleed ? 'lg:aspect-[16/9]' : 'lg:min-h-[calc(100vh-9rem)]' }}">

        <!-- Harita modu: içerik tüm pencereyi kaplar, menü üstünde durur -->
        @if($bleed)
            <div class="order-2 relative z-0 min-h-[640px] lg:min-h-0 lg:absolute lg:inset-0">
                {{ $slot }}
            </div>
        @endif

        <!-- SOL MENÜ -->
        <aside class="order-1 relative z-20 lg:w-64 shrink-0 overflow-hidden border-b lg:border-b-0 lg:border-r border-white/5 p-5
                      {{ $bleed ? 'bg-[#0a1230]/70 backdrop-blur-md' : 'bg-gradient-to-b from-[#0b1330] to-[#070b18]' }}">
            <div class="relative z-10 flex items-center gap-3 mb-6 lg:mb-8">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center font-black text-white shadow-[0_0_20px_rgba(79,70,229,0.5)]">AI</div>
                <div class="text-2xl font-extrabold leading-none">
                    <span class="text-white">{{ $brand[0] }}</span><span class="text-indigo-300">{{ $brand[1] }}</span>
                </div>
            </div>

            <nav class="relative z-10 flex lg:flex-col gap-2 overflow-x-auto lg:overflow-visible">
                @foreach($nav as $item)
                    <a href="{{ $item['href'] }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl border text-[15px] whitespace-nowrap transition
                              {{ $item['active']
                                  ? 'bg-blue-600/15 border-blue-500/40 text-white shadow-[0_0_20px_rgba(37,99,235,0.15)]'
                                  : 'border-transparent text-gray-300 hover:bg-white/5 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0 {{ $item['active'] ? 'text-blue-300' : 'text-gray-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$item['icon']] !!}</svg>
                        <span>{{ $item['label'] }}</span>
                        @if($item['href'] === '#')
                            <span class="ml-auto text-[10px] uppercase tracking-wider text-gray-500">yakında</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            @unless($bleed)
                @if($has('images/sidebar-bg.png'))
                    <img src="{{ asset('images/sidebar-bg.png') }}" alt="" class="hidden lg:block absolute bottom-0 left-0 w-full pointer-events-none">
                @else
                    <svg class="hidden lg:block absolute bottom-0 left-0 w-full h-[46%] pointer-events-none" viewBox="0 0 260 220" preserveAspectRatio="none" aria-hidden="true">
                        <defs>
                            <linearGradient id="mtnA" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2d3a97"/><stop offset="1" stop-color="#0b1130"/></linearGradient>
                            <linearGradient id="mtnB" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#1b2466"/><stop offset="1" stop-color="#070b18"/></linearGradient>
                        </defs>
                        <polygon points="0,220 0,95 38,55 66,98 104,28 150,104 190,62 232,112 260,84 260,220" fill="url(#mtnA)" opacity=".85"/>
                        <polygon points="0,220 0,150 52,118 92,156 132,112 182,160 222,132 260,156 260,220" fill="url(#mtnB)"/>
                        <circle cx="104" cy="34" r="2" fill="#38bdf8"/><circle cx="190" cy="68" r="1.6" fill="#38bdf8"/>
                        <circle cx="70" cy="140" r="1.4" fill="#67e8f9"/><circle cx="214" cy="150" r="1.4" fill="#67e8f9"/>
                    </svg>
                @endif
            @endunless
        </aside>

        <!-- Normal sayfalarda içerik -->
        @unless($bleed)
            <div class="order-2 relative z-10 flex-1 min-w-0 p-4 sm:p-6">
                {{ $slot }}
            </div>
        @endunless
    </div>
</div>

</body>
</html>