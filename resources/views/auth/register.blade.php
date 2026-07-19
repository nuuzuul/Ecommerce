<x-guest-layout>
    <div class="mb-6 text-center"><h1 class="text-2xl font-black text-stone-900">Daftar sebagai pembeli</h1><p class="mt-2 text-sm text-stone-500">Buat akun untuk mulai memesan kue Kanrejawataa.</p></div>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">@csrf
        <div><label class="form-label" for="name">Nama lengkap</label><input id="name" name="name" value="{{ old('name') }}" class="form-input" required autofocus>@error('name')<p class="form-error">{{ $message }}</p>@enderror</div>
        <div><label class="form-label" for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" class="form-input" required>@error('email')<p class="form-error">{{ $message }}</p>@enderror</div>
        <div><label class="form-label" for="phone">Nomor telepon</label><input id="phone" name="phone" value="{{ old('phone') }}" class="form-input">@error('phone')<p class="form-error">{{ $message }}</p>@enderror</div>
        <div><label class="form-label" for="address">Alamat</label><textarea id="address" name="address" rows="3" class="form-input">{{ old('address') }}</textarea>@error('address')<p class="form-error">{{ $message }}</p>@enderror</div>
        <div><label class="form-label" for="password">Password</label><input id="password" type="password" name="password" class="form-input" required>@error('password')<p class="form-error">{{ $message }}</p>@enderror</div>
        <div><label class="form-label" for="password_confirmation">Ulangi password</label><input id="password_confirmation" type="password" name="password_confirmation" class="form-input" required></div>
        <button class="btn-primary w-full">Daftar</button>
        <p class="text-center text-sm text-stone-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-amber-700">Masuk</a></p>
    </form>
</x-guest-layout>
