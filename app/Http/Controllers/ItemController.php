<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ItemRequest;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 商品一覧
     */
    public function index()
    {
        // 商品一覧を取得する
        $items = Item::all();
        return view('items.index', compact('items'));
    }

    /**
     * 商品登録
     */
    public function add(ItemRequest $request)
    {
        $imagePath = null;

        // 画像のアップロード処理
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                $imagePath = base64_encode(file_get_contents($image->getRealPath())); //画像をBase64エンコード
            } catch (\Exception $e) {
                Log::error('画像アップロードエラー:' . $e->getMessage());
                return redirect()->back()->withErrors(['image' => '画像のアップロードエラー']);
            }
        }
        // 商品を登録
        Item::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'type' => $request->type,
            'detail' => $request->detail,
            'image' => $imagePath, //画像パスを保存
        ]);

        return redirect()->route('items.index')->with('success', '商品を登録しました！');
    }


    // 商品更新
    public function update(ItemRequest $request, Item $item)
    {
        $item->name = $request->name;
        $item->type = $request->type;
        $item->detail = $request->detail;

        // 画像の更新処理
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                $item->image = base64_encode(file_get_contents($image->getRealPath()));
            } catch (\Exception $e) {
                Log::error('画像更新エラー:' . $e->getMessage());
                return redirect()->back()->withErrors(['image' => '画像の更新中にエラーが発生しました。']);
            }
        }

        // データベースに更新を反映
        $item->save();

        return redirect()->route('items.index')->with('success', '商品情報を更新しました。');
    }
}
