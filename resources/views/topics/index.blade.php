<x-game-layout>
    <div class="space-y-5" x-data="{
        topicInput: '',
        isGenerating: false,
        step: 0,
        tags: ['Python', 'Trigonometri', 'Türev', 'Veri Bilimi', 'Web Geliştirme'],
        steps: ['Konu analiz ediliyor...', 'Öğrenme hedefleri belirleniyor...', 'İçerikler hazırlanıyor...', 'Harita oluşturuluyor...'],
        startGeneration(e) {
            if (this.isGenerating || !this.topicInput.trim()) { e.preventDefault(); return; }
            this.isGenerating = true;
            this.step = 1;
            setTimeout(() => { this.step = 2; }, 1500);
            setTimeout(() => { this.step = 3; }, 3000);
            setTimeout(() => { this.step = 4; }, 4500);
        }
    }">

        <!-- ÜST KART: Harita üretme formu -->
        <section class="rounded-3xl border border-white/10 bg-[#0b1226]/80 p-6 sm:p-8">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-3">Yeni Öğrenme Haritası Üret</h1>
            <p class="text-gray-400 mb-6 leading-relaxed">
                Hangi konuda bir maceraya atılmak istersin?<br>
                Yapay zeka senin için 5 aşamalı kişisel bir harita hazırlayacak.
            </p>

            <form action="{{ route('topics.store') }}" method="POST" @submit="startGeneration($event)">
                @csrf

                <div class="relative mb-5">
                    <input type="text" name="prompt" x-model="topicInput" :readonly="isGenerating"
                           required maxlength="100" placeholder="Örn: Python, Veri Bilimi..."
                           class="w-full bg-[#0e1630] border border-indigo-400/60 text-white text-2xl pl-6 pr-36 py-5 rounded-2xl outline-none placeholder-gray-600
                                  focus:ring-2 focus:ring-indigo-400 focus:border-indigo-300 transition shadow-[0_0_25px_rgba(99,102,241,0.15)]">

                    <button type="submit" x-show="!isGenerating"
                            class="absolute right-2 top-2 bottom-2 px-7 rounded-xl font-bold text-white flex items-center gap-2
                                   bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 transition shadow-[0_0_15px_rgba(37,99,235,0.35)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Üret
                    </button>
                </div>

                <div>
                    <p class="text-gray-400 mb-3">Örnek konular:</p>
                    <div class="flex flex-wrap gap-3">
                        <template x-for="tag in tags" :key="tag">
                            <button type="button" @click="topicInput = tag" :disabled="isGenerating"
                                    class="px-5 py-2.5 rounded-xl border text-[15px] transition"
                                    :class="topicInput === tag
                                        ? 'bg-indigo-600/20 text-indigo-200 border-indigo-400/60 shadow-[0_0_12px_rgba(99,102,241,0.25)]'
                                        : 'bg-[#0e1630]/60 text-gray-300 border-white/10 hover:border-white/30 hover:text-white'">
                                <span x-text="tag"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </form>
        </section>

        <!-- ALT KART: Yükleniyor ekranı -->
        <section x-show="isGenerating" x-cloak x-transition.opacity
                 class="rounded-3xl border border-white/10 bg-[#0b1226]/80 p-8 sm:p-10 text-center">

            <!-- Robot kafası -->
            <div class="relative w-28 h-24 mx-auto mb-8">
                <div class="absolute -inset-6 rounded-full bg-blue-500/20 blur-2xl animate-pulse"></div>
                <div class="absolute left-1/2 -top-2 -translate-x-1/2 w-6 h-3 rounded-t-full bg-blue-400"></div>
                <div class="absolute -left-3 top-8 w-5 h-9 rounded-full bg-gradient-to-b from-blue-400 to-indigo-700"></div>
                <div class="absolute -right-3 top-8 w-5 h-9 rounded-full bg-gradient-to-b from-blue-400 to-indigo-700"></div>
                <div class="absolute inset-0 rounded-[2.5rem] bg-gradient-to-b from-blue-400 to-indigo-800 shadow-[0_0_40px_rgba(59,130,246,0.55)]"></div>
                <div class="absolute left-3 right-3 top-4 bottom-3 rounded-3xl bg-[#0a1230] flex items-center justify-center gap-5">
                    <span class="w-3 h-4 rounded-full bg-cyan-300 shadow-[0_0_12px_#67e8f9] animate-pulse"></span>
                    <span class="w-3 h-4 rounded-full bg-cyan-300 shadow-[0_0_12px_#67e8f9] animate-pulse"></span>
                </div>
            </div>

            <h2 class="text-2xl font-extrabold text-white mb-2">Yapay zeka senin için bir harita oluşturuyor...</h2>
            <p class="text-gray-400 mb-8">Konular analiz ediliyor, seviyene göre içerikler hazırlanıyor.</p>

            <div class="max-w-sm mx-auto space-y-5 text-left">
                <template x-for="(label, i) in steps" :key="i">
                    <div class="flex items-center gap-4 transition-opacity duration-500"
                         :class="step >= i + 1 ? 'opacity-100' : 'opacity-0'">
                        <div class="w-7 h-7 shrink-0 rounded-full flex items-center justify-center"
                             :class="step > i + 1 ? 'bg-emerald-500 text-white' : 'border-[3px] border-indigo-400 border-t-transparent animate-spin'">
                            <svg x-show="step > i + 1" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span :class="step > i + 1 ? 'text-gray-300' : 'text-indigo-300 font-bold'" x-text="label"></span>
                    </div>
                </template>
            </div>

            <p class="mt-8 text-xs text-gray-500">Bu işlem 20-40 saniye sürebilir, sayfayı kapatma.</p>
        </section>

        <!-- EK: Hazır haritalar (tasarımda yok; yapay zekâ beklemeden başlamak için) -->
        @php $readyTopics = isset($topics) ? $topics->take(6) : collect(); @endphp
        @if($readyTopics->isNotEmpty() && \Illuminate\Support\Facades\Route::has('topics.enroll'))
            <section x-show="!isGenerating" class="rounded-3xl border border-white/10 bg-[#0b1226]/80 p-6 sm:p-8">
                <h2 class="text-xl font-extrabold text-white mb-4">Hazır Haritalar</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($readyTopics as $topic)
                        <form action="{{ route('topics.enroll', $topic) }}" method="POST"
                              class="rounded-2xl border border-white/10 bg-[#0e1630]/60 p-5 flex flex-col gap-3">
                            @csrf
                            <div>
                                <div class="font-bold text-white">{{ $topic->name }}</div>
                                <div class="text-sm text-gray-400 mt-1 line-clamp-2">{{ $topic->description }}</div>
                            </div>
                            <button type="submit" class="self-start px-5 py-2 rounded-xl font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 transition">
                                Maceraya Başla
                            </button>
                        </form>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-game-layout>