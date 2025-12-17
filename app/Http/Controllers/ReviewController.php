<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use App\Models\ProductSimple;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Review::with(['buyer', 'game', 'order']);

        // Filter by game/product
        if ($request->has('product_simple_id') && $request->product_simple_id) {
            $query->where('product_simple_id', $request->product_simple_id);
        }

        // Filter by buyer
        if ($request->has('buyer_id') && $request->buyer_id) {
            $query->where('buyer_id', $request->buyer_id);
        }

        // Filter by rating
        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }

        // Filter verified purchases only
        if ($request->has('verified_only') && $request->verified_only) {
            $query->where('is_verified_purchase', true);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSortFields = ['id', 'rating', 'created_at'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $perPage = min(max(1, $perPage), 100);

        $reviews = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $reviews->items(),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'last_page' => $reviews->lastPage(),
                'from' => $reviews->firstItem(),
                'to' => $reviews->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created review.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if order exists and belongs to user
        $order = Order::find($request->order_id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->buyer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'This order does not belong to you'
            ], 403);
        }

        // Check if order is completed
        if ($order->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'You can only review completed orders'
            ], 400);
        }

        // Check if review already exists for this order
        if (Review::where('order_id', $request->order_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this order'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $review = Review::create([
                'order_id' => $request->order_id,
                'buyer_id' => $user->id,
                'product_simple_id' => $order->product_simple_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'images' => $request->images,
                'is_verified_purchase' => true,
            ]);

            // Update product rating statistics
            $product = ProductSimple::find($order->product_simple_id);
            if ($product) {
                $totalReviews = Review::where('product_simple_id', $product->id)->count();
                $averageRating = Review::where('product_simple_id', $product->id)->avg('rating');
                
                $product->update([
                    'rating_count' => $totalReviews,
                    'average_rating' => round($averageRating, 2),
                ]);
            }

            DB::commit();

            $review->load(['buyer', 'game', 'order']);

            return response()->json([
                'success' => true,
                'message' => 'Review created successfully',
                'data' => $review
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified review.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $review = Review::with(['buyer', 'game', 'order'])->find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $review
        ]);
    }

    /**
     * Update the specified review.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        // Check if user owns this review
        if ($review->buyer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only update your own reviews'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $review->update($request->only(['rating', 'comment', 'images']));

            // Update product rating statistics
            $product = ProductSimple::find($review->product_simple_id);
            if ($product) {
                $totalReviews = Review::where('product_simple_id', $product->id)->count();
                $averageRating = Review::where('product_simple_id', $product->id)->avg('rating');
                
                $product->update([
                    'rating_count' => $totalReviews,
                    'average_rating' => round($averageRating, 2),
                ]);
            }

            DB::commit();

            $review->load(['buyer', 'game', 'order']);

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully',
                'data' => $review
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified review.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        // Check if user owns this review or is admin
        if ($review->buyer_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $productId = $review->product_simple_id;

        DB::beginTransaction();
        try {
            $review->delete();

            // Update product rating statistics
            $product = ProductSimple::find($productId);
            if ($product) {
                $totalReviews = Review::where('product_simple_id', $productId)->count();
                $averageRating = $totalReviews > 0 
                    ? Review::where('product_simple_id', $productId)->avg('rating') 
                    : 0;
                
                $product->update([
                    'rating_count' => $totalReviews,
                    'average_rating' => round($averageRating, 2),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reviews for a specific product/game
     *
     * @param int $productId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByProduct($productId, Request $request)
    {
        $query = Review::with(['buyer', 'order'])
            ->where('product_simple_id', $productId)
            ->where('is_verified_purchase', true);

        // Filter by rating
        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSortFields = ['id', 'rating', 'created_at'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $perPage = min(max(1, $perPage), 100);

        $reviews = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $reviews->items(),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'last_page' => $reviews->lastPage(),
            ]
        ]);
    }
}

