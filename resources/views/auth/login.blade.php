<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giriş Yap - KodMacerası</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .bg-grid {
            background-size: 50px 50px;
            background-image: 
                linear-gradient(to right, rgba(168, 85, 247, 0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(168, 85, 247, 0.1) 1px, transparent 1px);
        }
    </style>
</head>
<body class="bg-slate-950 text-white font-sans antialiased flex items-center justify-center min-h-screen">
    
    <!-- Animasyonlu Arka Plan Işıkları -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-purple-600/20 blur-[120px]"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[600px] h-[600px] rounded-full bg-cyan-600/20 blur-[150px]"></div>
        <div class="absolute inset-0 bg-grid z-0"></div>
    </div>

    <div class="relative z-10 w-full max-w-md p-8 bg-black/40 backdrop-blur-xl border border-purple-500/30 rounded-3xl shadow-[0_0_40px_rgba(168,85,247,0.15)]">
        
        <div class="text-center mb-8">
            <a href="/" class="text-3xl font-extrabold tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-500">
                KODMACERASI
            </a>
            <p class="text-gray-400 mt-2 text-sm">Sisteme giriş yap ve maceraya kaldığın yerden devam et.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">E-Posta Adresi</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                       class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Şifre</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" 
                       class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:border-purple-400 focus:ring-1 focus:ring-purple-400 transition-all">
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between text-sm">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-white/10 bg-black/50 text-purple-500 shadow-sm focus:ring-purple-500">
                    <span class="ms-2 text-gray-400">Beni Hatırla</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-cyan-400 hover:text-cyan-300 transition-colors" href="{{ route('password.request') }}">
                        Şifremi Unuttum
                    </a>
                @endif
            </div>

            <button type="submit" class="w-full py-4 bg-gradient-to-r from-cyan-500 to-purple-600 rounded-xl font-bold text-lg text-white shadow-[0_0_20px_rgba(168,85,247,0.4)] hover:shadow-[0_0_30px_rgba(34,211,238,0.6)] transition-all transform hover:-translate-y-1">
                Giriş Yap
            </button>

            <p class="text-center text-gray-400 text-sm mt-6">
                Hesabın yok mu? <a href="{{ route('register') }}" class="text-purple-400 hover:text-purple-300 font-bold">Kayıt Ol</a>
            </p>
        </form>
    </div>
</body>
</html>