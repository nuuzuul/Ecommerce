@extends('layouts.admin')
@section('title','Edit Produk')
@section('page-title','Edit Produk')
@section('content')
<form method="POST" action="{{ route('admin.products.update',$product) }}" enctype="multipart/form-data" class="mx-auto max-w-4xl rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">@csrf @method('PUT') @include('admin.products._form')<div class="mt-7 flex gap-3"><button class="btn-primary">Perbarui produk</button><a href="{{ route('admin.products.show',$product) }}" class="btn-secondary">Batal</a></div></form>
@endsection
