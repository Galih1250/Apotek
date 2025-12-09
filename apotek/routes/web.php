<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Payment routes
    Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('/payment/create', [PaymentController::class, 'createPayment'])->name('payment.create');
    Route::get('/payment/result', [PaymentController::class, 'paymentResult'])->name('payment.result');
    Route::get('/payment/history', [PaymentController::class, 'history'])->name('payment.history');
    Route::post('/payment/check-status', [PaymentController::class, 'checkStatus'])->name('payment.check-status');
});

// Midtrans webhook (no auth required)
Route::post('/midtrans-webhook', [PaymentController::class, 'handleNotification'])->name('midtrans.webhook');

require __DIR__.'/auth.php';
