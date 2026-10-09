<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kayıt Ol - KodMacerası</title>
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
    
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-20%] right-[-10%] w-[500px] h-[500px] rounded-full bg-cyan-600/20 blur-[120px]"></div>
        <div class="absolute bottom-[-20%] left-[-10%] w-[600px] h-[600px] rounded-full bg-purple-600/20 blur-[150px]"></div>
        <div class="absolute inset-0 bg-grid z-0"></div>
    </div>

    <div class="relative z-10 w-full max-w-md p-8 bg-black/40 backdrop-blur-xl border border-cyan-500/30 rounded-3xl shadow-[0_0_40px_rgba(34,211,238,0.15)] my-8">
        
        <div class="text-center mb-8">
            <a href="/" class="text-3xl font-extrabold tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-500">
                KODMACERASI
            </a>
            <p class="text-gray-400 mt-2 text-sm">Arenaya katılmak için profilini oluştur.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Kahraman Adı (Kullanıcı Adı)</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                       class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all">
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-400 text-sm" />
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">E-Posta Adresi</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" 
                       class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-all">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Şifre</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" 
                       class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:border-purple-400 focus:ring-1 focus:ring-purple-400 transition-all">
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Şifre Tekrar</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                       class="w-full bg-black/50 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-gray-600 focus:border-purple-400 focus:ring-1 focus:ring-purple-400 transition-all">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-400 text-sm" />
            </div>

            <button type="submit" class="w-full py-4 bg-gradient-to-r from-purple-600 to-cyan-500 rounded-xl font-bold text-lg text-white shadow-[0_0_20px_rgba(34,211,238,0.4)] hover:shadow-[0_0_30px_rgba(168,85,247,0.6)] transition-all transform hover:-translate-y-1 mt-4">
                Maceraya Katıl
            </button>

            <p class="text-center text-gray-400 text-sm mt-6">
                Zaten bir hesabın var mı? <a href="{{ route('login') }}" class="text-cyan-400 hover:text-cyan-300 font-bold">Giriş Yap</a>
            </p>
        </form>
    </div>
</body>
</html>