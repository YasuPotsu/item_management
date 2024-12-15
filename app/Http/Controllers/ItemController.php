<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ItemRequest;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Support\Facades\Log;

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
    // 商品登録画面の表示
    public function add()
    {
        return view('items.add');
    }

    public function store(ItemRequest $request)
    {
        $imagePath = null;
        $imageFormat = null;

        // 画像のアップロード処理
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                $imagePath = $image->store('public/images');
                $imageFormat = $image->getClientOriginalExtension(); // 画像形式を取得(例:'jpeg','png','gif')
            } catch (\Exception $e) {
                Log::error('画像アップロードエラー:' . $e->getMessage(), ['request' => $request->all()]);
                return redirect()->back()->withErrors(['image' => '画像のアップロードエラー']);
            }
        }

        // 商品データを登録
        Item::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'type' => $request->type,
            'detail' => $request->detail,
            'image' => $imagePath, //画像パスを保存
            'image_format' => $imageFormat, // 画像形式を保存
        ]);

        return redirect()->route('items.index')->with('success', '商品を登録しました！');
    }
    // 商品画像ページ
    public function show($id)
    {
        $item = Item::findOrFail($id);
        $image = $item->image ? Storage::url($item->image) : null;

        return view('items.show', compact('item', 'image'));
    }

    // 商品データの編集
    public function edit($id)
    {
        //  指定されたIDの商品を取得
        $item = Item::findOrFail($id);

        // edit.blade.phpに$itemを渡す
        return view('items.edit', compact('item'));
    }

    // 商品データの更新
    public function update(ItemRequest $request, $id)
    {
        Log::info('更新処理開始: ID=' . $id);

        try {
            // IDでアイテムを取得
            $item = Item::findOrFail($id);
            Log::info('アイテム取得成功: ' . $item->name);

            // 認可チェック
            if ($item->user_id !== Auth::id()) {
                Log::warning('認可エラー: ユーザーID=' . Auth::id() . 'がアイテムID=' . $id . ' を編集しようとしました。');
                abort(403, 'この行為は許可されていません。');
            }

            // フィールドの更新
            $item->name = $request->name;
            $item->type = $request->type;
            $item->detail = $request->detail;

            // 画像の更新処理
            if ($request->hasFile('image')) {
                try {
                    // 古い画像を削除
                    if ($item->image) {
                        Storage::delete($item->image);
                    }

                    // 新しい画像を保存
                    $image = $request->file('image');
                    $imagePath = $image->store('public/images');
                    $imageFormat = $image->getClientOriginalExtension(); //拡張子を保存
                    $item->image = $imagePath;
                    $item->image_format = $imageFormat;
                } catch (\Exception $e) {
                    Log::error('画像更新エラー:' . $e->getMessage(), ['request' => $request->all()]);
                    return redirect()->back()->withErrors(['image' => '画像の更新中にエラーが発生しました。']);
                }
            }

            // save()を使ってデータベースに更新を保存
            $item->save();
            Log::info('更新処理完了: ID=' . $id);

            return redirect()->route('items.index')->with('success', '商品情報を更新しました。');
        } catch (\Exception $e) {
            Log::error('更新処理中にエラー発生: ' . $e->getMessage());
            Log::info('フォーム送信内容:', $request->all());
            Log::info('更新対象アイテム:', $item->toArray());

            return redirect()->back()->withErrors(['error' => '更新処理中にエラーが発生しました。もう一度お試しください',]);
        }
    }

    // 商品削除
    public function destroy($id)
    {
        try {
            // 指定したIDの商品を取得
            $item = Item::findOrFail($id);

            // ユーザーに紐づいているか確認
            if ($item->user_id !== Auth::id()) {
                return redirect()->route('items.index')->withErrors(['error' => 'この商品を削除する権限がありません。']);
            }

            // 画像を削除
            if ($item->image && Storage::exists($item->image)) {
                Storage::delete($item->image);
            }
            
            
            // 商品データを削除
            $item->delete();

            return redirect()->route('items.index')->with('success', '商品が削除されました。');
        } catch (\Exception $e) {
            Log::error('商品削除エラー：' . $e->getMessage(), ['item_id' => $id]);
            return redirect()->route('items.index')->withErrors(['error' => '商品削除中にエラーが発生しました。']);
        }
    }

    // 画像更新処理(リファクタリング)
    private function updateImage(Item $item, $image)
    {
        if ($item->image && Storage::exists($item->image)) {
            Storage::delete($item->image); // 古い画像を削除
        }

        $item->image = $image->store('public/images');
        $item->image_format = $image->getClientOriginalExtension();
    }
}
