<?php

use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Controllers\Store\PageController;
use App\Http\Controllers\Store\PostController;
use App\Http\Controllers\Store\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/tin-tuc', [PostController::class, 'index'])->name('posts.index');
Route::get('/tin-tuc/{slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/ve-chung-toi', [PageController::class, 'about'])->name('about');

Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::post('/gio-hang/them', [CartController::class, 'add'])->name('cart.add');
Route::patch('/gio-hang', [CartController::class, 'update'])->name('cart.update');
Route::delete('/gio-hang/{key}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/dat-hang', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/dat-hang/ma-km', [CheckoutController::class, 'applyPromo'])->name('checkout.promo');
Route::post('/dat-hang', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/dat-hang/thanh-cong/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
