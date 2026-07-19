@extends('layouts.admin')
@section('title','Tambah Produk')
@section('page-title','Tambah Produk')
@section('content')
<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="mx-auto max-w-4xl rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">@csrf @include('admin.products._form')<div class="mt-7 flex gap-3"><button class="btn-primary">Simpan produk</button><a href="{{ route('admin.products.index') }}" class="btn-secondary">Batal</a></div></form>
@endsection
