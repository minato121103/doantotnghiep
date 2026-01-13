<?php

namespace App\Http\Controllers;

use App\Models\ProductDiscussion;
use App\Models\ProductSimple;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductDiscussionController extends Controller
{
    /**
     * Display a listing of discussions for a product.
     *
     * @param Request $request
     * @param int $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $productId)
    {
        // Verify product exists
        $product = ProductSimple::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại'
            ], 404);
        }

        // Get only top-level comments (parent_id is null)
        $query = ProductDiscussion::where('product_simple_id', $productId)
            ->where('parent_id', null)
            ->where('status', 'approved')
            ->with(['user', 'replies.user']);

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSortFields = ['id', 'created_at', 'like_count'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $perPage = min(max(1, $perPage), 100);

        $discussions = $query->paginate($perPage);

        // Format data for frontend
        $formattedData = $discussions->items();
        foreach ($formattedData as $discussion) {
            $discussion->display_name = $discussion->display_name;
            $discussion->avatar_initial = $discussion->avatar_initial;
            
            // Format replies
            if ($discussion->replies) {
                foreach ($discussion->replies as $reply) {
                    $reply->display_name = $reply->display_name;
                    $reply->avatar_initial = $reply->avatar_initial;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $formattedData,
            'pagination' => [
                'current_page' => $discussions->currentPage(),
                'per_page' => $discussions->perPage(),
                'total' => $discussions->total(),
                'last_page' => $discussions->lastPage(),
                'from' => $discussions->firstItem(),
                'to' => $discussions->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created discussion.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'product_simple_id' => 'required|exists:product_simple,id',
            'content' => 'required|string|max:5000|min:1',
            'parent_id' => 'nullable|exists:product_discussions,id',
            'author_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // If replying, verify parent exists and belongs to same product
        if ($request->parent_id) {
            $parent = ProductDiscussion::find($request->parent_id);
            if (!$parent || $parent->product_simple_id != $request->product_simple_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bình luận cha không hợp lệ'
                ], 400);
            }
        }

        // If user is logged in, use user info; otherwise use author_name
        $authorName = null;
        if (!$user) {
            $authorName = $request->author_name ?: 'Người dùng ẩn danh';
        }

        $discussion = ProductDiscussion::create([
            'product_simple_id' => $request->product_simple_id,
            'user_id' => $user ? $user->id : null,
            'author_name' => $authorName,
            'content' => $request->content,
            'parent_id' => $request->parent_id,
            'status' => $user ? 'approved' : 'pending', // Ẩn danh cần duyệt
        ]);

        $discussion->load(['user', 'replies.user']);
        $discussion->display_name = $discussion->display_name;
        $discussion->avatar_initial = $discussion->avatar_initial;

        return response()->json([
            'success' => true,
            'message' => $user ? 'Bình luận đã được đăng' : 'Bình luận đang chờ duyệt',
            'data' => $discussion
        ], 201);
    }

    /**
     * Display the specified discussion.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $discussion = ProductDiscussion::with(['user', 'replies.user', 'parent.user'])
            ->where('status', 'approved')
            ->find($id);

        if (!$discussion) {
            return response()->json([
                'success' => false,
                'message' => 'Bình luận không tồn tại'
            ], 404);
        }

        $discussion->display_name = $discussion->display_name;
        $discussion->avatar_initial = $discussion->avatar_initial;

        return response()->json([
            'success' => true,
            'data' => $discussion
        ]);
    }

    /**
     * Delete a discussion (own or admin).
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $discussion = ProductDiscussion::find($id);

        if (!$discussion) {
            return response()->json([
                'success' => false,
                'message' => 'Bình luận không tồn tại'
            ], 404);
        }

        // Check permission: own comment or admin
        if ($discussion->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa bình luận này'
            ], 403);
        }

        $discussion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bình luận đã được xóa'
        ]);
    }

    /**
     * Like a discussion.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function like(Request $request, $id)
    {
        $discussion = ProductDiscussion::where('status', 'approved')->find($id);

        if (!$discussion) {
            return response()->json([
                'success' => false,
                'message' => 'Bình luận không tồn tại'
            ], 404);
        }

        // Tăng like_count
        $discussion->increment('like_count');
        $discussion->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Đã thích bình luận',
            'data' => [
                'id' => $discussion->id,
                'like_count' => $discussion->like_count
            ]
        ]);
    }

    /**
     * Unlike a discussion.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlike(Request $request, $id)
    {
        $discussion = ProductDiscussion::where('status', 'approved')->find($id);

        if (!$discussion) {
            return response()->json([
                'success' => false,
                'message' => 'Bình luận không tồn tại'
            ], 404);
        }

        // Giảm like_count (không cho phép âm)
        if ($discussion->like_count > 0) {
            $discussion->decrement('like_count');
            $discussion->refresh();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã bỏ thích bình luận',
            'data' => [
                'id' => $discussion->id,
                'like_count' => $discussion->like_count
            ]
        ]);
    }
}
