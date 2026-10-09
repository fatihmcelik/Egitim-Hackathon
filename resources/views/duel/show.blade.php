<x-app-layout>
    <div class="max-w-6xl mx-auto h-[calc(100vh-120px)] flex flex-col justify-center py-6" x-data="duelSystem()">
         
        <!-- BEKLEME EKRANI -->
        <template x-if="isWaiting">
            <div class="absolute inset-0 z-50 flex flex-col items-center justify-center bg-slate-950/90 backdrop-blur-md">
                <div class="animate-spin text-cyan-400 mb-8">
                    <svg class="w-16 h-16" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                </div>

                @if(request('mode') === 'random')
                    <h2 class="text-4xl font-black text-white mb-2 text-center">Dengin Bir Rakip Aranıyor...</h2>
                    <p class="text-gray-400 text-xl mb-6 text-center max-w-lg">Yetenek seviyene uygun başka bir oyuncu "Sıralı Maç" butonuna bastığı an savaş başlayacak!</p>
                @else
                    <h2 class="text-4xl font-black text-white mb-2 text-center">Arkadaşın Bekleniyor...</h2>
                    <p class="text-gray-400 text-xl mb-6 text-center">Arkadaşına şu Özel Oda Kodunu gönder:</p>
                    <div class="bg-black/50 border border-cyan-500 text-cyan-400 font-mono text-6xl font-black px-12 py-6 rounded-3xl shadow-[0_0_40px_rgba(34,211,238,0.4)]">
                        {{ $duel->id }}
                    </div>
                @endif
                
                <a href="{{ route('arena.index') }}" class="mt-8 text-gray-500 hover:text-white transition underline">Sıradan Çık</a>
            </div>
        </template>

        <!-- SAVAŞ ÜST BARI -->
        <div class="flex justify-between items-center mb-8 bg-black/40 p-6 rounded-3xl border border-white/10 shadow-2xl relative overflow-hidden" x-show="!isWaiting" style="display: none;">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
            
            <div class="relative z-10 w-1/3 flex flex-col items-start">
                <span class="text-cyan-400 font-bold text-sm tracking-widest uppercase">SEN</span>
                <h2 class="text-2xl font-black text-white">{{ Auth::user()->name }}</h2>
                <div class="w-full bg-gray-800 rounded-full h-4 mt-2 border border-white/10">
                    <div class="bg-cyan-500 h-4 rounded-full transition-all duration-300" :style="'width: ' + myHealth + '%'"></div>
                </div>
            </div>

            <div class="relative z-10 text-5xl font-black italic text-transparent bg-clip-text bg-gradient-to-b from-yellow-300 to-red-600 animate-pulse">
                VS
            </div>

            <div class="relative z-10 w-1/3 flex flex-col items-end text-right">
                <span class="text-red-400 font-bold text-sm tracking-widest uppercase">RAKİP</span>
                <h2 class="text-2xl font-black text-white">{{ $duel->opponent->name ?? 'Bağlanıyor...' }}</h2>
                <div class="w-full bg-gray-800 rounded-full h-4 mt-2 border border-white/10 flex justify-end">
                    <div class="bg-red-500 h-4 rounded-full transition-all duration-300" :style="'width: ' + enemyHealth + '%'"></div>
                </div>
            </div>
        </div>

        <!-- SAVAŞ ALANI -->
        <div class="flex-1 bg-black/60 backdrop-blur-xl border border-white/10 rounded-3xl p-10 flex flex-col items-center justify-center text-center shadow-[0_0_50px_rgba(0,0,0,0.5)] relative" x-show="!isWaiting" style="display: none;">
            
            <template x-if="!isFinished">
                <div class="w-full max-w-3xl">
                    <div class="w-20 h-20 mx-auto bg-gray-900 border-4 border-yellow-500 rounded-full flex items-center justify-center text-3xl font-black text-white shadow-[0_0_20px_rgba(234,179,8,0.4)] mb-8" x-text="timeLeft"></div>
                    <h3 class="text-3xl font-bold text-white mb-8 leading-tight" x-text="currentQuestion.body"></h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="(option, index) in currentQuestion.options" :key="index">
                            <button @click="submitAnswer(index)" :disabled="hasAnswered" :class="{'opacity-50 cursor-not-allowed': hasAnswered}" class="bg-white/5 border border-white/10 hover:border-cyan-400 hover:bg-cyan-500/10 p-5 rounded-2xl text-white font-medium text-lg transition shadow-lg w-full text-left">
                                <span class="font-bold text-cyan-500 mr-2" x-text="['A)','B)','C)','D)'][index]"></span><span x-text="option"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            <!-- SONUÇ -->
            <template x-if="isFinished">
                <div class="animate-fade-in flex flex-col items-center">
                    <div class="text-8xl mb-6" x-text="myHealth >= enemyHealth ? '🏆' : '💀'"></div>
                    <h2 class="text-5xl font-black text-white mb-4" x-text="myHealth >= enemyHealth ? 'ZAFER!' : 'MAĞLUBİYET'"></h2>
                    <p class="text-xl text-gray-400 mb-8" x-text="myHealth >= enemyHealth ? '+50 Yetenek Puanı ve XP Kazandın' : '-20 Genel XP Kaybettin'"></p>
                    <a href="{{ route('arena.index') }}" class="px-10 py-4 bg-white text-black font-bold rounded-full hover:scale-105 transition">Arenaya Dön</a>
                </div>
            </template>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('duelSystem', () => ({
                questions: @json($questions),
                currentIndex: 0,
                currentQuestion: {},
                myHealth: 100,
                enemyHealth: 100,
                timeLeft: 15,
                timer: null,
                pollTimer: null,
                hasAnswered: false,
                isFinished: false,
                
                isGhost: {{ $duel->is_ghost ? 'true' : 'false' }},
                isWaiting: {{ $duel->status === 'pending' ? 'true' : 'false' }},
                duelId: {{ $duel->id }},

                init() {
                    if (this.isWaiting) {
                        this.pollStatus(); 
                    } else {
                        this.startGame(); 
                    }
                },

                pollStatus() {
                    this.pollTimer = setInterval(async () => {
                        let res = await fetch('/arena/api/check-status/' + this.duelId).then(r => r.json());
                        if (res.status === 'active') {
                            clearInterval(this.pollTimer);
                            window.location.reload(); 
                        }
                    }, 2000);
                },

                startGame() {
                    this.loadQuestion();
                    // Sadece Bot ise sana hasar vurur. İnsan insana kapışmada senin canını rakibin yanlış yapması etkilemez (sadece senin yanlışın canını götürür)
                    if (this.isGhost) {
                        setInterval(() => {
                            if(!this.isFinished && !this.hasAnswered && Math.random() > 0.4) {
                                this.myHealth = Math.max(0, this.myHealth - 20);
                            }
                        }, 4000);
                    }
                },

                loadQuestion() {
                    if (this.currentIndex >= this.questions.length || this.myHealth <= 0 || this.enemyHealth <= 0) {
                        this.finishDuel(); return;
                    }
                    this.currentQuestion = this.questions[this.currentIndex];
                    this.hasAnswered = false;
                    this.timeLeft = 15;
                    clearInterval(this.timer);
                    this.timer = setInterval(() => {
                        this.timeLeft--;
                        if (this.timeLeft <= 0) {
                            this.myHealth -= 20; 
                            this.nextQuestion();
                        }
                    }, 1000);
                },

                submitAnswer(index) {
                    this.hasAnswered = true;
                    clearInterval(this.timer);
                    if (index === this.currentQuestion.correct_index) {
                        this.enemyHealth = Math.max(0, this.enemyHealth - 35);
                    } else {
                        this.myHealth = Math.max(0, this.myHealth - 20);
                    }
                    setTimeout(() => { this.nextQuestion(); }, 1000);
                },

                nextQuestion() {
                    this.currentIndex++;
                    this.loadQuestion();
                },

                finishDuel() {
                    this.isFinished = true;
                    clearInterval(this.timer);
                    let isWin = this.myHealth >= this.enemyHealth;
                    fetch('{{ route('arena.answer', $duel->id) }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ win: isWin })
                    });
                }
            }))
        })
    </script>
</x-app-layout>