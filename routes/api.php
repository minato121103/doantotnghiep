<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductSimpleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SteamAccountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication API Routes
Route::prefix('auth')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

// Product Simple API Routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductSimpleController::class, 'index']);
    Route::get('/categories', [ProductSimpleController::class, 'categories']);
    Route::post('/', [ProductSimpleController::class, 'store']);
    Route::get('/{id}', [ProductSimpleController::class, 'show']);
    Route::put('/{id}', [ProductSimpleController::class, 'update']);
    Route::patch('/{id}', [ProductSimpleController::class, 'update']);
    Route::delete('/{id}', [ProductSimpleController::class, 'destroy']);
});

// User API Routes
Route::prefix('users')->group(function () {
    // Get all users (with filters)
    Route::get('/', [UserController::class, 'index']);
    
    // Create a new user
    Route::post('/', [UserController::class, 'store']);
    
    // Get a single user by ID
    Route::get('/{id}', [UserController::class, 'show']);
    
    // Update a user
    Route::put('/{id}', [UserController::class, 'update']);
    Route::patch('/{id}', [UserController::class, 'update']);
    
    // Delete a user
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

// Authenticated User Routes
Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    // Get current user profile
    Route::get('/profile', [UserController::class, 'profile']);
    
    // Update current user profile
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::patch('/profile', [UserController::class, 'updateProfile']);
});

// Steam Account API Routes (Admin only - should add middleware)
Route::prefix('steam-accounts')->group(function () {
    // Get all steam accounts
    Route::get('/', [SteamAccountController::class, 'index']);
    
    // Create a new steam account
    Route::post('/', [SteamAccountController::class, 'store']);
    
    // Find available account by game
    Route::get('/find-by-game/{gameId}', [SteamAccountController::class, 'findAvailableByGame']);
    
    // Get a single steam account by ID
    Route::get('/{id}', [SteamAccountController::class, 'show']);
    
    // Update a steam account
    Route::put('/{id}', [SteamAccountController::class, 'update']);
    Route::patch('/{id}', [SteamAccountController::class, 'update']);
    
    // Delete a steam account
    Route::delete('/{id}', [SteamAccountController::class, 'destroy']);
});

// Order API Routes
Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
    // Get all orders (user sees own, admin sees all)
    Route::get('/', [OrderController::class, 'index']);
    
    // Create a new order
    Route::post('/', [OrderController::class, 'store']);
    
    // Get a single order by ID
    Route::get('/{id}', [OrderController::class, 'show']);
    
    // Update order status (Admin only)
    Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
    Route::patch('/{id}/status', [OrderController::class, 'updateStatus']);
});

// Review API Routes
Route::prefix('reviews')->group(function () {
    // Get all reviews
    Route::get('/', [ReviewController::class, 'index']);
    
    // Get reviews by product
    Route::get('/product/{productId}', [ReviewController::class, 'getByProduct']);
    
    // Get a single review by ID
    Route::get('/{id}', [ReviewController::class, 'show']);
});

Route::middleware('auth:sanctum')->prefix('reviews')->group(function () {
    // Create a new review
    Route::post('/', [ReviewController::class, 'store']);
    
    // Update a review
    Route::put('/{id}', [ReviewController::class, 'update']);
    Route::patch('/{id}', [ReviewController::class, 'update']);
    
    // Delete a review
    Route::delete('/{id}', [ReviewController::class, 'destroy']);
});

// Transaction API Routes
Route::middleware('auth:sanctum')->prefix('transactions')->group(function () {
    // Get all transactions (user sees own, admin sees all)
    Route::get('/', [TransactionController::class, 'index']);
    
    // Get transaction statistics
    Route::get('/statistics', [TransactionController::class, 'statistics']);
    
    // Create a deposit
    Route::post('/deposit', [TransactionController::class, 'deposit']);
    
    // Create a withdraw request
    Route::post('/withdraw', [TransactionController::class, 'withdraw']);
    
    // Get a single transaction by ID
    Route::get('/{id}', [TransactionController::class, 'show']);
    
    // Update transaction status (Admin only)
    Route::put('/{id}/status', [TransactionController::class, 'updateStatus']);
    Route::patch('/{id}/status', [TransactionController::class, 'updateStatus']);
});
