<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CloudinaryController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\NewsController;

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

// Main Routes - Game Store
Route::get('/', [MainController::class, 'index'])->name('home');
Route::get('/store', [MainController::class, 'store'])->name('store');
Route::get('/store/offline', [MainController::class, 'store'])->name('store.offline');
Route::get('/store/online', [MainController::class, 'store'])->name('store.online');
Route::get('/categories', [MainController::class, 'categories'])->name('categories');
Route::get('/game/{id}', [MainController::class, 'gameDetail'])->name('game.detail');
Route::get('/cart', [MainController::class, 'cart'])->name('cart');
Route::get('/orders', [MainController::class, 'orders'])->name('orders');
Route::get('/wallet', [MainController::class, 'wallet'])->name('wallet');
Route::get('/wallet/vnpay/callback', [WalletController::class, 'callback'])->name('wallet.vnpay.callback');
Route::get('/wallet/payment/callback', [MainController::class, 'paymentCallback'])->name('wallet.payment.callback');
Route::get('/news', [MainController::class, 'news'])->name('news');
Route::get('/news/{id}', [MainController::class, 'newsDetail'])->name('news.detail');

// Authentication Routes
Route::get('/login', [MainController::class, 'login'])->name('login');
Route::get('/register', [MainController::class, 'register'])->name('register');
Route::get('/auth/callback', [MainController::class, 'authCallback'])->name('auth.callback');

// Social Login Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::get('/auth/facebook', [AuthController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('/auth/facebook/callback', [AuthController::class, 'handleFacebookCallback']);

Route::get('/welcome', function () {
    return view('welcome');
});

// Cloudinary routes
Route::prefix('cloudinary')->group(function () {
    Route::post('/upload', [CloudinaryController::class, 'upload']);
    Route::post('/upload-all', [CloudinaryController::class, 'uploadAll']);
    Route::get('/results', [CloudinaryController::class, 'results']);
});

// Route to access the Cloudinary upload page
Route::get('/cloudinary/upload', function () {
    return view('Cloudinary.update');
});

// Database Management routes (CRUD operations handled via API)
Route::prefix('database')->name('database.')->group(function () {
    Route::get('/', [DatabaseController::class, 'index'])->name('index');
    Route::get('/users', [DatabaseController::class, 'users'])->name('users');
    Route::get('/users/create', [DatabaseController::class, 'createUser'])->name('create-user');
    Route::get('/users/{id}/edit', [DatabaseController::class, 'editUser'])->name('edit-user');
    Route::get('/products', [DatabaseController::class, 'products'])->name('products');
    Route::get('/products/create', [DatabaseController::class, 'createProduct'])->name('create-product');
    Route::get('/products/{id}/edit', [DatabaseController::class, 'editProduct'])->name('edit-product');
    Route::get('/news', [DatabaseController::class, 'news'])->name('news');
    Route::get('/news/create', [DatabaseController::class, 'createNews'])->name('create-news');
    Route::get('/news/{id}/edit', [DatabaseController::class, 'editNews'])->name('edit-news');
    Route::get('/orders', [DatabaseController::class, 'orders'])->name('orders');
    Route::get('/reviews', [DatabaseController::class, 'reviews'])->name('reviews');
    Route::get('/transactions', [DatabaseController::class, 'transactions'])->name('transactions');
    Route::get('/steam-accounts', [DatabaseController::class, 'steamAccounts'])->name('steam-accounts');
    Route::get('/discussions', [DatabaseController::class, 'discussions'])->name('discussions');
    Route::get('/table/{tableName}/structure', [DatabaseController::class, 'tableStructure'])->name('table-structure');
    
    // AI Recommendation Management Routes
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations');
    Route::post('/recommendations/train', [RecommendationController::class, 'train'])->name('recommendations.train');
    Route::get('/recommendations/status', [RecommendationController::class, 'trainingStatus'])->name('recommendations.status');
    Route::get('/recommendations/history', [RecommendationController::class, 'trainingHistory'])->name('recommendations.history');
    Route::post('/recommendations/clear-cache', [RecommendationController::class, 'clearCache'])->name('recommendations.clear-cache');
    Route::delete('/recommendations/logs/{id}', [RecommendationController::class, 'deleteTrainingLog'])->name('recommendations.delete-log');
});
