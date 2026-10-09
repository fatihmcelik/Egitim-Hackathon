<x-app-layout>
    <!-- x-data kapsayıcısı: modal artık bunun İÇİNDE -->
    <div class="max-w-6xl mx-auto w-full space-y-8" x-data="{ showStudyModal: false, selectedBuddyId: null, selectedBuddyName: '' }">

        <!-- ÜST PANEL: ARENA BAŞLIĞI VE OYUN MODLARI -->
        <div class="bg-[#0D1425] border border-blue-500/20 p-6 md:p-10 rounded-3xl shadow-2xl relative overflow-hidden flex flex-col items-center w-full">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-900/20 to-transparent pointer-events-none"></div>

            <div class="relative z-10 w-full">
                <h1 class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300 tracking-widest uppercase italic mb-4 text-center drop-shadow-lg">
                    ARENA
                </h1>
                <p class="text-blue-200 text-sm md:text-lg mb-8 text-center">İster botla pratik yap, ister gerçek oyuncularla kozlarını paylaş!</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-[#070B14]/60 p-6 rounded-2xl border border-white/5 backdrop-blur-sm w-full">

                    <!-- 1. Bot (PVE) -->
                    <div class="border-b md:border-b-0 md:border-r border-white/10 pb-6 md:pb-0 md:pr-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-2">🤖 Antrenman</h3>
                            <p class="text-xs text-gray-400 mb-4">Sistem botuna karşı beklemeden pratik yap.</p>
                        </div>
                        <form action="{{ route('arena.botMatch') }}" method="POST">
                            @csrf
                            <select name="topic_id" required class="w-full bg-[#11192D] border border-white/10 text-white px-4 py-3 rounded-xl mb-4 focus:ring-2 focus:ring-blue-500 cursor-pointer outline-none">
                                @foreach ($topics as $topic)
                                    <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full bg-[#1e293b] hover:bg-[#334155] text-white font-bold px-4 py-3 rounded-xl transition border border-white/5">
                                Hemen Başla
                            </button>
                        </form>
                    </div>

                    <!-- 2. Rastgele Oyuncu (PVP) -->
                    <div class="border-b md:border-b-0 md:border-r border-white/10 pb-6 md:pb-0 md:px-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-2">🌍 Sıralı Maç</h3>
                            <p class="text-xs text-gray-400 mb-4">Seninle aynı puan seviyesindeki oyuncularla savaş.</p>
                        </div>
                        <form action="{{ route('arena.randomMatch') }}" method="POST">
                            @csrf
                            <select name="topic_id" required class="w-full bg-[#11192D] border border-white/10 text-white px-4 py-3 rounded-xl mb-4 focus:ring-2 focus:ring-blue-500 cursor-pointer outline-none">
                                @foreach ($topics as $topic)
                                    <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold px-4 py-3 rounded-xl shadow-[0_0_15px_rgba(37,99,235,0.4)] transition hover:scale-105">
                                Rastgele Bul ⚔️
                            </button>
                        </form>
                    </div>

                    <!-- 3. Oda Kur/Katıl -->
                    <div class="md:pl-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2 flex items-center gap-2">🤝 Özel Oda</h3>
                            <p class="text-xs text-gray-400 mb-4">Arkadaşınla oynamak için oda kodu kullan.</p>
                        </div>
                        <div class="space-y-3 flex-1 flex flex-col justify-end">
                            <form action="{{ route('arena.joinRoom') }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" name="room_id" placeholder="Oda Kodu" required class="w-full bg-[#11192D] border border-blue-500/30 rounded-xl px-3 py-3 text-white text-sm text-center outline-none focus:border-blue-500">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-4 py-3 rounded-xl text-sm transition">Katıl</button>
                            </form>
                            <form action="{{ route('arena.createRoom') }}" method="POST">
                                @csrf
                                <select name="topic_id" class="hidden">
                                    @foreach ($topics as $topic)
                                        <option value="{{ $topic->id }}" selected></option>
                                    @endforeach
                                </select>
                                <button type="submit" class="w-full border border-dashed border-blue-500/30 text-blue-300 hover:text-white hover:bg-blue-500/10 font-bold px-4 py-3 rounded-xl text-sm transition">
                                    Veya Oda Kur
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ALT BÖLÜM: Birlikte Çalış (Sol) & Liderlik (Sağ) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 w-full">

            <!-- SOL KOLON (Çalışma Arkadaşı Modülü) -->
            <div class="lg:col-span-5 flex flex-col">
                <div class="bg-gradient-to-br from-[#0D1425] to-[#070B14] border border-blue-500/20 rounded-3xl p-8 shadow-2xl flex-1 flex flex-col relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>

                    <h3 class="text-2xl font-bold text-white mb-2 flex items-center gap-3 relative z-10">
                        🔥 Birlikte Çalış
                    </h3>
                    <p class="text-sm text-gray-400 mb-8 relative z-10">Sistem, seviyene göre en uygun çalışma arkadaşını buldu. Birlikte kodla, XP kazan!</p>

                    <!-- DİKKAT: Şimdilik sabit demo verisi. Gerçek eşleştirme gelene kadar canlıya çıkarma. -->
                    <div class="bg-[#11192D]/80 border border-white/5 p-6 rounded-2xl flex flex-col items-center text-center group hover:bg-blue-900/20 transition-all flex-1 justify-center relative z-10">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center font-black text-white text-3xl shadow-[0_0_20px_rgba(37,99,235,0.4)] mb-4 group-hover:scale-110 transition-transform">
                            E
                        </div>
                        <h4 class="text-white font-bold text-xl group-hover:text-blue-300 transition-colors">Emre Yılmaz</h4>
                        <p class="text-cyan-400 text-sm font-medium mt-1 mb-6">Veritabanı Uzmanı</p>

                        <button @click="showStudyModal = true; selectedBuddyId = 2; selectedBuddyName = 'Emre Yılmaz'" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold px-6 py-4 rounded-xl transition shadow-[0_0_15px_rgba(37,99,235,0.4)]">
                            Etkinlik Planla
                        </button>
                    </div>
                </div>
            </div>

            <!-- SAĞ KOLON (Liderlik Tablosu) -->
            <div class="lg:col-span-7 flex flex-col">
                <div class="bg-[#0D1425] border border-white/5 rounded-3xl p-8 shadow-2xl flex-1">
                    <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">🏆 Arenanın Efendileri</h3>
                    <div class="space-y-4">
                        @foreach ($leaderboard as $index => $player)
                            <div class="bg-[#070B14]/50 border border-white/5 p-4 rounded-2xl flex items-center justify-between group hover:bg-white/10 transition">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 {{ $index === 0 ? 'bg-yellow-500 text-black shadow-[0_0_15px_rgba(234,179,8,0.5)]' : 'bg-[#1e293b] text-gray-300' }} rounded-full flex shrink-0 items-center justify-center font-black">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <h4 class="text-white font-bold line-clamp-1">{{ $player->name }}</h4>
                                    </div>
                                </div>
                                <div class="text-right pl-2">
                                    <p class="text-xl font-black {{ $index === 0 ? 'text-yellow-400' : 'text-blue-400' }}">
                                        {{ $player->total_xp ?? 0 }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <!-- RANDEVU/ETKİNLİK MODALI (x-data div'inin içinde) -->
        <div x-show="showStudyModal" x-cloak style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
            <div @click.away="showStudyModal = false" class="bg-[#0D1425] border border-blue-500/30 p-8 rounded-3xl w-full max-w-md shadow-[0_0_50px_rgba(37,99,235,0.2)] relative">
                <button @click="showStudyModal = false" class="absolute top-4 right-5 text-gray-400 hover:text-white text-2xl font-bold">&times;</button>

                <div class="text-center mb-6">
                    <div class="text-5xl mb-4">🤝</div>
                    <h2 class="text-2xl font-black text-white"><span x-text="selectedBuddyName"></span> ile Çalış</h2>
                    <p class="text-gray-400 text-sm mt-2">Birlikte kod yazmak ve XP kazanmak için bir etkinlik planla.</p>
                </div>

                <form action="{{ route('study-session.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="receiver_id" :value="selectedBuddyId">

                    <div>
                        <label class="text-gray-300 text-sm font-bold mb-2 block">Buluşma Türü</label>
                        <select name="type" required class="w-full bg-[#070B14] border border-white/10 text-white px-4 py-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="digital">💻 Dijital (Google Meet / Discord)</option>
                            <option value="physical">☕ Fiziksel (Kampüs / Kütüphane / Kafe)</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-gray-300 text-sm font-bold mb-2 block">Link veya Mekan Adı</label>
                        <input type="text" name="location_or_link" placeholder="Örn: meet.google.com/xyz veya Merkez Kütüphane" required class="w-full bg-[#070B14] border border-white/10 text-white px-4 py-3 rounded-xl outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="text-gray-300 text-sm font-bold mb-2 block">Tarih ve Saat</label>
                        <input type="datetime-local" name="scheduled_at" required class="w-full bg-[#070B14] border border-white/10 text-white px-4 py-3 rounded-xl outline-none text-center focus:border-blue-500">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black px-4 py-4 rounded-xl transition shadow-[0_0_15px_rgba(37,99,235,0.4)] mt-4">
                        Davet Gönder
                    </button>
                </form>
            </div>
        </div>

    </div> <!-- x-data div'inin kapanışı -->
</x-app-layout>