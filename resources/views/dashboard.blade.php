<x-app-layout>
    <div class="max-w-7xl mx-auto space-y-10 py-8 px-4 relative z-50 w-full">
        
        <!-- ÜST KARŞILAMA ALANI -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-6 bg-gradient-to-r from-[#0D1425] to-[#070B14] border border-blue-500/20 p-8 rounded-3xl shadow-2xl relative overflow-hidden">
            <div class="absolute -left-10 -top-10 w-40 h-40 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10">
                <h1 class="text-4xl font-black text-white mb-2">Hoş Geldin, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">{{ Auth::user()->name }}!</span> 👋</h1>
                <p class="text-gray-400 text-lg">Macerana kaldığın yerden devam et veya yeni ufuklara yelken aç.</p>
            </div>
            
            <div class="relative z-10 bg-[#11192D]/80 border border-white/5 px-6 py-4 rounded-2xl flex items-center gap-4 shadow-lg backdrop-blur-md">
                <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Mevcut Seviye</p>
                    <p class="text-xl font-black text-white">Hackathon Sürümü Aktif</p>
                </div>
            </div>
        </div>

        <!-- MODÜL KARTLARI (Kaybolan Yönlendirme Menüleri) -->
        <h2 class="text-2xl font-bold text-white mb-4 mt-8 px-2">Nereye Gitmek İstersin?</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-50">
            
            <a href="{{ route('topics.index') }}" class="group bg-[#0D1425] hover:bg-[#11192D] border border-white/5 hover:border-blue-500/50 p-8 rounded-3xl shadow-lg transition-all hover:-translate-y-1 relative overflow-hidden cursor-pointer block">
                <div class="absolute right-0 top-0 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all pointer-events-none"></div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Yapay Zeka ile Üret</h3>
                <p class="text-gray-400">Gemini AI senin için özel bir öğrenme haritası hazırlasın.</p>
            </a>

            <a href="{{ route('map.index') }}" class="group bg-[#0D1425] hover:bg-[#11192D] border border-white/5 hover:border-cyan-500/50 p-8 rounded-3xl shadow-lg transition-all hover:-translate-y-1 relative overflow-hidden cursor-pointer block">
                <div class="absolute right-0 top-0 w-32 h-32 bg-cyan-500/10 rounded-full blur-3xl group-hover:bg-cyan-500/20 transition-all pointer-events-none"></div>
                <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Maceraya Devam Et</h3>
                <p class="text-gray-400">Önceden ürettiğin haritalarda görevleri tamamla ve XP kazan.</p>
            </a>

            <a href="{{ route('arena.index') }}" class="group bg-[#0D1425] hover:bg-[#11192D] border border-white/5 hover:border-orange-500/50 p-8 rounded-3xl shadow-lg transition-all hover:-translate-y-1 relative overflow-hidden cursor-pointer block">
                <div class="absolute right-0 top-0 w-32 h-32 bg-orange-500/10 rounded-full blur-3xl group-hover:bg-orange-500/20 transition-all pointer-events-none"></div>
                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Arena (Savaş)</h3>
                <p class="text-gray-400">Bilgini botlara veya gerçek oyunculara karşı test et.</p>
            </a>

            <a href="{{ route('profile.index') }}" class="group bg-[#0D1425] hover:bg-[#11192D] border border-white/5 hover:border-purple-500/50 p-8 rounded-3xl shadow-lg transition-all hover:-translate-y-1 relative overflow-hidden cursor-pointer block">
                <div class="absolute right-0 top-0 w-32 h-32 bg-purple-500/10 rounded-full blur-3xl group-hover:bg-purple-500/20 transition-all pointer-events-none"></div>
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">Yetenek Profilin</h3>
                <p class="text-gray-400">Gelişim barlarını, seviyeni ve kazandığın unvanları incele.</p>
            </a>
            
        </div>
    </div>
</x-app-layout>