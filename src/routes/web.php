<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// ==========================
// ログイン不要（公開ページ）
// ==========================
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

// ==========================
// ログイン
// ==========================
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'store']);

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// ==========================
// 会員登録
// ==========================
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

// ==========================
// ログイン必須ページ
// ==========================
Route::middleware(['auth'])->group(function () {

    // マイページ
    Route::get('/mypage', [ProfileController::class, 'show'])->name('profile.show');

    // プロフィール編集（← これを追加）
    Route::get('/mypage/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/mypage/edit', [ProfileController::class, 'update'])->name('profile.update');


    // 初回プロフィール設定
    Route::get('/mypage/setup', [ProfileController::class, 'setup'])->name('profile.setup');
    Route::post('/mypage/setup', [ProfileController::class, 'storeInitial'])->name('profile.storeInitial');

    // 商品出品（仕様準拠）
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    // いいね
    Route::post('/like/{item}', [LikeController::class, 'store'])->name('like.store');
    Route::delete('/like/{item}', [LikeController::class, 'destroy'])->name('like.destroy');

    // コメント
    Route::post('/comment/{item}', [CommentController::class, 'store'])->name('comment.store');

    // ==========================
    // 購入完了（★ DB反映あり）
    // ==========================
    Route::get('/purchase/complete', [PurchaseController::class, 'complete'])
        ->name('purchase.complete');


    // ==========================
    // 購入フロー
    // ==========================
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    // 住所変更
    Route::get('/purchase/{item}/address', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::put('/purchase/{item}/address', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
});
