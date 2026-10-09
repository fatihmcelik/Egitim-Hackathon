<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'KodMacerası') }} - Öğren, Keşfet, Geliş, Oyna!</title>
    <!-- El yazısı fontu -->
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- YENİ: Sayfaya can katacak özel CSS animasyonları -->
    <style>
        /* Arka planın yavaşça hareket etmesi (Nefes alma efekti) */
        @keyframes bg-pan {
            0% { background-position: center top; background-size: 105%; }
            100% { background-position: center bottom; background-size: 110%; }
        }
        .animate-bg {
            animation: bg-pan 30s ease-in-out infinite alternate;
        }
        /* Elementlerin havada süzülme efekti */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .animate-float-delayed {
            animation: float 7s ease-in-out infinite 2s;
        }
    </style>
</head>
<body class="bg-[#050810] min-h-screen p-4 md:p-6 flex flex-col font-sans text-white overflow-x-hidden">
    
    <!-- ANA KART (Artık ekrana mükemmel sığıyor ve esnek) -->
    <div class="relative w-full flex-1 rounded-[2.5rem] border border-white/10 shadow-[0_0_50px_rgba(0,0,0,0.5)] overflow-hidden flex flex-col group">
        
        <!-- HAREKETLİ ARKA PLAN GÖRSELİ -->
        <div class="absolute inset-0 bg-no-repeat animate-bg" 
             style="background-image: url('{{ asset('images/giris.png') }}');"></div>
        
        <!-- KARANLIK GRADYAN (Yazıların okunması için) -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#0B1120] via-[#0B1120]/75 to-transparent"></div>

        <!-- İÇERİK (justify-between ile üst, orta ve alt kısımlar ezilmeden dağıtıldı) -->
        <div class="relative z-10 flex flex-col h-full justify-between min-h-[85vh]">
            
            <!-- ÜST MENÜ (Gereksizler atıldı, sadece Giriş/Kayıt kaldı) -->
            <nav class="flex justify-between items-center px-6 md:px-12 py-6">
                <div class="flex items-center gap-3 animate-float">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center font-black text-white shadow-[0_0_15px_rgba(37,99,235,0.5)]">
                        AI
                    </div>
                    <span class="text-2xl font-bold tracking-wide">Kod<span class="text-blue-400">Macerası</span></span>
                </div>

                <div class="flex gap-3 md:gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 rounded-xl font-bold transition shadow-[0_0_15px_rgba(37,99,235,0.4)]">
                                Maceraya Dön
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-5 py-2.5 border border-white/20 hover:bg-white/10 rounded-xl font-bold transition backdrop-blur-md hidden md:block">
                                Giriş Yap
                            </a>
                            <a href="{{ route('register') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 rounded-xl font-bold transition shadow-[0_0_15px_rgba(37,99,235,0.4)]">
                                Kayıt Ol
                            </a>
                        @endauth
                    @endif
                </div>
            </nav>

            <!-- ORTA ALAN (Metin ve Aksiyon Butonları) -->
            <div class="px-6 md:px-12 flex flex-col justify-center max-w-3xl py-8">
                <h1 class="text-5xl md:text-[5.5rem] font-black leading-[1.1] mb-6 drop-shadow-2xl">
                    Öğren, Keşfet, <br>
                    <!-- YENİ: Geliş, Oyna metni parlak gradyanlı ve hareketli -->
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#38BDF8] to-blue-600 animate-pulse inline-block mt-2">
                        Geliş, Oyna!
                    </span>
                </h1>
                <p class="text-lg md:text-xl text-gray-300 mb-10 leading-relaxed max-w-lg">
                    Yapay zeka destekli, kişiselleştirilmiş öğrenme macerana katıl. Her konu bir yeni dünya, her başarı daha güçlü bir sen!
                </p>
                
                <div class="flex flex-wrap gap-4 relative">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-4 px-8 md:px-10 rounded-2xl shadow-[0_0_30px_rgba(37,99,235,0.4)] text-lg transition-transform hover:scale-105 flex items-center gap-2">
                            ⚔️ Haritaya Git
                        </a>
                    @else
                        <!-- YENİ: Buton doğrudan Register (Kayıt Ol) sayfasına bağlandı -->
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-4 px-8 md:px-10 rounded-2xl shadow-[0_0_30px_rgba(37,99,235,0.4)] text-lg transition-transform hover:scale-105 flex items-center gap-2 z-20 relative">
                            🚀 Hemen Başla
                        </a>
                    @endauth

                    <!-- El Yazısı (Düzgün yerleştirildi ve yukarı/aşağı süzülüyor) -->
                    <div class="absolute -right-4 md:right-10 top-0 transform -rotate-12 animate-float-delayed pointer-events-none hidden sm:block z-10">
                        <span class="font-['Caveat'] text-3xl md:text-4xl text-blue-300 drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                            Her Konu <br> Yeni Bir Macera!
                        </span>
                    </div>
                </div>
            </div>

            <!-- ALT ÖZELLİKLER BARI (Daha kompakt hale getirildi, sığmama sorunu çözüldü) -->
            <div class="mx-4 md:mx-12 mb-6 bg-[#0B1120]/75 backdrop-blur-xl border border-white/10 rounded-2xl p-4 md:p-5 grid grid-cols-2 lg:grid-cols-4 gap-4 relative z-30">
                
                <div class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/20 flex shrink-0 items-center justify-center text-purple-400 group-hover:rotate-12 group-hover:scale-110 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </div>
                    <div class="text-white text-xs md:text-sm font-bold leading-tight">AI Destekli <br><span class="text-gray-400 font-normal">Kişisel Haritalar</span></div>
                </div>

                <div class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex shrink-0 items-center justify-center text-blue-400 group-hover:rotate-12 group-hover:scale-110 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="text-white text-xs md:text-sm font-bold leading-tight">Oyunlaştırılmış <br><span class="text-gray-400 font-normal">Deneyim</span></div>
                </div>

                <div class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/20 flex shrink-0 items-center justify-center text-cyan-400 group-hover:rotate-12 group-hover:scale-110 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <div class="text-white text-xs md:text-sm font-bold leading-tight">XP ve Unvan <br><span class="text-gray-400 font-normal">Sistemi</span></div>
                </div>

                <div class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gray-500/20 flex shrink-0 items-center justify-center text-gray-300 group-hover:rotate-12 group-hover:scale-110 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div class="text-white text-xs md:text-sm font-bold leading-tight">Öğretmen <br><span class="text-gray-400 font-normal">Paneli</span></div>
                </div>
                
            </div>
        </div>
    </div>
    
</body>
</html>