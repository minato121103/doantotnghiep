<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CloudinaryController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;

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
Route::get('/categories', [MainController::class, 'categories'])->name('categories');
Route::get('/game/{id}', [MainController::class, 'gameDetail'])->name('game.detail');

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
    Route::get('/table/{tableName}/structure', [DatabaseController::class, 'tableStructure'])->name('table-structure');
});
