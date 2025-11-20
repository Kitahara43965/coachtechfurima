<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerifyEmailController;



Route::get('/', [ItemController::class, 'index'])
    ->name('index');
Route::get('/item/{item_id}',[DisplayController::class, 'evaluate'])
    ->name('item.item_id');
Route::get('/register', [RegisterController::class, 'show'])
    ->name('register');
Route::post('/register', [RegisterController::class, 'store'])
    ->name('register.store');
Route::get('/login', [LoginController::class, 'show'])
    ->name('login');
Route::post('/login', [LoginController::class, 'store'])
    ->name('login.store');


Route::middleware(['auth'])->group(function () {
    // 認証待ちページ（未認証ユーザー用）
    Route::get('/email/verify', [VerifyEmailController::class, 'emailVerify'])
        ->name('verification.notice');
    Route::post('/verify-email', [VerifyEmailController::class,'verifyEmail'])
        ->name('verification.manual');
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
});

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class,'emailVerifyIdHash'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');


Route::middleware(['auth','verified'])->group(function () {
    Route::get('/mypage/profile', [DisplayController::class, 'profile'])
        ->name('mypage.profile');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])
        ->name('mypage.profile.update');
    Route::get('/profile/count-images', [ProfileController::class, 'countImages'])
        ->name('profile.image.count');
});

Route::middleware(['auth','verified','profile.complete'])->group(function () {
    Route::get('/mypage', [ItemController::class, 'mypage'])
        ->name('mypage');
    Route::get('/sell', [DisplayController::class, 'add'])
        ->name('sell');
    Route::get('/sell/count-images', [SellController::class, 'countImages'])
        ->name('sell.image.count');
    Route::post('/sell/store', [SellController::class, 'store'])
        ->name('sell.store');
    Route::post('/sell/update/{item_id}', [SellController::class, 'update'])
        ->name('sell.update.item_id');
    Route::delete('/sell/delete/{item_id}', [SellController::class, 'delete'])
        ->name('sell.delete.item_id');
    Route::get('/purchase/{item_id}', [DisplayController::class, 'purchase'])
        ->name('purchase.item_id');
    Route::post('/purchase/{item_id}/update-method', [PurchaseController::class, 'updateMethod'])
        ->name('purchase.update-method');
    Route::get('/purchase/address/{item_id}', [DisplayController::class, 'address'])
        ->name('purchase.address.item_id');
    Route::post('/purchase/address/update/{item_id}', [PurchaseController::class, 'address'])
        ->name('purchase.address.update.item_id');
    Route::get('/item/edit/{item_id}', [DisplayController::class, 'edit'])
        ->name('item.edit.item_id');
    Route::post('/item/{item_id}/favorite', [FavoriteController::class, 'toggle'])
        ->name('item.item_id.favorite');
    Route::post('/item/{item_id}/comments', [CommentController::class, 'store'])
        ->name('item.item_id.comments');
    Route::post('/purchase/store/{item_id}', [PurchaseController::class, 'store'])
        ->name('purchase.store.item_id');
});
