<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductSimpleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SteamAccountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductDiscussionController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\NewsController;

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
    
    // Batch create orders (for cart checkout)
    Route::post('/batch', [OrderController::class, 'batchStore']);
    
    // Create a new order
    Route::post('/', [OrderController::class, 'store']);
    
    // Get a single order by ID
    Route::get('/{id}', [OrderController::class, 'show']);
    
    // Delete an order (Admin only)
    Route::delete('/{id}', [OrderController::class, 'destroy']);
});

// Review API Routes
Route::prefix('reviews')->group(function () {
    // Get all reviews
    Route::get('/', [ReviewController::class, 'index']);
    
    // Get reviews by product
    Route::get('/product/{productId}', [ReviewController::class, 'getByProduct']);
    
    // Get a single review by ID (must be last to avoid route conflicts)
    Route::get('/{id}', [ReviewController::class, 'show']);
});

Route::middleware('auth:sanctum')->prefix('reviews')->group(function () {
    // Check if user can review a product (must be before /{id} route)
    Route::get('/check/{productId}', [ReviewController::class, 'checkCanReview']);
    
    // Create a new review
    Route::post('/', [ReviewController::class, 'store']);
    
    // Update a review
    Route::put('/{id}', [ReviewController::class, 'update']);
    Route::patch('/{id}', [ReviewController::class, 'update']);
    
    // Delete a review
    Route::delete('/{id}', [ReviewController::class, 'destroy']);
});

// Product Discussion API Routes
Route::prefix('discussions')->group(function () {
    // Get all discussions (public - for admin management)
    Route::get('/', [ProductDiscussionController::class, 'all']);
    
    // Get discussions by product (public)
    Route::get('/product/{productId}', [ProductDiscussionController::class, 'index']);
    
    // Get a single discussion (public)
    Route::get('/{id}', [ProductDiscussionController::class, 'show']);
    
    // Like/Unlike discussion (public - không cần auth)
    Route::post('/{id}/like', [ProductDiscussionController::class, 'like']);
    Route::post('/{id}/unlike', [ProductDiscussionController::class, 'unlike']);
});

Route::middleware('auth:sanctum')->prefix('discussions')->group(function () {
    // Create a new discussion (auth required)
    Route::post('/', [ProductDiscussionController::class, 'store']);
    
    // Approve discussion (admin only)
    Route::post('/{id}/approve', [ProductDiscussionController::class, 'approve']);
    
    // Delete own discussion (auth required)
    Route::delete('/{id}', [ProductDiscussionController::class, 'destroy']);
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

// Wallet API Routes
Route::middleware('auth:sanctum')->prefix('wallet')->group(function () {
    // Create VNPay payment URL
    Route::post('/create-payment', [WalletController::class, 'createPayment']);
});

// VNPay Callback Routes (no auth required)
Route::prefix('wallet')->group(function () {
    // VNPay callback (return URL)
    Route::get('/vnpay/callback', [WalletController::class, 'callback']);
    
    // VNPay IPN (Instant Payment Notification)
    Route::post('/vnpay/ipn', [WalletController::class, 'ipn']);
});

// Recommendation API Routes
Route::prefix('recommendations')->group(function () {
    // Public routes - Get recommendations
    Route::get('/products/{productId}/similar', [RecommendationController::class, 'getSimilarProducts']);
    Route::get('/popular', [RecommendationController::class, 'getPopularProducts']);
});

// Authenticated recommendation routes
Route::middleware('auth:sanctum')->prefix('recommendations')->group(function () {
    // Get personalized recommendations for current user
    Route::get('/for-me', [RecommendationController::class, 'getUserRecommendations']);
    Route::get('/user/{userId}', [RecommendationController::class, 'getUserRecommendations']);
    
    // Record user interaction (for improving recommendations)
    Route::post('/interaction', [RecommendationController::class, 'recordInteraction']);
});

// News API Routes
Route::prefix('news')->group(function () {
    // Get all news (public)
    Route::get('/', [NewsController::class, 'index']);
    
    // Get a single news by ID (public)
    Route::get('/{id}', [NewsController::class, 'show']);
});

// Admin News Routes (should add admin middleware)
Route::prefix('news')->group(function () {
    // Create a new news
    Route::post('/', [NewsController::class, 'store']);
    
    // Update a news
    Route::put('/{id}', [NewsController::class, 'update']);
    Route::patch('/{id}', [NewsController::class, 'update']);
    
    // Delete a news
    Route::delete('/{id}', [NewsController::class, 'destroy']);
});
