<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommunityController extends Controller
{
    // Display community page
    public function index()
    {
        return view('main.community');
    }

    // Admin: Get all posts for management (including inactive)
    public function adminGetPosts(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user || !in_array($user->role, ['admin', 'editor'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            $search = $request->input('search');
            $status = $request->input('status', 'all'); // all, active, inactive
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            $query = \App\Models\CommunityPost::with('user');

            // Filter by status
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }

            // Search
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('content', 'LIKE', "%{$search}%")
                      ->orWhereHas('user', function ($q2) use ($search) {
                          $q2->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                      });
                });
            }

            // Sorting
            $query->orderBy($sortBy, $sortOrder);

            $posts = $query->paginate($perPage);

            // Get stats
            $stats = [
                'total' => \App\Models\CommunityPost::count(),
                'active' => \App\Models\CommunityPost::where('is_active', true)->count(),
                'inactive' => \App\Models\CommunityPost::where('is_active', false)->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $posts->items(),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ],
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Admin: Toggle post active status
    public function adminToggleStatus(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user || !in_array($user->role, ['admin', 'editor'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $post = \App\Models\CommunityPost::findOrFail($id);
            $post->is_active = !$post->is_active;
            $post->save();

            return response()->json([
                'success' => true,
                'message' => $post->is_active ? 'Đã kích hoạt bài viết' : 'Đã ẩn bài viết',
                'is_active' => $post->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Get game groups for current user (online games they purchased; admin thấy tất cả nhóm)
    public function getMyGameGroups(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $isAdmin = in_array($user->role ?? '', ['admin', 'editor']);

            // Admin/editor: hiển thị tất cả game online (để vào mọi nhóm cộng đồng). User thường: chỉ game đã mua.
            $query = \App\Models\ProductSimple::where(function ($q) {
                $q->where('type', 'online')->orWhereNull('type');
            });

            if (!$isAdmin) {
                $query->whereHas('orders', function ($q) use ($user) {
                    $q->where('buyer_id', $user->id)
                      ->whereIn('status', ['completed', 'processing']);
                });
            }

            $games = $query->select('id', 'title', 'image')
                ->distinct()
                ->get()
                ->map(function ($game) {
                    // Count members (users who also bought this game)
                    $memberCount = \App\Models\Order::where('product_simple_id', $game->id)
                        ->whereIn('status', ['completed', 'processing'])
                        ->distinct('buyer_id')
                        ->count('buyer_id');
                    // Count posts matching this game
                    $postCount = \App\Models\CommunityPost::where('game_preference', 'LIKE', '%' . $game->title . '%')
                        ->where('is_active', true)
                        ->count();
                    return [
                        'id' => $game->id,
                        'title' => $game->title,
                        'image' => $game->image,
                        'member_count' => $memberCount,
                        'post_count' => $postCount,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $games
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Get all community posts (API)
    public function getAllPosts(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $gameFilter = $request->input('game_filter');
            
            $query = \App\Models\CommunityPost::with('user')
                ->where('is_active', true);
            
            // Filter by game group
            if ($gameFilter) {
                $query->where('game_preference', 'LIKE', '%' . $gameFilter . '%');
            } else {
                // In "All posts" view, hide posts that belong to a game group
                $query->where(function ($q) {
                    $q->whereNull('game_preference')
                      ->orWhere('game_preference', '');
                });
            }
            
            $posts = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);
            
            // Add liked status for authenticated user
            $user = auth('sanctum')->user();
            $postsData = $posts->items();
            
            if ($user) {
                foreach ($postsData as $post) {
                    $post->is_liked = \DB::table('community_post_likes')
                        ->where('post_id', $post->id)
                        ->where('user_id', $user->id)
                        ->exists();
                }
            } else {
                foreach ($postsData as $post) {
                    $post->is_liked = false;
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $postsData,
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching posts: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Create new post (API)
    public function createPost(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|string',
                'media' => 'nullable|array',
                'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov|max:51200', // max 50MB
                'game_preference' => 'nullable|string',
                'privacy' => 'nullable|in:public,friends,private'
            ]);
            
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            $images = [];
            $videos = [];
            
            // Handle file uploads
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $extension = strtolower($file->getClientOriginalExtension());
                    $filename = uniqid() . '_' . time() . '.' . $extension;
                    
                    // Store file in public storage
                    $path = $file->storeAs('community', $filename, 'public');
                    $url = asset('storage/' . $path);
                    
                    // Categorize by type
                    if (in_array($extension, ['mp4', 'webm', 'mov'])) {
                        $videos[] = $url;
                    } else {
                        $images[] = $url;
                    }
                }
            }
            
            $post = \App\Models\CommunityPost::create([
                'user_id' => $user->id,
                'content' => $request->content,
                'images' => $images,
                'videos' => $videos,
                'game_preference' => $request->game_preference,
                'privacy' => $request->privacy ?? 'public',
            ]);
            
            // Load user relationship
            $post->load('user');
            
            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => $post
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating post: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Update post (API) — chỉ người đăng
    public function updatePost(Request $request, $id)
    {
        try {
            $request->validate([
                'content' => 'required|string',
                'game_preference' => 'nullable|string',
            ]);
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            $post = \App\Models\CommunityPost::findOrFail($id);
            if ($post->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }
            $post->content = $request->content;
            $post->game_preference = $request->game_preference ?? '';
            $post->save();
            $post->load('user');
            return response()->json(['success' => true, 'message' => 'Cập nhật thành công', 'data' => $post]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Delete post (API) — người đăng hoặc admin/editor. Admin xóa thì gửi tin nhắn cho tác giả.
    public function deletePost(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $post = \App\Models\CommunityPost::findOrFail($id);
            $isAdminOrEditor = in_array($user->role ?? '', ['admin', 'editor']);

            if ($post->user_id !== $user->id && !$isAdminOrEditor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden'
                ], 403);
            }

            // Admin (không phải editor) xóa bài của người khác → gửi tin nhắn cho tác giả
            if (($user->role ?? '') === 'admin' && $post->user_id != $user->id) {
                \Illuminate\Support\Facades\DB::table('messages')->insert([
                    'sender_id' => $user->id,
                    'receiver_id' => $post->user_id,
                    'content' => 'Bài viết của bạn đã bị xóa do vi phạm quy tắc cộng đồng.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting post: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Toggle like on post (API)
    public function toggleLike(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            $post = \App\Models\CommunityPost::findOrFail($id);
            
            // Check if user already liked
            $existing = \DB::table('community_post_likes')
                ->where('post_id', $id)
                ->where('user_id', $user->id)
                ->first();
            
            if ($existing) {
                // Unlike
                \DB::table('community_post_likes')
                    ->where('post_id', $id)
                    ->where('user_id', $user->id)
                    ->delete();
                
                $post->decrement('likes_count');
                $liked = false;
            } else {
                // Like
                \DB::table('community_post_likes')->insert([
                    'post_id' => $id,
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $post->increment('likes_count');
                $liked = true;
            }
            
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $post->fresh()->likes_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling like: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Add comment to post (API)
    public function addComment(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            $request->validate([
                'content' => 'required|string|max:1000',
                'parent_id' => 'nullable|integer|exists:community_post_comments,id'
            ]);
            
            $post = \App\Models\CommunityPost::findOrFail($id);
            
            $commentId = \DB::table('community_post_comments')->insertGetId([
                'post_id' => $id,
                'user_id' => $user->id,
                'parent_id' => $request->parent_id,
                'content' => $request->content,
                'likes_count' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $post->increment('comments_count');
            
            // Get the comment with user info
            $comment = \DB::table('community_post_comments')
                ->join('users', 'users.id', '=', 'community_post_comments.user_id')
                ->where('community_post_comments.id', $commentId)
                ->select(
                    'community_post_comments.*',
                    'users.name as user_name',
                    'users.avatar as user_avatar'
                )
                ->first();
            
            return response()->json([
                'success' => true,
                'comment' => $comment,
                'comments_count' => $post->fresh()->comments_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding comment: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Get comments for a post (API)
    public function getComments(Request $request, $id)
    {
        try {
            // Use auth guard directly so it works without auth middleware
            $user = auth('sanctum')->user();
            
            // Get ALL comments for this post in one query
            $allComments = \DB::table('community_post_comments')
                ->join('users', 'users.id', '=', 'community_post_comments.user_id')
                ->where('community_post_comments.post_id', $id)
                ->select(
                    'community_post_comments.*',
                    'users.name as user_name',
                    'users.avatar as user_avatar'
                )
                ->orderBy('community_post_comments.created_at', 'asc')
                ->get();
            
            // Get liked comment IDs for current user
            $likedIds = [];
            if ($user) {
                $commentIds = $allComments->pluck('id')->toArray();
                if (!empty($commentIds)) {
                    $likedIds = \DB::table('community_comment_likes')
                        ->whereIn('comment_id', $commentIds)
                        ->where('user_id', $user->id)
                        ->pluck('comment_id')
                        ->toArray();
                }
            }
            
            // Set is_liked for each comment
            foreach ($allComments as &$comment) {
                $comment->is_liked = in_array($comment->id, $likedIds);
            }
            
            // Build recursive tree
            $tree = $this->buildCommentTree($allComments, null);
            
            // Reverse top-level to show newest first
            $tree = array_reverse($tree);
            
            return response()->json([
                'success' => true,
                'data' => $tree
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching comments: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Build comment tree recursively
    private function buildCommentTree($allComments, $parentId)
    {
        $branch = [];
        foreach ($allComments as $comment) {
            if ($comment->parent_id == $parentId) {
                $children = $this->buildCommentTree($allComments, $comment->id);
                $comment->replies = $children;
                $branch[] = $comment;
            }
        }
        return $branch;
    }
    
    // Toggle like on comment
    public function toggleCommentLike(Request $request, $commentId)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            $existing = \DB::table('community_comment_likes')
                ->where('comment_id', $commentId)
                ->where('user_id', $user->id)
                ->first();
            
            if ($existing) {
                \DB::table('community_comment_likes')
                    ->where('comment_id', $commentId)
                    ->where('user_id', $user->id)
                    ->delete();
                \DB::table('community_post_comments')
                    ->where('id', $commentId)
                    ->decrement('likes_count');
                $liked = false;
            } else {
                \DB::table('community_comment_likes')->insert([
                    'comment_id' => $commentId,
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                \DB::table('community_post_comments')
                    ->where('id', $commentId)
                    ->increment('likes_count');
                $liked = true;
            }
            
            $likesCount = \DB::table('community_post_comments')
                ->where('id', $commentId)
                ->value('likes_count');
            
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $likesCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Admin: Delete a comment
    public function adminDeleteComment(Request $request, $commentId)
    {
        try {
            $user = $request->user();
            if (!$user || !in_array($user->role, ['admin', 'editor'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $comment = \DB::table('community_post_comments')->find($commentId);
            if (!$comment) {
                return response()->json(['success' => false, 'message' => 'Comment không tồn tại'], 404);
            }

            // Delete all replies first (comments that have this as parent)
            $replyIds = \DB::table('community_post_comments')
                ->where('parent_id', $commentId)
                ->pluck('id')
                ->toArray();

            if (!empty($replyIds)) {
                // Delete likes for replies
                \DB::table('community_comment_likes')->whereIn('comment_id', $replyIds)->delete();
                // Delete replies
                \DB::table('community_post_comments')->whereIn('id', $replyIds)->delete();
            }

            // Delete likes for this comment
            \DB::table('community_comment_likes')->where('comment_id', $commentId)->delete();

            // Delete the comment
            \DB::table('community_post_comments')->where('id', $commentId)->delete();

            // Update comments_count on the post
            $repliesDeleted = count($replyIds);
            \App\Models\CommunityPost::where('id', $comment->post_id)
                ->decrement('comments_count', 1 + $repliesDeleted);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa comment' . ($repliesDeleted > 0 ? " và {$repliesDeleted} phản hồi" : '')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Admin: Get all comments for a post (flat list with user info)
    public function adminGetComments(Request $request, $postId)
    {
        try {
            $user = $request->user();
            if (!$user || !in_array($user->role, ['admin', 'editor'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $comments = \DB::table('community_post_comments')
                ->join('users', 'users.id', '=', 'community_post_comments.user_id')
                ->where('community_post_comments.post_id', $postId)
                ->select(
                    'community_post_comments.*',
                    'users.name as user_name',
                    'users.avatar as user_avatar',
                    'users.email as user_email'
                )
                ->orderBy('community_post_comments.created_at', 'asc')
                ->get();

            // Build tree structure
            $tree = $this->buildCommentTree($comments, null);

            // Get stats
            $stats = [
                'total' => $comments->count(),
                'root_comments' => $comments->whereNull('parent_id')->count(),
                'replies' => $comments->whereNotNull('parent_id')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $tree,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
