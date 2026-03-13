<!doctype html>
<html lang="en">
<head>
    <title>Login – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="min-h-screen bg-gradient-to-br from-brand-500 via-brand-400 to-teal-600 flex flex-col items-center justify-center px-5">

    {{-- Logo / Brand --}}
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-3xl mb-4 shadow-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white tracking-tight">WMS Mobile</h1>
        <p class="text-white/70 text-sm mt-1">Trans Kargo Solusindo</p>
    </div>

    {{-- Card --}}
    <div class="w-full max-w-sm bg-white rounded-3xl shadow-2xl p-7">
        <h2 class="text-xl font-bold text-slate-800 mb-1">Selamat Datang 👋</h2>
        <p class="text-sm text-slate-500 mb-6">Masuk ke akun Anda untuk melanjutkan.</p>

        @if(session('error'))
            <div class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('auth.mobile.login') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Username --}}
            <div>
                <label for="username" class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Username</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    </span>
                    <input type="text" id="username" name="username" placeholder="Masukkan username"
                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white transition-all duration-200"
                           value="{{ old('username') }}" autocomplete="username" required>
                </div>
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Password</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                    </span>
                    <input type="password" id="password" name="password" placeholder="Masukkan password"
                           class="w-full pl-10 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white transition-all duration-200"
                           autocomplete="current-password" required>
                    <button type="button" id="toggle-pw" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg id="eye-show" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <svg id="eye-hide" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                    </button>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full py-3.5 bg-brand-400 hover:bg-brand-500 text-white font-semibold text-sm rounded-xl shadow-lg shadow-brand-400/30 transition-all duration-200 active:scale-[.98]">
                Masuk ke Sistem
            </button>
        </form>
    </div>

    <p class="mt-8 text-white/50 text-xs text-center">© {{ date('Y') }} Trans Kargo Solusindo</p>

    <script>
        const btn = document.getElementById('toggle-pw');
        const pw  = document.getElementById('password');
        const s   = document.getElementById('eye-show');
        const h   = document.getElementById('eye-hide');
        btn.addEventListener('click', () => {
            const show = pw.type === 'password';
            pw.type = show ? 'text' : 'password';
            s.classList.toggle('hidden', show);
            h.classList.toggle('hidden', !show);
        });
    </script>
</body>
</html>
