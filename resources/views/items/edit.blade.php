@extends('adminlte::page')

@section('title', '商品編集')

@section('content_header')
<h1>商品編集</h1>
@stop

@section('content')
<form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="form-group">
        <label for="name">商品名</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ $item->name }}" required maxlength="255">
    </div>

    <div class="form-group">
        <label for="type">種別</label>
        <input type="text" class="form-control" id="type" name="type" value="{{ $item->type }}" maxlength="255">
    </div>

    <div class="form-group">
        <label for="detail">詳細</label>
        <textarea class="form-control" id="detail" name="detail" maxlength="1000">{{ $item->detail }}</textarea>
    </div>

    <div class="form-group">
        <label for="image">画像</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*">
        @if ($item->image)
        <div class="mt-2">
            <img src="data:image/{{ $item->image_format }};base64,{{ $item->image }}" width="100" alt="商品画像">
        </div>
        @else
        <p>画像は登録されていません。</p>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">更新</button>
</form>

<form action="{{ route('items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">削除</button>
</form>
@stop