<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'KodMacerası') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans antialiased text-white bg-[#070B14] min-h-screen flex overflow-hidden relative">

    <!-- ARKA PLAN KATMANLARI (body'nin kendi background'u yerine ayrı katmanlar: birbirini ezmiyorlar) -->
    <div class="fixed inset-0 z-0 pointer-events-none bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-900/20 via-transparent to-transparent"></div>
    <div class="fixed inset-0 z-0 pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/stardust.png');"></div>

    <!-- SOL MENÜ (SIDEBAR) : fixed, "relative" KALDIRILDI -->
    <aside class="w-64 bg-[#0B1120]/95 backdrop-blur-xl border-r border-white/5 flex flex-col h-screen fixed left-0 top-0 z-50 overflow-hidden">

        <div class="h-20 flex items-center px-6 gap-3 border-b border-white/5 relative z-10">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center font-black text-white text-sm shadow-[0_0_15px_rgba(37,99,235,0.5)]">
                AI
            </div>
            <span class="text-xl font-bold text-white tracking-wide">Kod<span class="text-blue-400">Macerası</span></span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 relative z-10">
            <a href="{{ route('map.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="font-medium">Ana Sayfa</span>
            </a>

            <!-- Aktif Menü (Yeni Harita) -->
            <a href="{{ route('topics.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-900/20 text-blue-400 border border-blue-500/30 transition shadow-[0_0_15px_rgba(37,99,235,0.1)]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium">Yeni Harita</span>
            </a>

            <a href="{{ route('arena.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span class="font-medium">Arena</span>
            </a>

            @if(Auth::user()->role === 'teacher')
            <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-purple-400 hover:text-purple-300 hover:bg-purple-500/10 transition mt-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                <span class="font-medium">Öğretmen Paneli</span>
            </a>
            @endif
        </nav>

        <!-- Sol Menü Alt Dekoratif Dağ/Buzul Görseli -->
        <div class="absolute bottom-0 left-0 w-full h-48 opacity-40 pointer-events-none z-0">
            <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="w-full h-full">
                <polygon fill="#1e40af" points="0,100 0,60 20,80 45,40 70,70 100,30 100,100" />
                <polygon fill="#1e3a8a" points="0,100 0,80 30,50 60,85 100,60 100,100" />
                <polygon fill="#0f172a" points="0,100 15,70 50,90 85,65 100,100" />
            </svg>
        </div>
    </aside>

    <!-- SAĞ ANA İÇERİK ALANI -->
    <main class="ml-64 w-[calc(100%-16rem)] h-screen overflow-y-auto flex flex-col relative z-10">

        <header class="h-20 px-8 flex items-center justify-end sticky top-0 z-40 bg-[#070B14]/60 backdrop-blur-md border-b border-white/5">
            @auth
            @php
                $mapXp = \Illuminate\Support\Facades\DB::table('topic_user')->where('user_id', Auth::id())->sum('xp');
                $skillXp = \Illuminate\Support\Facades\DB::table('skill_user')->where('user_id', Auth::id())->sum('score');
                $totalXp = $mapXp + $skillXp;

                $currentTitleName = 'Siber Çırak';
                if ($totalXp >= 3000) $currentTitleName = 'Siber Efsane';
                elseif ($totalXp >= 1500) $currentTitleName = 'Yazılım Ustası';
                elseif ($totalXp >= 500) $currentTitleName = 'Kod Şövalyesi';
            @endphp
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-bold text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400">{{ $currentTitleName }} • {{ $totalXp }} XP</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-purple-600 flex items-center justify-center border-2 border-white/10 shadow-lg">
                    <span class="text-white font-bold text-sm">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                </div>
            </div>
            @endauth
        </header>

        <div class="p-8">
            {{ $slot }}
        </div>
    </main>
</body>
</html>