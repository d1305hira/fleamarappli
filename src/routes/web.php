<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemLikeController;
use App\Http\Controllers\ItemCommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ShippingAddressController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [CustomLoginController::class, 'login'])->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('/mypage', [UserController::class, 'show'])->name('profile');
    Route::get('/mypage/profile', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('profile.update');

    Route::post('/items/{item}/like', [ItemLikeController::class, 'toggle'])->name('item.like');
    Route::post('/items/{item}/comments', [ItemCommentController::class, 'store'])->name('comments.store');

    Route::get('/sell', [ItemController::class, 'create'])->name('item_shipping');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::get('/purchase/address/{item}', [ShippingAddressController::class, 'edit'])->name('shipping_address.edit');

    Route::post('/address/update/{item}', [ShippingAddressController::class, 'update'])->name('shipping_address.update');

    Route::post('/checkout', [PurchaseController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/success', [PurchaseController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [PurchaseController::class, 'cancel'])->name('checkout.cancel');

    Route::get('/transaction/{item}', [TransactionController::class, 'show'])
    ->name('transaction.show');
    Route::post('/transaction/{item}/message', [TransactionController::class, 'store'])
    ->name('transaction.message');
    Route::post('/transaction/{item}/complete', [TransactionController::class, 'complete'])
    ->name('transaction.complete');
    Route::patch('/messages/{message}', [TransactionController::class, 'updateMessage'])
    ->name('messages.update');
    Route::delete('/messages/{message}', [TransactionController::class, 'destroyMessage'])
    ->name('messages.destroy');
});


Route::get('/', [ItemController::class, 'index'])->name('top');


Route::get('/items/{item}', [ItemController::class, 'show'])->name('item.show');