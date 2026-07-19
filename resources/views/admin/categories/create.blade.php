@extends('layouts.admin')
@section('title','Tambah Kategori')
@section('page-title','Tambah Kategori')
@section('content')<form method="POST" action="{{ route('admin.categories.store') }}" class="mx-auto max-w-2xl rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">@csrf @include('admin.categories._form')<div class="mt-6 flex gap-3"><button class="btn-primary">Simpan kategori</button><a href="{{ route('admin.categories.index') }}" class="btn-secondary">Batal</a></div></form>@endsection
