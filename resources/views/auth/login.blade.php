<x-guest-layout>
    <div class="mb-6 text-center"><h1 class="text-2xl font-black text-stone-900">Selamat datang</h1><p class="mt-2 text-sm text-stone-500">Masuk ke akun Kanrejawataa.</p></div>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <form method="POST" action="{{ route('login') }}" class="space-y-4">@csrf
        <div><label class="form-label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" class="form-input" required autofocus>@error('email')<p class="form-error">{{ $message }}</p>@enderror</div>
        <div><label class="form-label" for="password">Password</label><input id="password" type="password" name="password" class="form-input" required>@error('password')<p class="form-error">{{ $message }}</p>@enderror</div>
        <div class="flex items-center justify-between"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" class="rounded border-stone-300 text-amber-600"> Ingat saya</label>@if(Route::has('password.request'))<a href="{{ route('password.request') }}" class="text-sm font-bold text-amber-700">Lupa password?</a>@endif</div>
        <button class="btn-primary w-full">Masuk</button>
        <p class="text-center text-sm text-stone-500">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-amber-700">Daftar</a></p>
    </form>
</x-guest-layout>
