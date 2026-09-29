<?php

use App\Http\Controllers\Store\ContactController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Controllers\Store\PageController;
use App\Http\Controllers\Store\PostController;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\RobotsController;
use App\Http\Controllers\Store\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/tin-tuc', [PostController::class, 'index'])->name('posts.index');
Route::get('/tin-tuc/{slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/ve-chung-toi', [PageController::class, 'about'])->name('about');
Route::get('/lien-he', [ContactController::class, 'index'])->name('contact');
// Giới hạn request theo IP — chi tiết: docs/store-logic.md
Route::post('/lien-he', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::post('/gio-hang/them', [CartController::class, 'add'])->name('cart.add');
Route::patch('/gio-hang', [CartController::class, 'update'])->name('cart.update');
Route::delete('/gio-hang/{key}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/dat-hang', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/dat-hang/ma-km', [CheckoutController::class, 'applyPromo'])
    ->middleware('throttle:15,1')
    ->name('checkout.promo');
Route::post('/dat-hang', [CheckoutController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('checkout.store');
Route::get('/dat-hang/thanh-cong/{token}', [CheckoutController::class, 'success'])
    ->where('token', '[a-f0-9]{32}')
    ->name('checkout.success');
