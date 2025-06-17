<?php

use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\XenditController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/midtrans/callback', [MidtransController::class, 'callback']);


// Route::post('/payment/webhook/callback', [XenditController::class, 'callback'])
//     ->name('xendit.callback');