<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// 公開
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

// ログイン
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// 会員登録
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

// メール認証
require __DIR__ . '/auth.php';

// ★ 購入完了（Stripe/コンビニ共通・必ず到達）
Route::get('/purchase/complete', [PurchaseController::class, 'complete'])
    ->name('purchase.complete');

// 認証必須
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/mypage', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/mypage/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/mypage/edit', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/mypage/setup', [ProfileController::class, 'setup'])->name('profile.setup');
    Route::post('/mypage/setup', [ProfileController::class, 'storeInitial'])->name('profile.storeInitial');

    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    Route::post('/like/{item}', [LikeController::class, 'store'])->name('like.store');
    Route::delete('/like/{item}', [LikeController::class, 'destroy'])->name('like.destroy');

    Route::post('/comment/{item}', [CommentController::class, 'store'])->name('comment.store');

    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    Route::get('/purchase/{item}/address', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::put('/purchase/{item}/address', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
});
