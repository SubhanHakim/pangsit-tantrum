<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\XenditController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Route::get('/menu/{id}', [MenuController::class, 'show'])->name('menu.show');

Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');

Route::delete('/cart/remove/{index}', [CartController::class, 'remove'])->name('cart.remove');


Route::patch('/cart/update/{index}', [CartController::class, 'update'])->name('cart.update');

Route::get('/payment', [CartController::class, 'payment'])->name('payment');
Route::post('/payment/process', [CartController::class, 'processPayment'])->name('payment.process');
Route::get('/midtrans', [CartController::class, 'midtransPage'])->name('midtrans.page');
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);

Route::get('/order/{order_id}', [OrderController::class, 'show'])->name('order.show');

Route::get('/admin/tables/{id}/generate-qr', [TableController::class, 'generate'])
    ->name('admin.tables.generate-qr')
    ->middleware(['auth']);



// Route::get('/payment', [XenditController::class, 'payment'])->name('payment');
// Route::post('/payment/process', [XenditController::class, 'processPayment'])->name('payment.process');
// Route::post('/xendit/callback', [XenditController::class, 'callback'])->name('xendit.callback');
// Route::get('/order/{order_id}', [XenditController::class, 'show'])->name('order.show');