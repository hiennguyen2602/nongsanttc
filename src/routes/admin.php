<?php

use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('password/forgot', [AuthController::class, 'showForgotPassword'])->name('password.forgot');
    Route::post('password/email', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('password/reset/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('password/reset', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('password/change', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('password/change', [AuthController::class, 'changePassword'])->name('password.change.submit');

    Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::post('media/delete', [MediaController::class, 'destroy'])->name('media.delete');

    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('posts', PostController::class);
    Route::resource('promotions', PromotionController::class)->except(['show']);

    // Chỉ Admin — xem docs/store-logic.md mục 5
    Route::middleware('administrator')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('banners', BannerController::class)->except(['show']);
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

    Route::get('contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::delete('contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
});
