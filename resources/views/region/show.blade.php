<x-app-layout>
    @php
        $cards   = $content['cards'] ?? [];
        $quizzes = $content['quizzes'] ?? [];
        $task    = $content['code_task'] ?? [];
        $hasTask = is_array($task) && (! empty($task['cases']) || ! empty($task['validation_regex']));

        // Elle eklenmiş video (lessons tablosu); yoksa "YouTube'da ara" butonu gösterilir
        $videoLesson = $region->lessons->first(fn ($l) => ! empty($l->video_id));
        $videoId     = $videoLesson->video_id ?? null;
        if ($videoId && ! preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)) {
            $videoId = null;
        }
        $videoStart = (int) ($videoLesson->video_start ?? 0);
        $videoEnd   = (int) ($videoLesson->video_end ?? 0);
        $embedUrl   = $videoId
            ? 'https://www.youtube.com/embed/' . $videoId . '?rel=0'
                . ($videoStart > 0 ? '&start=' . $videoStart : '')
                . ($videoEnd > 0 ? '&end=' . $videoEnd : '')
            : null;
        $searchUrl = 'https://www.youtube.com/results?search_query=' . urlencode($region->topic->name . ' ' . $region->name . ' Türkçe');
    @endphp

    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6 lg:h-[calc(100vh-100px)] py-4 px-2">

        <!-- Sol Menü -->
        <aside class="w-full lg:w-1/4 bg-black/40 backdrop-blur-xl border border-white/10 rounded-3xl p-6 flex flex-col shadow-2xl overflow-y-auto">
            <div class="mb-8">
                <span class="text-xs font-bold text-purple-400 tracking-widest uppercase mb-1 block">{{ $region->topic->name }} - Adım {{ $region->order }}</span>
                <h2 class="text-3xl font-black text-white leading-tight">{{ $region->name }}</h2>
                <p class="text-gray-400 text-sm mt-2">{{ $region->description ?? 'Bu bölgenin sırlarını keşfet.' }}</p>
            </div>

            <div class="space-y-4 flex-1">
                <button onclick="showTab('learn')" id="btn-learn" class="tab-btn w-full text-left bg-white/5 border border-purple-500/50 p-4 rounded-2xl transition flex items-center justify-between">
                    <h4 class="text-white font-bold">1. Bilgiyi Al</h4>
                    <div class="w-8 h-8 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-300">📖</div>
                </button>
                <button onclick="showTab('quiz')" id="btn-quiz" class="tab-btn w-full text-left bg-black/40 border border-white/10 p-4 rounded-2xl transition flex items-center justify-between">
                    <div>
                        <h4 class="text-white font-bold">2. Zihnini Sına</h4>
                        <p id="quiz-progress-text" class="text-xs text-gray-500">Hazırlanıyor...</p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-cyan-500/20 flex items-center justify-center text-cyan-300">❓</div>
                </button>
                <button onclick="showTab('code')" id="btn-code" class="tab-btn w-full text-left bg-black/40 border border-white/10 p-4 rounded-2xl transition flex items-center justify-between">
                    <h4 class="text-white font-bold">3. {{ $hasTask ? 'Kodu Yaz' : 'Bölümü Bitir' }}</h4>
                    <div class="w-8 h-8 rounded-full bg-yellow-500/20 flex items-center justify-center text-yellow-300">💻</div>
                </button>
            </div>
        </aside>

        <!-- Sağ İçerik Alanı -->
        <main class="w-full lg:w-3/4 relative flex flex-col gap-4 min-h-0">

            <!-- 1. BİLGİ KARTLARI + VİDEO -->
            <div id="tab-learn" class="flex-1 min-h-0 overflow-y-auto grid grid-cols-1 xl:grid-cols-2 gap-8 content-start pr-1 animate-fade-in">

                <section class="flex flex-col items-center pt-2">
                    <div id="cards-container" class="relative w-full max-w-md h-[440px] mb-12"></div>
                    <div class="flex justify-center items-center gap-6 bg-black/50 backdrop-blur-md px-6 py-3 rounded-full border border-white/10">
                        <button onclick="prevCard()" class="text-white hover:text-cyan-400 font-bold transition">&larr; Geri</button>
                        <span id="card-counter" class="text-gray-400 font-mono font-bold">1 / 1</span>
                        <button onclick="nextCard()" class="text-white hover:text-cyan-400 font-bold transition">İleri &rarr;</button>
                        <button onclick="speakCard()" class="text-yellow-300 hover:text-yellow-200 font-bold transition" title="Kartı sesli oku">🔊 Dinle</button>
                    </div>
                </section>

                <section class="flex flex-col gap-3">
                    <h3 class="text-white font-bold text-lg">🎬 Konu Videosu</h3>
                    @if($embedUrl)
                        <div class="w-full aspect-video bg-black border border-white/10 rounded-3xl overflow-hidden">
                            <iframe class="w-full h-full" src="{{ $embedUrl }}" title="Konu videosu" frameborder="0"
                                    allow="accelerometer; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        </div>
                    @else
                        <div class="w-full aspect-video bg-black/40 border border-white/10 rounded-3xl flex flex-col items-center justify-center text-center p-6 gap-3">
                            <div class="text-5xl">🎬</div>
                            <p class="text-gray-300">Bu bölüm için hazır bir video yok.</p>
                        </div>
                    @endif
                    <a href="{{ $searchUrl }}" target="_blank" rel="noopener"
                       class="self-start bg-red-600 hover:bg-red-500 text-white font-bold px-5 py-3 rounded-full transition">
                        ▶ YouTube'da bu konuyu ara
                    </a>
                </section>
            </div>

            <!-- 2. ZİHNİNİ SINA -->
            <div id="tab-quiz" class="hidden flex-1 min-h-0 animate-fade-in">
                <div class="h-full bg-black/40 border border-cyan-500/30 rounded-3xl p-6 md:p-10 flex flex-col items-center text-center overflow-y-auto">
                    <span id="quiz-counter" class="text-cyan-400 font-bold mb-4 tracking-widest uppercase">Soru 1</span>
                    <h3 id="quiz-question" class="text-2xl md:text-3xl font-bold text-white mb-8 max-w-3xl leading-relaxed">Yükleniyor...</h3>

                    <div id="quiz-options" class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full max-w-4xl"></div>

                    <div class="mt-6 flex gap-3 items-center">
                        <button id="hint-btn" onclick="showHint()" class="hidden bg-white/10 hover:bg-white/20 text-yellow-300 font-bold px-5 py-2 rounded-full transition">💡 İpucu</button>
                    </div>

                    <div id="quiz-feedback-box" class="hidden mt-4 w-full max-w-2xl p-4 rounded-2xl border transition-all text-left">
                        <p id="quiz-feedback-text" class="text-lg font-medium"></p>
                    </div>

                    <button id="next-question-btn" onclick="nextQuestion()" class="hidden mt-6 bg-cyan-500 hover:bg-cyan-400 text-black font-extrabold px-10 py-4 rounded-full transition shadow-[0_0_20px_rgba(34,211,238,0.5)]">
                        Sıradaki Soru &rarr;
                    </button>
                </div>
            </div>

            <!-- 3. KOD / ÇÖZÜM ALANI -->
            <div id="tab-code" class="hidden flex-1 min-h-0 flex flex-col border border-yellow-500/30 rounded-3xl overflow-hidden animate-fade-in shadow-[0_0_30px_rgba(234,179,8,0.1)]">
                @if($hasTask)
                    <div class="bg-[#1e1e1e] border-b border-white/10 px-4 py-3 flex flex-wrap gap-3 justify-between items-center">
                        <div class="flex items-center gap-3 text-sm text-gray-400">
                            <span class="w-3 h-3 rounded-full bg-red-500"></span><span class="w-3 h-3 rounded-full bg-yellow-500"></span><span class="w-3 h-3 rounded-full bg-green-500"></span>
                            <label for="lang-select" class="ml-2 text-xs uppercase text-gray-300">Dil:</label>
                            <select id="lang-select" onchange="changeLang(this.value)" class="bg-black text-white text-xs rounded-lg border border-white/20 py-1 px-2"></select>
                        </div>
                        <button id="run-code-btn" onclick="runCode()" class="bg-yellow-500 hover:bg-yellow-400 text-black px-8 py-2 rounded-full font-bold transition">
                            ▶ Görevi Test Et
                        </button>
                    </div>
                    <div id="monaco-container" class="flex-1 min-h-[280px] bg-[#1e1e1e] w-full"></div>
                    <div class="bg-black border-t border-white/10 p-5 font-mono text-sm overflow-y-auto max-h-64">
                        <div class="text-gray-400 mb-1 font-bold">Hedefin:</div>
                        <div class="text-white leading-relaxed whitespace-pre-line" id="task-instructions"></div>
                        <div class="mt-3 flex items-center gap-3">
                            <button id="code-hint-btn" onclick="showCodeHint()" class="hidden text-yellow-300 hover:text-yellow-200 font-bold">💡 İpucu</button>
                            <span id="code-hint-text" class="text-yellow-200"></span>
                        </div>
                        <div id="test-results" class="mt-4 space-y-2">
                            <div class="flex items-center gap-2 text-gray-500 bg-gray-900/50 p-3 rounded-lg border border-gray-700">Testler henüz çalıştırılmadı.</div>
                        </div>
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-center p-10 gap-6 bg-black/40">
                        <div class="text-6xl">🏁</div>
                        <p class="text-white text-xl max-w-xl">Bu bölümde kod görevi yok. Quizi bitirdiysen bölümü tamamlayabilirsin.</p>
                        <button onclick="openFinishModal()" class="bg-yellow-500 hover:bg-yellow-400 text-black px-10 py-4 rounded-full font-extrabold transition">Bölümü Bitir</button>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- BÖLÜM SONU MODALI -->
    <div id="level-up-modal" class="fixed inset-0 z-[100] hidden flex-col items-center justify-center bg-slate-950/95 backdrop-blur-md opacity-0 transition-opacity duration-700">
        <div class="absolute w-[500px] h-[500px] bg-yellow-500/20 rounded-full blur-[120px] animate-pulse"></div>
        <div class="relative z-10 flex flex-col items-center text-center transform scale-50 transition-transform duration-1000 ease-out" id="level-up-content">
            <h2 id="finish-title" class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-600 mb-4">BÖLGE AŞILDI!</h2>
            <p id="finish-sub" class="text-gray-300 text-lg mb-2"></p>
            <div class="my-8"><div id="finish-icon" class="w-48 h-48 bg-gradient-to-tr from-yellow-700 via-yellow-500 to-yellow-200 rounded-full flex items-center justify-center text-8xl shadow-[0_0_80px_rgba(234,179,8,0.6)] animate-bounce">🏆</div></div>
            <form action="{{ route('region.submit', $region->id) }}" method="POST">
                @csrf
                <input type="hidden" name="correct" id="form-correct" value="0">
                <input type="hidden" name="total" id="form-total" value="1">
                <button type="submit" class="px-12 py-6 bg-gradient-to-r from-cyan-500 to-blue-600 hover:scale-110 rounded-full text-white font-extrabold text-2xl shadow-[0_0_40px_rgba(34,211,238,0.4)] transition-all">Maceraya Devam Et ⚔️</button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs/loader.min.js"></script>
    <script>
        const cards    = @json($cards);
        const quizData = @json($quizzes);
        const codeTask = @json($hasTask ? $task : null);
        const hasTask  = !!codeTask;
        let firstTryCorrect = 0;

        // ---------- Yardımcılar ----------
        function esc(s) {
            return String(s === null || s === undefined ? '' : s).replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }
        // Güvenli HTML + `kod` biçimlendirmesi
        function fmt(s) {
            return esc(s).replace(/`([^`]+)`/g, '<code class="px-1.5 py-0.5 rounded bg-white/10 text-cyan-300 font-mono text-[0.9em]">$1</code>');
        }

        // ---------- Sekmeler ----------
        function showTab(tabName) {
            ['learn', 'quiz', 'code'].forEach(function (t) {
                document.getElementById('tab-' + t).classList.add('hidden');
                document.getElementById('btn-' + t).classList.replace('border-purple-500/50', 'border-white/10');
            });
            document.getElementById('tab-' + tabName).classList.remove('hidden');
            document.getElementById('btn-' + tabName).classList.replace('border-white/10', 'border-purple-500/50');

            if (tabName === 'code' && hasTask && !window.editorLoaded) { initEditor(); }
        }

        // ---------- 1. Kartlar ----------
        let currentCardIndex = 0;
        function renderCard() {
            const container = document.getElementById('cards-container');
            if (!cards.length) {
                container.innerHTML = '<div class="text-gray-400 text-center pt-20">Bu bölüm için kart bulunamadı.</div>';
                document.getElementById('card-counter').innerText = '0 / 0';
                return;
            }
            let html = '';
            cards.forEach(function (card, index) {
                const offset = index - currentCardIndex;
                if (offset < 0 || offset > 2) return;
                const scale = 1 - offset * 0.04;
                const translateY = offset * 14;
                html += '<div class="absolute inset-0 flex flex-col rounded-3xl border border-white/20 bg-gradient-to-br from-gray-900 to-black shadow-[0_20px_50px_rgba(0,0,0,0.8)] transition-all duration-500 ease-out" ' +
                    'style="transform: translateY(' + translateY + 'px) scale(' + scale + '); z-index: ' + (50 - offset) + ';' + (offset > 0 ? 'pointer-events:none;' : '') + '">' +
                    '<div class="flex items-center gap-4 p-6 pb-3 shrink-0">' +
                    '<div class="w-12 h-12 shrink-0 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-300 text-xl font-black">' + (index + 1) + '</div>' +
                    '<h3 class="text-xl font-black text-white leading-snug">' + fmt(card.title) + '</h3></div>' +
                    '<div class="px-6 pb-6 overflow-y-auto text-gray-300 text-base leading-relaxed whitespace-pre-line">' + fmt(card.desc) + '</div></div>';
            });
            container.innerHTML = html;
            document.getElementById('card-counter').innerText = (currentCardIndex + 1) + ' / ' + cards.length;
        }
        function prevCard() { if (currentCardIndex > 0) { currentCardIndex--; renderCard(); } }
        function nextCard() { if (currentCardIndex < cards.length - 1) { currentCardIndex++; renderCard(); } }
        function speakCard() {
            if (!('speechSynthesis' in window) || !cards[currentCardIndex]) return;
            speechSynthesis.cancel();
            const c = cards[currentCardIndex];
            const u = new SpeechSynthesisUtterance((c.title + '. ' + c.desc).replace(/`/g, ''));
            u.lang = 'tr-TR';
            speechSynthesis.speak(u);
        }

        // ---------- 2. Quiz ----------
        let currentQuizIndex = 0, wrongCount = 0, solved = false;

        function setFeedback(kind, html) {
            const box = document.getElementById('quiz-feedback-box');
            const styles = {
                ok:   'mt-4 w-full max-w-2xl p-4 rounded-2xl border border-green-500 bg-green-900/30 text-green-300 text-left',
                bad:  'mt-4 w-full max-w-2xl p-4 rounded-2xl border border-red-500 bg-red-900/30 text-red-300 text-left',
                hint: 'mt-4 w-full max-w-2xl p-4 rounded-2xl border border-yellow-500 bg-yellow-900/20 text-yellow-200 text-left'
            };
            box.className = styles[kind];
            document.getElementById('quiz-feedback-text').innerHTML = html;
        }

        function renderQuiz() {
            if (!quizData.length) {
                document.getElementById('quiz-question').innerText = 'Bu bölüm için soru bulunamadı.';
                document.getElementById('quiz-progress-text').innerText = 'Soru yok';
                const b = document.getElementById('next-question-btn');
                b.innerText = hasTask ? 'Kod Görevine Geç' : 'Bölümü Bitir';
                b.classList.remove('hidden');
                return;
            }
            const q = quizData[currentQuizIndex];
            wrongCount = 0; solved = false;
            document.getElementById('quiz-counter').innerText = 'Soru ' + (currentQuizIndex + 1) + ' / ' + quizData.length;
            document.getElementById('quiz-progress-text').innerText = currentQuizIndex + ' / ' + quizData.length + ' çözüldü';
            document.getElementById('quiz-question').innerHTML = fmt(q.q);

            const wrap = document.getElementById('quiz-options');
            wrap.innerHTML = '';
            q.options.forEach(function (opt, i) {
                const b = document.createElement('button');
                b.className = 'quiz-btn bg-white/5 border border-white/10 hover:border-cyan-400 p-5 rounded-2xl text-white font-medium text-lg transition shadow-lg';
                b.innerHTML = fmt(opt);
                b.addEventListener('click', function () { checkAnswer(i, b); });
                wrap.appendChild(b);
            });

            document.getElementById('quiz-feedback-box').classList.add('hidden');
            const next = document.getElementById('next-question-btn');
            next.classList.add('hidden');
            next.innerText = (currentQuizIndex === quizData.length - 1)
                ? (hasTask ? 'Pratiğe Geç: Kod Görevi' : 'Bölümü Bitir')
                : 'Sıradaki Soru →';
            document.getElementById('hint-btn').classList.toggle('hidden', !q.hint);
        }

        function showHint() {
            const q = quizData[currentQuizIndex];
            if (q && q.hint) { setFeedback('hint', '💡 <strong>İpucu:</strong> ' + fmt(q.hint)); document.getElementById('quiz-feedback-box').classList.remove('hidden'); }
        }

        function checkAnswer(selectedIndex, btn) {
            if (solved) return;
            const q = quizData[currentQuizIndex];
            const buttons = document.querySelectorAll('.quiz-btn');

            if (selectedIndex === q.correct) {
                solved = true;
                btn.classList.add('bg-green-500/20', 'border-green-500', 'text-green-400');
                buttons.forEach(function (b) { b.disabled = true; });
                if (wrongCount === 0) { firstTryCorrect++; }
                setFeedback('ok', '✨ <strong>Doğru!</strong> ' + fmt(q.explanation || 'Harika bir iş çıkardın.'));
                document.getElementById('next-question-btn').classList.remove('hidden');
            } else {
                wrongCount++;
                btn.classList.add('bg-red-500/20', 'border-red-500', 'text-red-400', 'opacity-50');
                btn.disabled = true;
                let msg = '❌ <strong>Yanlış.</strong> ';
                msg += q.hint ? ('💡 İpucu: ' + fmt(q.hint)) : 'Soruyu tekrar oku ve bir daha dene.';
                if (wrongCount >= 2 && q.explanation) { msg += '<br><br>📘 <strong>Açıklama:</strong> ' + fmt(q.explanation); }
                setFeedback('bad', msg);
            }
            document.getElementById('quiz-feedback-box').classList.remove('hidden');
        }

        function nextQuestion() {
            if (quizData.length && currentQuizIndex < quizData.length - 1) {
                currentQuizIndex++; renderQuiz(); return;
            }
            document.getElementById('quiz-progress-text').innerText = firstTryCorrect + ' / ' + quizData.length + ' ilk denemede doğru';
            if (hasTask) { showTab('code'); } else { openFinishModal(); }
        }

        // ---------- 3. Kod editörü ----------
        const langLabels = { javascript: 'JavaScript', python: 'Python' };
        let availableLangs = ['javascript'];
        let drafts = {};
        let currentLang = 'javascript';

        function defaultStarter(lang) {
            const fn = (codeTask && codeTask.function) ? codeTask.function : 'cozum';
            return lang === 'python'
                ? 'def ' + fn + '(*args):\n    # kodunu buraya yaz\n    pass\n'
                : 'function ' + fn + '(...args) {\n  // kodunu buraya yaz\n}\n';
        }

        function setupTask() {
            if (!hasTask) return;
            const starter = codeTask.starter || {};
            const langs = ['javascript', 'python'].filter(function (l) { return typeof starter[l] === 'string' && starter[l].trim() !== ''; });
            availableLangs = (langs.length && codeTask.function) ? langs : ['javascript'];
            availableLangs.forEach(function (l) { drafts[l] = starter[l] || defaultStarter(l); });
            currentLang = availableLangs[0];

            const sel = document.getElementById('lang-select');
            sel.innerHTML = '';
            availableLangs.forEach(function (l) {
                const o = document.createElement('option');
                o.value = l; o.textContent = langLabels[l] || l;
                sel.appendChild(o);
            });
            sel.disabled = availableLangs.length < 2;

            document.getElementById('task-instructions').innerHTML = fmt(codeTask.instructions || 'Görevin yükleniyor...');
            if (codeTask.hint) { document.getElementById('code-hint-btn').classList.remove('hidden'); }
        }
        function showCodeHint() { document.getElementById('code-hint-text').innerText = codeTask.hint || ''; }

        function initEditor() {
            window.editorLoaded = true;
            require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs' } });
            require(['vs/editor/editor.main'], function () {
                window.codeEditor = monaco.editor.create(document.getElementById('monaco-container'), {
                    value: drafts[currentLang] || '',
                    language: currentLang,
                    theme: 'vs-dark',
                    minimap: { enabled: false },
                    fontSize: 16,
                    automaticLayout: true
                });
            });
        }

        function changeLang(lang) {
            if (!window.codeEditor || lang === currentLang) return;
            drafts[currentLang] = window.codeEditor.getValue();
            currentLang = lang;
            monaco.editor.setModelLanguage(window.codeEditor.getModel(), lang);
            window.codeEditor.setValue(drafts[lang] || defaultStarter(lang));
            if (lang === 'python') { loadPy().catch(function () {}); }
        }

        // JavaScript: Web Worker içinde çalıştır (3 sn zaman aşımı)
        function workerBody() {
            self.onmessage = function (e) {
                var code = e.data.code, fn = e.data.fn, cases = e.data.cases, out = [], f;
                try {
                    f = new Function(code + '\n;return (typeof ' + fn + ' === "function") ? ' + fn + ' : undefined;')();
                } catch (err) { self.postMessage({ fatal: 'Kodda hata var: ' + err }); return; }
                if (!f) { self.postMessage({ fatal: '"' + fn + '" adında bir fonksiyon tanımlamalısın.' }); return; }
                for (var i = 0; i < cases.length; i++) {
                    try {
                        var r = f.apply(null, cases[i].args);
                        out.push({ ok: JSON.stringify(r) === JSON.stringify(cases[i].expected), got: (r === undefined ? 'undefined' : r) });
                    } catch (err) { out.push({ ok: false, error: String(err) }); }
                }
                self.postMessage({ results: out });
            };
        }
        function runJs(code) {
            return new Promise(function (resolve, reject) {
                const url = URL.createObjectURL(new Blob(['(' + workerBody.toString() + ')()'], { type: 'text/javascript' }));
                const worker = new Worker(url);
                const timer = setTimeout(function () {
                    worker.terminate(); URL.revokeObjectURL(url);
                    reject(new Error('Kod 3 saniyede bitmedi (sonsuz döngü olabilir).'));
                }, 3000);
                worker.onmessage = function (e) { clearTimeout(timer); worker.terminate(); URL.revokeObjectURL(url); resolve(e.data); };
                worker.onerror = function (e) { clearTimeout(timer); worker.terminate(); URL.revokeObjectURL(url); reject(new Error(e.message || 'Çalıştırma hatası')); };
                worker.postMessage({ code: code, fn: codeTask.function, cases: codeTask.cases });
            });
        }

        // Python: Pyodide ilk kullanımda yüklenir (birkaç saniye sürer)
        let pyPromise = null;
        function loadPy() {
            if (!pyPromise) {
                pyPromise = new Promise(function (resolve, reject) {
                    const savedDefine = window.define;
                    window.define = undefined; // Monaco'nun AMD yükleyicisi Pyodide'i bozmasın
                    const s = document.createElement('script');
                    s.src = 'https://cdn.jsdelivr.net/pyodide/v0.26.4/full/pyodide.js';
                    s.onload = function () { window.define = savedDefine; loadPyodide().then(resolve, reject); };
                    s.onerror = function () { window.define = savedDefine; pyPromise = null; reject(new Error('Python ortamı yüklenemedi (internet bağlantısını kontrol et).')); };
                    document.head.appendChild(s);
                });
            }
            return pyPromise;
        }
        async function runPython(code) {
            const py = await loadPy();
            py.globals.set('__code', code);
            py.globals.set('__fn', codeTask.function);
            py.globals.set('__cases', JSON.stringify(codeTask.cases));
            const out = await py.runPythonAsync([
                'import json',
                '__ns = {}',
                '__results = []',
                'try:',
                '    exec(__code, __ns)',
                '    __f = __ns.get(__fn)',
                '    if __f is None:',
                '        __out = {"fatal": "\\"" + __fn + "\\" adında bir fonksiyon tanımlamalısın."}',
                '    else:',
                '        for __c in json.loads(__cases):',
                '            try:',
                '                __r = __f(*__c["args"])',
                '                __results.append({"ok": __r == __c["expected"], "got": __r})',
                '            except Exception as __e:',
                '                __results.append({"ok": False, "error": str(__e)})',
                '        __out = {"results": __results}',
                'except Exception as __e:',
                '    __out = {"fatal": "Kodda hata var: " + str(__e)}',
                'json.dumps(__out, default=str)'
            ].join('\n'));
            return JSON.parse(out);
        }

        function renderTestResults(data) {
            const box = document.getElementById('test-results');
            if (data.fatal) {
                box.innerHTML = '<div class="p-3 rounded-lg border border-red-500 bg-red-900/20 text-red-300">⚠️ ' + esc(data.fatal) + '</div>';
                return false;
            }
            let allOk = true, html = '';
            data.results.forEach(function (r, i) {
                const c = codeTask.cases[i];
                const call = esc(codeTask.function + '(' + c.args.map(function (a) { return JSON.stringify(a); }).join(', ') + ')');
                if (r.ok) {
                    html += '<div class="p-3 rounded-lg border border-green-500 bg-green-900/20 text-green-300">✅ Test ' + (i + 1) + ': ' + call + ' → ' + esc(JSON.stringify(c.expected)) + '</div>';
                } else {
                    allOk = false;
                    const got = r.error ? ('Hata: ' + r.error) : ('Senin sonucun: ' + JSON.stringify(r.got));
                    html += '<div class="p-3 rounded-lg border border-red-500 bg-red-900/20 text-red-300">❌ Test ' + (i + 1) + ': ' + call + ' → beklenen ' + esc(JSON.stringify(c.expected)) + '. ' + esc(got) + '</div>';
                }
            });
            box.innerHTML = html;
            return allOk;
        }

        async function runCode() {
            const btn = document.getElementById('run-code-btn');
            const code = window.codeEditor ? window.codeEditor.getValue() : '';
            drafts[currentLang] = code;

            btn.disabled = true; btn.classList.add('opacity-75');
            btn.innerHTML = currentLang === 'python' ? '⏳ Python hazırlanıyor...' : '⏳ Test ediliyor...';

            let passed = false;
            try {
                if (codeTask.cases && codeTask.cases.length && codeTask.function) {
                    const data = currentLang === 'python' ? await runPython(code) : await runJs(code);
                    passed = renderTestResults(data);
                } else {
                    // Eski biçim (regex) için yedek kontrol
                    const clean = code.replace(/\/\/.*/g, '').trim();
                    try { passed = codeTask.validation_regex ? new RegExp(codeTask.validation_regex, 'i').test(code) : clean.length > 2; }
                    catch (e) { passed = clean.length > 2; }
                    document.getElementById('test-results').innerHTML = passed
                        ? '<div class="p-3 rounded-lg border border-green-500 bg-green-900/20 text-green-300">✅ Beklenen kod bulundu.</div>'
                        : '<div class="p-3 rounded-lg border border-red-500 bg-red-900/20 text-red-300">❌ İstenen kod bulunamadı.</div>';
                }
            } catch (err) {
                document.getElementById('test-results').innerHTML = '<div class="p-3 rounded-lg border border-red-500 bg-red-900/20 text-red-300">⚠️ ' + esc(err.message) + '</div>';
            }

            if (passed) {
                btn.innerHTML = '✅ Mükemmel!';
                btn.classList.replace('bg-yellow-500', 'bg-green-500');
                setTimeout(openFinishModal, 1200);
            } else {
                btn.innerHTML = '▶ Tekrar Dene';
                btn.disabled = false; btn.classList.remove('opacity-75');
            }
        }

        // ---------- Bölüm sonu ----------
        function openFinishModal() {
            const total = quizData.length || 1;
            document.getElementById('form-correct').value = firstTryCorrect;
            document.getElementById('form-total').value = total;

            const ok = (firstTryCorrect / total) >= 0.6;
            document.getElementById('finish-title').innerText = ok ? 'BÖLGE AŞILDI!' : 'BİRAZ DAHA ÇALIŞ!';
            document.getElementById('finish-sub').innerText = 'Quiz: ' + firstTryCorrect + ' / ' + total + ' soruyu ilk denemede bildin' + (ok ? '.' : '. Geçmek için en az %60 gerekir; haritaya dönüp bölgeyi tekrar çözebilirsin.');
            document.getElementById('finish-icon').innerText = ok ? '🏆' : '📚';

            const modal = document.getElementById('level-up-modal');
            modal.classList.remove('hidden');
            setTimeout(function () {
                modal.classList.remove('opacity-0');
                document.getElementById('level-up-content').classList.remove('scale-50');
                document.getElementById('level-up-content').classList.add('scale-100');
            }, 50);
        }

        document.addEventListener('DOMContentLoaded', function () {
            renderCard(); renderQuiz(); setupTask();
        });
    </script>
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
    </style>
</x-app-layout>