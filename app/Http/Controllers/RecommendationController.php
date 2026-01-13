<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\ProductSimple;

class RecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Dashboard - Hiển thị thống kê và quản lý
     */
    public function index()
    {
        // Check if tables exist
        $tablesExist = Schema::hasTable('user_recommendations') && 
                       Schema::hasTable('product_recommendations') &&
                       Schema::hasTable('recommendation_training_logs');

        $stats = [
            'tables_exist' => $tablesExist,
            'users_with_recommendations' => 0,
            'products_with_recommendations' => 0,
            'total_recommendations' => 0,
            'total_interactions' => 0,
            'last_training' => null,
            'training_history' => [],
            'data_stats' => [
                'total_users' => User::count(),
                'total_products' => ProductSimple::count(),
                'total_orders' => DB::table('orders')->where('status', 'completed')->count(),
                'total_reviews' => DB::table('reviews')->count(),
                'users_with_orders' => DB::table('orders')->distinct('buyer_id')->count('buyer_id'),
            ],
        ];

        if ($tablesExist) {
            $stats['users_with_recommendations'] = DB::table('user_recommendations')
                ->distinct('user_id')->count('user_id');
            $stats['products_with_recommendations'] = DB::table('product_recommendations')
                ->distinct('product_id')->count('product_id');
            $stats['total_recommendations'] = DB::table('user_recommendations')->count() + 
                                              DB::table('product_recommendations')->count();
            $stats['total_interactions'] = Schema::hasTable('user_product_interactions') 
                ? DB::table('user_product_interactions')->count() 
                : 0;
            $stats['last_training'] = DB::table('recommendation_training_logs')
                ->latest('created_at')
                ->first();
            $stats['training_history'] = DB::table('recommendation_training_logs')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        return view('database.recommendations', compact('stats'));
    }

    /**
     * API: Trigger training manually
     */
    public function train(Request $request)
    {
        try {
            // Check if tables exist
            if (!Schema::hasTable('user_recommendations')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chưa có bảng database. Vui lòng chạy: php artisan migrate'
                ], 400);
            }

            // Check if already running
            $running = DB::table('recommendation_training_logs')
                ->where('status', 'running')
                ->where('created_at', '>', now()->subMinutes(30))
                ->exists();

            if ($running && !$request->boolean('force', false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đang có một training process đang chạy. Vui lòng đợi hoặc sử dụng force=true'
                ], 400);
            }

            // Run training synchronously for better feedback
            $result = $this->recommendationService->train($request->boolean('force', false));

            return response()->json([
                'success' => true,
                'message' => 'Training hoàn thành thành công!',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get training status
     */
    public function trainingStatus()
    {
        if (!Schema::hasTable('recommendation_training_logs')) {
            return response()->json([
                'success' => false,
                'message' => 'Tables not created yet'
            ]);
        }

        $lastTraining = DB::table('recommendation_training_logs')
            ->latest('created_at')
            ->first();

        $stats = $this->recommendationService->getStats();

        return response()->json([
            'success' => true,
            'data' => [
                'last_training' => $lastTraining,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * API: Get recommendations for a user
     */
    public function getUserRecommendations(Request $request, $userId = null)
    {
        // If no userId provided, try to get from authenticated user
        if (!$userId) {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID required'
                ], 400);
            }
            $userId = $user->id;
        }

        $limit = $request->get('limit', 10);
        $recommendations = $this->recommendationService->getUserRecommendations($userId, $limit);
        
        return response()->json([
            'success' => true,
            'data' => $recommendations
        ]);
    }

    /**
     * API: Get similar products
     */
    public function getSimilarProducts(Request $request, $productId)
    {
        $limit = $request->get('limit', 10);
        $similar = $this->recommendationService->getSimilarProducts($productId, $limit);
        
        return response()->json([
            'success' => true,
            'data' => $similar
        ]);
    }

    /**
     * API: Get popular products (for new users or fallback)
     */
    public function getPopularProducts(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        // Get from cache first
        $popularIds = \Illuminate\Support\Facades\Cache::get('popular_recommendations', []);
        
        if (!empty($popularIds)) {
            // Lấy sản phẩm từ cache IDs và giữ thứ tự
            $products = ProductSimple::whereIn('id', array_slice($popularIds, 0, $limit))
                ->get()
                ->sortBy(function($product) use ($popularIds) {
                    return array_search($product->id, $popularIds);
                })
                ->values()
                ->toArray();
                
            if (count($products) >= $limit) {
                return response()->json([
                    'success' => true,
                    'data' => $products
                ]);
            }
        }
        
        // Fallback: Tính toán popular dựa trên view_count, orders, và rating
        $products = DB::table('product_simple')
            ->leftJoin('orders', function($join) {
                $join->on('product_simple.id', '=', 'orders.product_simple_id')
                     ->where('orders.status', '=', 'completed');
            })
            ->leftJoin('reviews', 'product_simple.id', '=', 'reviews.product_simple_id')
            ->select(
                'product_simple.*',
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('COUNT(DISTINCT reviews.id) as review_count'),
                DB::raw('COALESCE(AVG(reviews.rating), 0) as avg_rating')
            )
            ->groupBy('product_simple.id')
            // Score = (orders * 5) + (reviews * 2) + (rating * 3) + (view_count / 100)
            ->orderByRaw('
                (COUNT(DISTINCT orders.id) * 5) + 
                (COUNT(DISTINCT reviews.id) * 2) + 
                (COALESCE(AVG(reviews.rating), 0) * 3) + 
                (COALESCE(product_simple.view_count, 0) / 100) 
                DESC
            ')
            ->limit($limit)
            ->get()
            ->toArray();
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * API: Record user interaction (for tracking)
     */
    public function recordInteraction(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:product_simple,id',
            'type' => 'required|in:view,cart_add,purchase,review,wishlist'
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $this->recommendationService->recordInteraction(
                $user->id,
                $request->product_id,
                $request->type
            );

            return response()->json([
                'success' => true,
                'message' => 'Interaction recorded'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Clear cache
     */
    public function clearCache()
    {
        try {
            $this->recommendationService->clearCache();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache đã được xóa thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get training history
     */
    public function trainingHistory(Request $request)
    {
        if (!Schema::hasTable('recommendation_training_logs')) {
            return response()->json([
                'success' => false,
                'message' => 'Tables not created yet'
            ]);
        }

        $limit = $request->get('limit', 20);
        
        $history = DB::table('recommendation_training_logs')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * API: Delete a training log
     */
    public function deleteTrainingLog($id)
    {
        try {
            DB::table('recommendation_training_logs')->where('id', $id)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa log training'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}
