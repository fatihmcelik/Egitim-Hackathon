<x-app-layout>
    <!-- TÜM SAYFAYI KAPSAYAN ALPINE.JS VERİSİ (Bütün Modallar ve Butonlar Buna Bağlı) -->
    <div class="max-w-7xl mx-auto space-y-8 py-8 px-4" x-data="{ activeStudentId: null, showTournamentModal: false }">
        
        <!-- ÜST PANEL -->
        <div class="bg-black/40 backdrop-blur-xl border border-white/10 p-8 rounded-3xl flex flex-col md:flex-row justify-between items-center shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
            <div class="relative z-10">
                <h1 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-indigo-500">Öğretmen Komuta Merkezi</h1>
                <p class="text-gray-400 mt-2">Sınıfını yönet, çoklu içerik üret ve canlı turnuvalar düzenle.</p>
            </div>
            
            @if($classroom)
            <div class="relative z-10 mt-6 md:mt-0 flex gap-4 items-center">
                
                <!-- CANLI TURNUVA BUTONU (Artık Kesin Çalışıyor) -->
                <button @click="showTournamentModal = true" class="bg-gradient-to-r from-orange-500 to-red-600 hover:scale-105 transition text-white px-6 py-4 rounded-2xl font-black shadow-[0_0_20px_rgba(239,68,68,0.5)]">
                    🏆 CANLI TURNUVA
                </button>

                <!-- SINIF KODU VE YENİLEME BUTONU -->
                <div class="group relative bg-indigo-900/40 border border-indigo-500/50 px-8 py-4 rounded-2xl text-center shadow-[0_0_20px_rgba(99,102,241,0.3)] transition-all hover:bg-indigo-900/60">
                    <p class="text-[10px] text-indigo-300 font-bold mb-1 uppercase tracking-widest">Sınıf Kodu</p>
                    <div class="text-4xl font-black text-white font-mono tracking-widest">{{ $classroom->code }}</div>
                    
                    <!-- Kodu Yenile Formu (Sadece Fare Üstüne Gelince Çıkar) -->
                    <form action="{{ route('teacher.refreshCode') }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        @csrf
                        <button type="submit" class="text-indigo-300 hover:text-white bg-black/30 p-1.5 rounded-lg border border-indigo-500/50" title="Kodu Yenile">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </button>
                    </form>
                </div>

            </div>
            @endif
        </div>

        @if(!$classroom)
        <!-- SINIF OLUŞTURMA EKRANI -->
        <div class="bg-black/40 border border-white/10 p-10 rounded-3xl max-w-2xl mx-auto text-center shadow-2xl">
            <div class="text-6xl mb-6">🏫</div>
            <h2 class="text-3xl font-bold text-white mb-4">Sınıfını Oluştur</h2>
            <form action="{{ route('teacher.storeClassroom') }}" method="POST" class="flex flex-col gap-4">
                @csrf
                <input type="text" name="name" placeholder="Sınıf Adı" required class="bg-gray-900 border border-white/20 text-white px-6 py-4 rounded-xl text-center outline-none">
                <button type="submit" class="bg-gradient-to-r from-cyan-500 to-indigo-600 text-white font-black px-6 py-4 rounded-xl hover:scale-105">Oluştur</button>
            </form>
        </div>
        @else
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- SOL KOLON: Liderlik Tablosu -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-black/40 border border-white/10 p-8 rounded-3xl shadow-2xl">
                    <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">🏆 Sınıf Sıralaması</h3>
                    <p class="text-xs text-gray-400 mb-4">Detaylı karne ve ödev durumu için öğrenciye tıklayın.</p>
                    
                    @if($classLeaderboard->isEmpty())
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center text-gray-400">
                            Öğrencilerinize <strong>{{ $classroom->code }}</strong> kodunu gönderin.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($classLeaderboard as $index => $student)
                                <div @click="activeStudentId = {{ $student->id }}" class="bg-white/5 border border-white/10 p-4 rounded-2xl flex items-center justify-between group cursor-pointer hover:bg-indigo-500/20 hover:border-indigo-500/50 transition-all">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 {{ $index === 0 ? 'bg-yellow-500 text-black' : 'bg-gray-800 text-gray-400 group-hover:text-white' }} rounded-full flex items-center justify-center font-black transition-colors">
                                            {{ $index + 1 }}
                                        </div>
                                        <h4 class="text-white font-bold text-lg group-hover:text-indigo-300 transition-colors">{{ $student->name }}</h4>
                                    </div>
                                    <p class="text-xl font-black text-cyan-400">{{ $student->total_xp ?? 0 }} XP</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- SAĞ KOLON: Çoklu İçerik Ekleme -->
            <div class="lg:col-span-7 bg-black/40 border border-white/10 p-8 rounded-3xl shadow-2xl">
                <div class="flex items-center justify-between mb-6 border-b border-white/10 pb-4">
                    <h3 class="text-2xl font-bold text-white flex items-center gap-3">✍️ Çoklu İçerik Ekle</h3>
                </div>
                
                <form action="{{ route('teacher.storeQuestion') }}" method="POST" x-data="{ items: [{id: 1, type: 'question'}] }">
                    @csrf
                    <div class="mb-6">
                        <label class="text-gray-300 text-sm font-bold mb-2 block">Bu içerikler hangi bölgeye eklensin?</label>
                        <select name="region_id" required class="w-full bg-gray-900 border border-white/20 text-white px-4 py-3 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($regions as $region) <option value="{{ $region->id }}">{{ $region->name }}</option> @endforeach
                        </select>
                    </div>

                    <div class="space-y-6">
                        <template x-for="(item, index) in items" :key="item.id">
                            <div class="bg-white/5 border border-white/10 p-6 rounded-2xl relative">
                                <button type="button" @click="items.splice(index, 1)" class="absolute top-4 right-4 text-gray-500 hover:text-red-500 font-bold" x-show="items.length > 1">&times; Sil</button>
                                
                                <div x-show="item.type === 'question'">
                                    <h4 class="text-indigo-400 font-bold mb-4 flex items-center gap-2"><span x-text="index + 1"></span>. Soru</h4>
                                    <textarea :name="'questions['+index+'][body]'" rows="2" class="w-full bg-gray-900 border border-white/20 text-white px-4 py-3 rounded-xl mb-4 outline-none" placeholder="Soru metni..."></textarea>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <input type="text" :name="'questions['+index+'][option_0]'" placeholder="A Şıkkı" class="bg-gray-900 border border-white/20 text-white px-4 py-3 rounded-xl">
                                        <input type="text" :name="'questions['+index+'][option_1]'" placeholder="B Şıkkı" class="bg-gray-900 border border-white/20 text-white px-4 py-3 rounded-xl">
                                        <input type="text" :name="'questions['+index+'][option_2]'" placeholder="C Şıkkı" class="bg-gray-900 border border-white/20 text-white px-4 py-3 rounded-xl">
                                        <input type="text" :name="'questions['+index+'][option_3]'" placeholder="D Şıkkı" class="bg-gray-900 border border-white/20 text-white px-4 py-3 rounded-xl">
                                    </div>
                                    <select :name="'questions['+index+'][correct_index]'" class="w-full bg-gray-900 border border-white/20 text-white px-4 py-3 rounded-xl outline-none">
                                        <option value="0">A Şıkkı Doğru</option>
                                        <option value="1">B Şıkkı Doğru</option>
                                        <option value="2">C Şıkkı Doğru</option>
                                        <option value="3">D Şıkkı Doğru</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <button type="button" @click="items.push({id: Date.now(), type: 'question'})" class="flex-1 border border-dashed border-indigo-500/50 text-indigo-400 hover:bg-indigo-500/10 font-bold px-4 py-3 rounded-xl transition">
                            + Yeni Soru Ekle
                        </button>
                    </div>

                    <button type="submit" class="w-full mt-6 bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-6 py-4 rounded-xl transition shadow-[0_0_15px_rgba(79,70,229,0.4)]">
                        Hazırlanan İçerikleri Sınıfa Gönder
                    </button>
                </form>
            </div>
        </div>
        
        <!-- YENİ EKLENEN: CANLI TURNUVA MODALI (Artık Ana div'in içinde olduğu için çalışacak) -->
        <div x-show="showTournamentModal" x-cloak style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div @click.away="showTournamentModal = false" class="bg-slate-900 border border-red-500/30 p-8 rounded-3xl w-full max-w-md shadow-[0_0_50px_rgba(239,68,68,0.2)] relative">
                <button @click="showTournamentModal = false" class="absolute top-4 right-5 text-gray-400 hover:text-white text-2xl font-bold">&times;</button>
                
                <div class="text-center mb-6">
                    <div class="text-5xl mb-4">⚔️</div>
                    <h2 class="text-2xl font-black text-white">Canlı Sınıf Turnuvası</h2>
                    <p class="text-gray-400 text-sm mt-2">Sınıftaki tüm öğrencilerin ekranına aynı soru düşer. En hızlı ve doğru cevap veren kazanır!</p>
                </div>
                
                <div class="space-y-4">
                    <select class="w-full bg-gray-900 border border-white/20 text-white px-4 py-4 rounded-xl outline-none text-center">
                        <option>Bugün İşlenen Konular (Karışık)</option>
                        <option>Sadece Döngüler</option>
                        <option>Sadece Veri Tipleri</option>
                    </select>
                    
                    <button @click="showTournamentModal = false; alert('Demo: Öğrencilerin ekranında geri sayım başladı!')" class="w-full bg-gradient-to-r from-red-600 to-orange-500 hover:from-red-500 hover:to-orange-400 text-white font-black px-4 py-4 rounded-xl transition-all shadow-lg shadow-red-500/30">
                        Turnuvayı Başlat
                    </button>
                </div>
            </div>
        </div>

        <!-- ÖĞRENCİ DETAY MODALLARI -->
        @foreach($classLeaderboard as $student)
        <div x-show="activeStudentId === {{ $student->id }}" x-cloak style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm">
            <div @click.away="activeStudentId = null" class="bg-slate-900 border border-indigo-500/30 p-8 rounded-3xl w-full max-w-2xl shadow-2xl relative">
                <button @click="activeStudentId = null" class="absolute top-4 right-5 text-gray-400 hover:text-white text-2xl font-bold">&times;</button>
                
                <div class="flex items-center gap-4 mb-8 border-b border-white/10 pb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-2xl font-black text-white">
                        {{ mb_substr($student->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-white">{{ $student->name }}</h2>
                        <p class="text-cyan-400 font-bold">{{ $student->total_xp ?? 0 }} Toplam XP</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Gelişim Profili</h3>
                        <div class="space-y-4">
                            @forelse($student->skills as $skill)
                                @php $percent = min(100, ($skill->pivot->score / 1000) * 100); @endphp
                                <div>
                                    <div class="flex justify-between items-end mb-1">
                                        <span class="text-white text-sm font-bold">{{ $skill->icon }} {{ $skill->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $skill->pivot->score }}P</span>
                                    </div>
                                    <div class="w-full bg-gray-800 rounded-full h-2">
                                        <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Henüz yetenek analizi oluşmadı.</p>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Görev & Ödev</h3>
                        <div class="bg-black/50 border border-white/5 p-4 rounded-xl text-center mb-4">
                            <p class="text-xs text-gray-400 mb-2">Görev Tamamlama</p>
                            <div class="text-3xl font-black {{ $student->homework_progress > 50 ? 'text-green-400' : 'text-orange-400' }}">{{ $student->homework_progress }}%</div>
                        </div>
                        <button class="w-full bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold py-3 rounded-xl transition text-sm">
                            📩 Yeni Ödev Gönder
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif

    </div>
</x-app-layout>