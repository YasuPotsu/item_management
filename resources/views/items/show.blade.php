@extends('adminlte::page')

@section('title', '商品画像')

@section('content_header')
<h1>商品画像</h1>
@stop

@section('content')
<div class="container">
    <h2>商品名：{{ $item->name }}</h2>
    <p>種別：{{ $item->type }}</p>
    <p>詳細：{{ $item->detail }}</p>

    @if($image)
        <img src="{{ $image }}" alt="{{ $item->name }}" style="width: 300px; height: auto;">
    @else
        <p>画像は登録されていません。</p>
    @endif

    <div class="mt-3">
    <a href="{{ route('items.index') }}" class="btn btn-primary">商品一覧に戻る</a>
    </div>
</div>
@endsection
