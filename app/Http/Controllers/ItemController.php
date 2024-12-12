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
        // POSTリクエストのとき
        if ($request->isMethod('post')) {
            $imagePath = null;
            
            // 画像のアップロード
            try {
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $imagePath = base64_encode(file_get_contents($image->getRealPath())); //画像を保存
                }
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['image' => '画像のアップロードエラー']);
            }

            // 商品を登録
            Item::create([
                'user_id' => Auth::user()->id,
                'name' => $request->name,
                'type' => $request->type,
                'detail' => $request->detail,
                'image' => $imagePath, //画像パスを保存
            ]);

            return redirect('/items')->with('success', '商品を登録しました！');
        }

        return view('items.add');
    }

    // 商品編集フォーム
    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    // 商品更新処理
    public function update(ItemRequest $request, Item $item)
    {
        $item->name = $request->name;
        $item->type = $request->type;
        $item->detail = $request->detail;
        $item->save();

        // 画像更新
        try {
            if ($request->hasFile('image')) {
                if ($item->image) {
                    try {
                        Storage::disk('public')->delete($item->image);
                    } catch (\Exception $e) {
                        return redirect()->back()->withErrors(['image' => '古い画像の削除中にエラーが発生しました。']);
                    }
                }
                $item->image = base64_encode(file_get_contents($image->getRealPath()));
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['image' => '画像の更新中にエラーが発生しました。']);
        }

        $item->update($request->all());
        return redirect()->route('items.index')->with('success', '商品情報を更新しました。');
    }
}
