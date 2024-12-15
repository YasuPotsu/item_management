<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Laravelの認証ルートを設定
Auth::routes();

// ホーム画面
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// 商品一覧ページ
Route::get('items', [App\Http\Controllers\ItemController::class, 'index'])->name('items.index');

// 商品登録ページ
Route::get('items/add',  [App\Http\Controllers\ItemController::class, 'add'])->name('items.add');

// 商品登録処理
Route::post('items',  [App\Http\Controllers\ItemController::class, 'store'])->name('items.store');

// 商品画像ページ
Route::get('/items/{id}', [App\Http\Controllers\ItemController::class, 'show'])->name('items.show');

// 商品編集ページ(Edit)
Route::get('items/{id}/edit',  [App\Http\Controllers\ItemController::class, 'edit'])->name('items.edit');

// 商品更新処理(Update)
Route::put('items/{id}',  [App\Http\Controllers\ItemController::class, 'update'])->name('items.update');

// 商品削除処理(Destroy)
Route::delete('items/{id}',  [App\Http\Controllers\ItemController::class, 'destroy'])->name('items.destroy');



