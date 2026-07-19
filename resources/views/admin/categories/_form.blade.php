<div class="space-y-5">
    <div><label class="form-label">Nama kategori</label><input name="name" value="{{ old('name',$category->name ?? '') }}" class="form-input">@error('name')<p class="form-error">{{ $message }}</p>@enderror</div>
    <div><label class="form-label">Deskripsi</label><textarea name="description" rows="4" class="form-input">{{ old('description',$category->description ?? '') }}</textarea>@error('description')<p class="form-error">{{ $message }}</p>@enderror</div>
    <label class="flex items-start gap-3 rounded-2xl border border-stone-200 p-4"><input type="checkbox" name="uses_variants" value="1" @checked(old('uses_variants',$category->uses_variants ?? false)) class="mt-1 rounded border-stone-300 text-amber-600"><span><b>Gunakan ukuran produk</b><small class="block text-stone-500">Aktifkan untuk Kue Kering. Produk wajib memiliki ukuran 500 gram dan 1 kg.</small></span></label>
    <label class="flex items-center gap-3"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$category->is_active ?? true)) class="rounded border-stone-300 text-amber-600"><span class="font-semibold">Kategori aktif</span></label>
</div>
