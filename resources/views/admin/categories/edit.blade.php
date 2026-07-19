@extends('layouts.admin')
@section('title','Edit Kategori')
@section('page-title','Edit Kategori')
@section('content')<form method="POST" action="{{ route('admin.categories.update',$category) }}" class="mx-auto max-w-2xl rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">@csrf @method('PUT') @include('admin.categories._form')<div class="mt-6 flex gap-3"><button class="btn-primary">Perbarui kategori</button><a href="{{ route('admin.categories.index') }}" class="btn-secondary">Batal</a></div></form>@endsection
