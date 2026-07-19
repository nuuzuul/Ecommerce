<section>
    <header><h2 class="text-xl font-black text-stone-900">Informasi profil</h2><p class="mt-1 text-sm text-stone-500">Perbarui nama, email, nomor telepon, dan alamat akun.</p></header>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">@csrf @method('patch')
        <div><label class="form-label" for="name">Nama</label><input id="name" name="name" class="form-input" value="{{ old('name',$user->name) }}" required>@error('name')<p class="form-error">{{ $message }}</p>@enderror</div>
        <div><label class="form-label" for="email">Email</label><input id="email" type="email" name="email" class="form-input" value="{{ old('email',$user->email) }}" required>@error('email')<p class="form-error">{{ $message }}</p>@enderror</div>
        <div><label class="form-label" for="phone">Nomor telepon</label><input id="phone" name="phone" class="form-input" value="{{ old('phone',$user->phone) }}">@error('phone')<p class="form-error">{{ $message }}</p>@enderror</div>
        <div><label class="form-label" for="address">Alamat</label><textarea id="address" name="address" rows="4" class="form-input">{{ old('address',$user->address) }}</textarea>@error('address')<p class="form-error">{{ $message }}</p>@enderror</div>
        <div class="flex items-center gap-4"><button class="btn-primary">Simpan profil</button>@if(session('status')==='profile-updated')<p x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,2000)" class="text-sm font-semibold text-emerald-600">Tersimpan.</p>@endif</div>
    </form>
</section>
