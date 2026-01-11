<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;


// ===========================================
// STORE FRONT
// ===========================================
Route::prefix('/')->name('store.')->group(function () {

    // Homepage
    Route::get('/', [StoreController::class, 'index'])->name('index');

    // Product details (STORE FRONT)
    Route::get('/product/{slug}', [StoreController::class, 'show'])->name('product.show');

    // Cart page
    Route::get('/cart', function () {
        return view('store.cart');
    })->name('cart');
Route::get('/pay', [StoreController::class, 'pay'])->name('store.pay');
Route::get('/pay', [PaymentController::class, 'payPage'])->name('pay');
Route::get('/pay/create', [PaymentController::class, 'create'])->name('pay.create');
Route::post('/pay', [PaymentController::class, 'pay'])->name('store.pay');
});


// ===========================================
// USER DASHBOARD
// ===========================================
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// ===========================================
// PROFILE + PAYMENTS (AUTH REQUIRED)
// ===========================================
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Payment
    Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('/payment/create', [PaymentController::class, 'createPayment'])->name('payment.create');
    Route::get('/payment/result', [PaymentController::class, 'paymentResult'])->name('payment.result');
    Route::get('/payment/history', [PaymentController::class, 'history'])->name('payment.history');
    Route::post('/payment/check-status', [PaymentController::class, 'checkStatus'])->name('payment.check-status');

    // Invoice download / preview (authenticated)
    Route::get('/payment/invoice/{order_id}', [PaymentController::class, 'downloadInvoice'])->name('payment.invoice');
    Route::get('/payment/invoice/{order_id}/preview', [PaymentController::class, 'previewInvoice'])->name('payment.invoice.preview');
});


// ===========================================
// MIDTRANS WEBHOOK
// ===========================================
Route::post('/midtrans-webhook', [PaymentController::class, 'handleNotification'])
    ->name('midtrans.webhook');

// Recurring notifications (Midtrans)
Route::post('/midtrans-recurring', [PaymentController::class, 'handleRecurringNotification'])
    ->name('midtrans.recurring');

// Pay account notifications (Midtrans)
Route::post('/midtrans-pay-account', [PaymentController::class, 'handlePayAccountNotification'])
    ->name('midtrans.pay_account');

// Redirect endpoints for success / unfinish / error (these are the URLs you put into Midtrans console)
Route::get('/payment/finish', [PaymentController::class, 'finishRedirect'])->name('payment.finish');
Route::get('/payment/unfinish', [PaymentController::class, 'unfinishRedirect'])->name('payment.unfinish');
Route::get('/payment/error', [PaymentController::class, 'paymentError'])->name('payment.error');




// routes/web.php
Route::prefix('admin')
    ->middleware(['auth', 'admin', 'throttle:admin'])
    ->group(function () {

        Route::get('products', [ProductController::class, 'index'])
            ->name('admin.index');

        Route::get('products/create', [ProductController::class, 'create'])
            ->name('admin.input');

        Route::post('products', [ProductController::class, 'store'])
            ->name('admin.store');

        Route::get('products/{product}/edit', [ProductController::class, 'edit'])
            ->name('admin.edit');

        Route::put('products/{product}', [ProductController::class, 'update'])
            ->name('admin.update');

        Route::delete('products/{product}', [ProductController::class, 'destroy'])
            ->name('admin.destroy');
});


use Illuminate\Support\Facades\Auth;



Route::get('/auth/google', [SocialiteController::class, 'redirect'])
    ->name('auth.google');

Route::get('/auth/google/callback', [SocialiteController::class, 'callback'])
    ->name('auth.google.callback');
  

require __DIR__.'/auth.php';