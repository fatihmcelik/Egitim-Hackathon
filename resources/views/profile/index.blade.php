<x-app-layout>
    <div class="max-w-7xl mx-auto w-full space-y-8 py-8 px-4">
        
        <!-- PROFİL ÜST KART -->
        <div class="bg-[#0D1425] border border-blue-500/20 p-10 rounded-3xl shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center gap-8 w-full">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900/10 to-transparent pointer-events-none"></div>
            
            <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center font-black text-white text-5xl shadow-[0_0_30px_rgba(37,99,235,0.4)] border-4 border-[#070B14] relative z-10 shrink-0">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
            
            <div class="relative z-10 text-center md:text-left flex-1">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h1 class="text-4xl md:text-5xl font-black text-white">{{ $user->name }}</h1>
                    
                    <!-- HATA ÇÖZÜLDÜ: Olmayan rotaya gitmek yerine şık bir uyarı veriyor -->
                    <button onclick="alert('Hackathon Demosu: Hesap ayarları modülü sunum için kilitlenmiştir.')" class="bg-white/5 hover:bg-white/10 border border-white/10 text-white text-sm font-bold px-4 py-2 rounded-xl transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Hesap Ayarları
                    </button>
                </div>
                
                <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-4">
                    <span class="bg-blue-500/20 text-blue-300 border border-blue-500/30 px-4 py-2 rounded-full text-sm font-bold flex items-center gap-2">
                        👑 Unvan: {{ $title ?? 'Siber Çırak' }}
                    </span>
                    <span class="bg-purple-500/20 text-purple-300 border border-purple-500/30 px-4 py-2 rounded-full text-sm font-bold flex items-center gap-2">
                        ✨ Toplam XP: {{ $totalXp ?? 0 }}
                    </span>
                    <span class="bg-green-500/20 text-green-300 border border-green-500/30 px-4 py-2 rounded-full text-sm font-bold flex items-center gap-2">
                        🏫 Sınıf: {{ $user->classroom ? $user->classroom->name : 'Bir Sınıfa Katılmadı' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- YETENEK BARLARI -->
        <div class="bg-[#0D1425] border border-white/5 rounded-3xl p-8 md:p-10 shadow-2xl w-full">
            <h3 class="text-3xl font-bold text-white mb-8 flex items-center gap-3">
                🧬 Kapsamlı Yetenek Profilin
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                @if(isset($user->skills) && $user->skills->count() > 0)
                    @foreach ($user->skills as $skill)
                        @php
                            $maxScore = 1000;
                            $percent = min(100, ($skill->pivot->score / $maxScore) * 100);
                            $color = $percent > 70 ? 'from-cyan-400 to-blue-500' : ($percent > 40 ? 'from-blue-400 to-indigo-500' : 'from-indigo-400 to-purple-600');
                        @endphp
                        <div>
                            <div class="flex justify-between items-end mb-3">
                                <span class="text-white text-lg font-bold flex items-center gap-2">{{ $skill->icon }} {{ $skill->name }}</span>
                                <span class="text-sm font-mono text-gray-400 bg-[#070B14] px-3 py-1 rounded-lg border border-white/5">{{ $skill->pivot->score }} / {{ $maxScore }} YP</span>
                            </div>
                            <div class="w-full bg-[#070B14] rounded-full h-4 overflow-hidden border border-white/5 shadow-inner">
                                <div class="bg-gradient-to-r {{ $color }} h-4 rounded-full transition-all duration-1000 shadow-[0_0_10px_currentColor] relative" style="width: {{ $percent }}%">
                                    <div class="absolute inset-0 bg-white/20 w-full h-full animate-pulse rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-1 md:col-span-2 text-center text-gray-400 py-10 bg-[#070B14]/50 rounded-2xl border border-dashed border-white/10">
                        Henüz bir yetenek verisi oluşmadı. Haritalarda görevleri tamamlayarak yeteneklerini geliştirmeye başla!
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>