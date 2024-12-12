@extends('adminlte::page')

@section('title', '商品編集')

@section('content_header')
<h1>商品編集</h1>
@stop

@section('content')
<form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="name">商品名</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ $item->name }}">
    </div>

    <div class="form-group">
        <label for="type">種別</label>
        <input type="text" class="form-control" id="type" name="type" value="{{ $item->type }}">
    </div>

    <div class="form-group">
        <label for="detail">詳細</label>
        <textarea class="form-control" id="detail" name="detail">{{ $item->detail }}</textarea>
    </div>

    <div class="form-group">
        <label for="image">画像</label>
        <input type="file" class="form-control" id="image" name="image">
        @if ($item->image)
        <img src="{{ asset('storage/'. $item->image) }}" width="100" alt="商品画像">
        @endif
    </div>


    <button type="submit" class="btn btn-primary">更新</button>
</form>
@stop