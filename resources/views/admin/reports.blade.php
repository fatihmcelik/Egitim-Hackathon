<x-app-layout>
    <div class="max-w-7xl mx-auto space-y-6 py-8">
        
        <div class="bg-black/40 backdrop-blur-xl border border-white/10 p-6 rounded-3xl flex flex-col md:flex-row justify-between items-center shadow-2xl">
            <div>
                <h1 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-orange-500">Sistem Yöneticisi Paneli</h1>
                <p class="text-gray-400 text-sm mt-1">Öğrenciler tarafından hatalı veya uygunsuz olarak raporlanan içerikler.</p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="bg-red-500/20 border border-red-500/50 px-4 py-2 rounded-xl text-red-400 font-bold flex items-center gap-2">
                    <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span></span>
                    {{ $reports->where('status', 'pending')->count() }} Bekleyen Rapor
                </div>
            </div>
        </div>

        <div class="bg-black/40 border border-white/10 rounded-3xl overflow-hidden shadow-2xl p-6">
            <h3 class="text-xl font-bold text-white mb-6 border-b border-white/10 pb-4">Aktif Şikayetler</h3>
            
            <div class="space-y-4">
                @forelse($reports as $report)
                    <div class="bg-white/5 border border-red-500/30 p-5 rounded-2xl flex flex-col md:flex-row gap-6 justify-between items-start md:items-center hover:bg-white/10 transition">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="bg-red-500/20 text-red-400 text-xs font-bold px-3 py-1 rounded-full border border-red-500/30">Şikayet ID: #{{ $report->id }}</span>
                                <span class="text-gray-400 text-sm">Bölge: <strong>{{ $report->question->region->name ?? 'Bilinmiyor' }}</strong></span>
                            </div>
                            <p class="text-white font-medium mb-1">{{ $report->reason }}</p>
                            <p class="text-xs text-gray-500">Raporlayan: {{ $report->user->name ?? 'Anonim' }} ({{ $report->created_at->diffForHumans() }})</p>
                        </div>
                        <div class="flex gap-2 w-full md:w-auto">
                            <button class="flex-1 md:flex-none bg-black/50 hover:bg-green-500/20 text-green-400 border border-green-500/30 px-4 py-2 rounded-xl transition text-sm font-bold">İncele</button>
                            <button class="flex-1 md:flex-none bg-black/50 hover:bg-red-500/20 text-red-400 border border-red-500/30 px-4 py-2 rounded-xl transition text-sm font-bold">Kapat</button>
                        </div>
                    </div>
                @empty
                    <p class="text-green-400 font-bold p-4 bg-green-900/20 rounded-xl border border-green-500/30 text-center">Şu an sistemde bekleyen hiçbir rapor bulunmuyor. Her şey yolunda!</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>