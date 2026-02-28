<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Render profile page
    public function index($id = null)
    {
        return view('main.profile');
    }

    // Get profile data (API)
    public function getProfile(Request $request, $id)
    {
        try {
            $user = User::select('id', 'name', 'email', 'phone', 'avatar', 'bio', 'cover_image', 'address', 'birthday', 'gender', 'created_at')
                ->findOrFail($id);

            $currentUser = auth('sanctum')->user();
            $friendStatus = null;
            $friendshipId = null;

            if ($currentUser && $currentUser->id !== $user->id) {
                $friendship = DB::table('friendships')
                    ->where(function ($q) use ($currentUser, $user) {
                        $q->where('sender_id', $currentUser->id)->where('receiver_id', $user->id);
                    })
                    ->orWhere(function ($q) use ($currentUser, $user) {
                        $q->where('sender_id', $user->id)->where('receiver_id', $currentUser->id);
                    })
                    ->first();

                if ($friendship) {
                    $friendshipId = $friendship->id;
                    if ($friendship->status === 'accepted') {
                        $friendStatus = 'friends';
                    } elseif ($friendship->status === 'pending') {
                        $friendStatus = $friendship->sender_id == $currentUser->id ? 'sent' : 'received';
                    }
                }
            } elseif ($currentUser && $currentUser->id === $user->id) {
                $friendStatus = 'self';
            }

            // Count friends
            $friendsCount = DB::table('friendships')
                ->where('status', 'accepted')
                ->where(function ($q) use ($user) {
                    $q->where('sender_id', $user->id)->orWhere('receiver_id', $user->id);
                })
                ->count();

            // Get full friends list (for card preview + "Xem tất cả" modal)
            $friendIds = DB::table('friendships')
                ->where('status', 'accepted')
                ->where(function ($q) use ($user) {
                    $q->where('sender_id', $user->id)->orWhere('receiver_id', $user->id);
                })
                ->get()
                ->map(function ($f) use ($user) {
                    return $f->sender_id == $user->id ? $f->receiver_id : $f->sender_id;
                });

            $friends = User::select('id', 'name', 'avatar')
                ->whereIn('id', $friendIds)
                ->orderBy('name')
                ->get();

            // Get user's community posts
            $posts = DB::table('community_posts')
                ->join('users', 'users.id', '=', 'community_posts.user_id')
                ->where('community_posts.user_id', $user->id)
                ->select(
                    'community_posts.*',
                    'users.name as user_name',
                    'users.avatar as user_avatar'
                )
                ->orderBy('community_posts.created_at', 'desc')
                ->limit(10)
                ->get();

            // Add is_liked for authenticated user (for post cards like community)
            $currentUser = auth('sanctum')->user();
            foreach ($posts as $post) {
                $post->is_liked = $currentUser
                    ? DB::table('community_post_likes')
                        ->where('post_id', $post->id)
                        ->where('user_id', $currentUser->id)
                        ->exists()
                    : false;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'friend_status' => $friendStatus,
                    'friendship_id' => $friendshipId,
                    'friends_count' => $friendsCount,
                    'friends' => $friends,
                    'posts' => $posts,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found: ' . $e->getMessage()
            ], 404);
        }
    }

    // Update profile (API)
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $request->validate([
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|nullable|string|max:20',
                'bio' => 'sometimes|nullable|string|max:500',
                'address' => 'sometimes|nullable|string|max:500',
                'birthday' => 'sometimes|nullable|date',
                'gender' => 'sometimes|nullable|in:male,female,other',
            ]);

            $user->update($request->only(['name', 'phone', 'bio', 'address', 'birthday', 'gender']));

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Upload avatar (API)
    public function uploadAvatar(Request $request)
    {
        try {
            $request->validate(['avatar' => 'required|image|max:5120']);
            $user = $request->user();

            $path = $request->file('avatar')->store('avatars', 'public');
            $fullUrl = asset('storage/' . $path);
            $user->update(['avatar' => $fullUrl]);

            return response()->json([
                'success' => true,
                'avatar' => $fullUrl
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Upload cover (API)
    public function uploadCover(Request $request)
    {
        try {
            $request->validate(['cover' => 'required|image|max:10240']);
            $user = $request->user();

            $path = $request->file('cover')->store('covers', 'public');
            $fullUrl = asset('storage/' . $path);
            $user->update(['cover_image' => $fullUrl]);

            return response()->json([
                'success' => true,
                'cover_image' => $fullUrl
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // === FRIEND SYSTEM ===
    
    // Send friend request
    public function sendFriendRequest(Request $request, $userId)
    {
        try {
            $currentUser = $request->user();
            if ($currentUser->id == $userId) {
                return response()->json(['success' => false, 'message' => 'Không thể kết bạn với chính mình'], 400);
            }

            $existing = DB::table('friendships')
                ->where(function ($q) use ($currentUser, $userId) {
                    $q->where('sender_id', $currentUser->id)->where('receiver_id', $userId);
                })
                ->orWhere(function ($q) use ($currentUser, $userId) {
                    $q->where('sender_id', $userId)->where('receiver_id', $currentUser->id);
                })
                ->first();

            if ($existing) {
                return response()->json(['success' => false, 'message' => 'Đã có lời mời kết bạn'], 400);
            }

            DB::table('friendships')->insert([
                'sender_id' => $currentUser->id,
                'receiver_id' => $userId,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Đã gửi lời mời kết bạn']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Accept friend request
    public function acceptFriendRequest(Request $request, $friendshipId)
    {
        try {
            $currentUser = $request->user();
            $friendship = DB::table('friendships')
                ->where('id', $friendshipId)
                ->where('receiver_id', $currentUser->id)
                ->where('status', 'pending')
                ->first();

            if (!$friendship) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy lời mời'], 404);
            }

            DB::table('friendships')
                ->where('id', $friendshipId)
                ->update(['status' => 'accepted', 'updated_at' => now()]);

            return response()->json(['success' => true, 'message' => 'Đã chấp nhận lời mời kết bạn']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Reject/Cancel/Unfriend
    public function removeFriend(Request $request, $friendshipId)
    {
        try {
            $currentUser = $request->user();
            DB::table('friendships')
                ->where('id', $friendshipId)
                ->where(function ($q) use ($currentUser) {
                    $q->where('sender_id', $currentUser->id)->orWhere('receiver_id', $currentUser->id);
                })
                ->delete();

            return response()->json(['success' => true, 'message' => 'Đã xóa']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Get friend requests received
    public function getFriendRequests(Request $request)
    {
        try {
            $user = $request->user();
            $requests = DB::table('friendships')
                ->join('users', 'users.id', '=', 'friendships.sender_id')
                ->where('friendships.receiver_id', $user->id)
                ->where('friendships.status', 'pending')
                ->select('friendships.id as friendship_id', 'users.id', 'users.name', 'users.avatar', 'friendships.created_at')
                ->orderBy('friendships.created_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'data' => $requests]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // === MESSAGING SYSTEM ===

    // Get chat list for messenger sidebar (friends + anyone who sent messages)
    public function getFriendsForChat(Request $request)
    {
        try {
            $user = $request->user();

            // Get all accepted friend IDs
            $friendships = DB::table('friendships')
                ->where('status', 'accepted')
                ->where(function ($q) use ($user) {
                    $q->where('sender_id', $user->id)->orWhere('receiver_id', $user->id);
                })
                ->get();

            $friendIds = $friendships->map(function ($f) use ($user) {
                return $f->sender_id == $user->id ? $f->receiver_id : $f->sender_id;
            })->toArray();

            // Get IDs of users who have exchanged messages with current user (including admins)
            $messageUserIds = DB::table('messages')
                ->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id)
                ->get()
                ->map(function ($m) use ($user) {
                    return $m->sender_id == $user->id ? $m->receiver_id : $m->sender_id;
                })
                ->unique()
                ->toArray();

            // Merge friend IDs and message user IDs (remove duplicates)
            $allUserIds = array_values(array_unique(array_merge($friendIds, $messageUserIds)));

            if (empty($allUserIds)) {
                return response()->json(['success' => true, 'data' => []]);
            }

            // Get users with last message info
            $placeholders = implode(',', array_fill(0, count($allUserIds), '?'));
            $params = array_merge(
                [$user->id], // for unread_count subquery
                [$user->id, $user->id, $user->id, $user->id], // for message subquery
                $allUserIds // for WHERE u.id IN (...)
            );

            $chatUsers = DB::select("
                SELECT u.id, u.name, u.avatar, u.role,
                    m.content as last_message,
                    m.created_at as last_message_time,
                    m.sender_id as last_sender_id,
                    COALESCE((SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = ? AND read_at IS NULL), 0) as unread_count
                FROM users u
                LEFT JOIN (
                    SELECT 
                        CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as other_user_id,
                        content, created_at, sender_id,
                        ROW_NUMBER() OVER (PARTITION BY CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END ORDER BY created_at DESC) as rn
                    FROM messages 
                    WHERE sender_id = ? OR receiver_id = ?
                ) m ON u.id = m.other_user_id AND m.rn = 1
                WHERE u.id IN ({$placeholders})
                ORDER BY 
                    CASE WHEN m.created_at IS NOT NULL THEN 0 ELSE 1 END,
                    m.created_at DESC,
                    u.name ASC
            ", $params);

            return response()->json(['success' => true, 'data' => $chatUsers]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Get all users for admin to chat (Admin only)
    public function getAllUsersForChat(Request $request)
    {
        try {
            $user = $request->user();

            // Only admin can access this
            if ($user->role !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $search = $request->query('search', '');
            $perPage = 50; // Get more users for better realtime experience

            // Build params in order of SQL placeholders
            $searchCondition = '';
            $params = [
                $user->id, // 1. unread_count subquery: receiver_id = ?
                $user->id, // 2. CASE WHEN sender_id = ?
                $user->id, // 3. PARTITION BY CASE WHEN sender_id = ?
                $user->id, // 4. WHERE sender_id = ?
                $user->id, // 5. OR receiver_id = ?
                $user->id, // 6. WHERE u.id != ?
            ];
            
            if ($search) {
                $searchCondition = "AND (u.name LIKE ? OR u.email LIKE ?)";
                $params[] = "%{$search}%";
                $params[] = "%{$search}%";
            }
            
            $params[] = $perPage; // LIMIT ?

            $chatUsers = DB::select("
                SELECT u.id, u.name, u.email, u.avatar, u.role,
                    m.content as last_message,
                    m.created_at as last_message_time,
                    m.sender_id as last_sender_id,
                    COALESCE((SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = ? AND read_at IS NULL), 0) as unread_count
                FROM users u
                LEFT JOIN (
                    SELECT 
                        CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as other_user_id,
                        content, created_at, sender_id,
                        ROW_NUMBER() OVER (PARTITION BY CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END ORDER BY created_at DESC) as rn
                    FROM messages 
                    WHERE sender_id = ? OR receiver_id = ?
                ) m ON u.id = m.other_user_id AND m.rn = 1
                WHERE u.id != ?
                {$searchCondition}
                ORDER BY 
                    CASE WHEN m.created_at IS NOT NULL THEN 0 ELSE 1 END,
                    m.created_at DESC,
                    u.name ASC
                LIMIT ?
            ", $params);

            return response()->json([
                'success' => true,
                'data' => $chatUsers
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Get conversations list
    public function getConversations(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get latest message with each user
            $conversations = DB::select("
                SELECT u.id, u.name, u.avatar, m.content as last_message, m.created_at as last_message_time,
                    m.sender_id as last_sender_id,
                    (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = ? AND read_at IS NULL) as unread_count
                FROM users u
                INNER JOIN (
                    SELECT 
                        CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as other_user_id,
                        content, created_at, sender_id,
                        ROW_NUMBER() OVER (PARTITION BY CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END ORDER BY created_at DESC) as rn
                    FROM messages 
                    WHERE sender_id = ? OR receiver_id = ?
                ) m ON u.id = m.other_user_id AND m.rn = 1
                ORDER BY m.created_at DESC
            ", [$user->id, $user->id, $user->id, $user->id, $user->id]);

            return response()->json(['success' => true, 'data' => $conversations]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Get messages with a user
    public function getMessages(Request $request, $userId)
    {
        try {
            $user = $request->user();
            
            $messages = DB::table('messages')
                ->where(function ($q) use ($user, $userId) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $userId);
                })
                ->orWhere(function ($q) use ($user, $userId) {
                    $q->where('sender_id', $userId)->where('receiver_id', $user->id);
                })
                ->orderBy('created_at', 'asc')
                ->limit(100)
                ->get();

            // Mark as read
            DB::table('messages')
                ->where('sender_id', $userId)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['success' => true, 'data' => $messages]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Send message
    public function sendMessage(Request $request, $userId)
    {
        try {
            $user = $request->user();
            $request->validate(['content' => 'required|string|max:2000']);

            // Check if receiver exists
            $receiver = User::find($userId);
            if (!$receiver) {
                return response()->json(['success' => false, 'message' => 'Người dùng không tồn tại'], 404);
            }

            // Admin can message anyone without being friends
            if ($user->role !== 'admin') {
                // Regular users must be friends to message
                $areFriends = DB::table('friendships')
                    ->where('status', 'accepted')
                    ->where(function ($q) use ($user, $userId) {
                        $q->where(function ($q2) use ($user, $userId) {
                            $q2->where('sender_id', $user->id)->where('receiver_id', $userId);
                        })->orWhere(function ($q2) use ($user, $userId) {
                            $q2->where('sender_id', $userId)->where('receiver_id', $user->id);
                        });
                    })
                    ->exists();

                if (!$areFriends) {
                    return response()->json(['success' => false, 'message' => 'Bạn cần kết bạn để nhắn tin'], 403);
                }
            }

            $messageId = DB::table('messages')->insertGetId([
                'sender_id' => $user->id,
                'receiver_id' => $userId,
                'content' => $request->content,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $message = DB::table('messages')->find($messageId);

            return response()->json(['success' => true, 'data' => $message]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Get unread count
    public function getUnreadCount(Request $request)
    {
        try {
            $user = $request->user();
            $count = DB::table('messages')
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->count();

            return response()->json(['success' => true, 'count' => $count])
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    // Search user by name (for @mention hover card and messenger search)
    public function searchUserByName(Request $request)
    {
        try {
            $name = $request->query('name');
            if (!$name) {
                return response()->json(['success' => false, 'message' => 'Name required'], 400);
            }

            $users = User::select('id', 'name', 'avatar')
                ->where('name', 'LIKE', '%' . $name . '%')
                ->limit(10)
                ->get();

            if ($users->count() > 0) {
                return response()->json(['success' => true, 'data' => $users]);
            }

            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
