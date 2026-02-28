<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class ChatbotController extends Controller
{
    protected ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Get user ID from Bearer token (optional authentication)
     */
    protected function getUserIdFromToken(Request $request): ?int
    {
        $token = $request->bearerToken();
        if (!$token) {
            return null;
        }

        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken) {
            return null;
        }

        return $accessToken->tokenable_id;
    }

    /**
     * Send message to chatbot
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'session_id' => 'nullable|string|max:100',
            'conversation_id' => 'nullable|string|max:36',
        ]);

        $message = $request->input('message');
        $userId = $this->getUserIdFromToken($request);
        
        // Use client-provided session_id or generate one based on IP
        $sessionId = $request->input('session_id') ?? md5($request->ip() . $request->userAgent());
        
        // Use existing conversation_id or generate new one
        $conversationId = $request->input('conversation_id') ?? Str::uuid()->toString();

        // Rate limiting: 10 requests per minute
        $rateLimitKey = 'chatbot_rate_' . ($userId ?? $sessionId);
        $requests = cache()->get($rateLimitKey, 0);
        
        if ($requests >= 10) {
            return response()->json([
                'success' => false,
                'error' => 'Bạn đang gửi quá nhiều tin nhắn. Vui lòng đợi 1 phút.',
            ], 429);
        }
        
        cache()->put($rateLimitKey, $requests + 1, 60);

        // Process message
        $result = $this->chatbotService->processMessage($message, $userId, $sessionId, $conversationId);

        return response()->json([
            'success' => true,
            'data' => [
                'answer' => $result['answer'],
                'source' => $result['source'],
                'conversation_id' => $result['conversation_id'],
                'record_id' => $result['record_id'] ?? null,
                'response_time_ms' => $result['response_time_ms'],
            ],
        ]);
    }

    /**
     * Submit feedback for a conversation
     */
    public function submitFeedback(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|integer',
            'feedback' => 'required|in:good,bad',
        ]);

        $success = $this->chatbotService->processFeedback(
            $request->input('conversation_id'),
            $request->input('feedback')
        );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Cảm ơn phản hồi của bạn!' : 'Không thể lưu phản hồi',
        ]);
    }

    /**
     * Get suggested questions
     */
    public function getSuggestions()
    {
        return response()->json([
            'success' => true,
            'data' => $this->chatbotService->getSuggestions(),
        ]);
    }

    /**
     * Get conversation history (list of conversations)
     */
    public function getHistory(Request $request)
    {
        $userId = $this->getUserIdFromToken($request);
        $sessionId = $request->input('session_id');
        
        if (!$userId && !$sessionId) {
            return response()->json([
                'success' => false,
                'error' => 'Missing session_id'
            ], 400);
        }
        
        // Get conversations grouped by conversation_id ONLY
        $query = \DB::table('chatbot_conversations')
            ->select(
                'conversation_id',
                \DB::raw('MIN(id) as first_id'),
                \DB::raw('MAX(id) as last_id'),
                \DB::raw('COUNT(*) as message_count'),
                \DB::raw('MIN(question) as first_question'),
                \DB::raw('MIN(created_at) as started_at'),
                \DB::raw('MAX(created_at) as last_message_at')
            )
            ->whereNotNull('conversation_id');
            
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }
        
        $conversations = $query->groupBy('conversation_id')
            ->orderByDesc('last_message_at')
            ->limit(50)
            ->get();
        
        // Format conversations
        $formattedConversations = $conversations->map(function($conv) {
            // Generate title from first question
            $title = mb_strlen($conv->first_question) > 35 
                ? mb_substr($conv->first_question, 0, 35) . '...' 
                : $conv->first_question;
            
            // Format time
            $lastAt = new \DateTime($conv->last_message_at);
            $now = new \DateTime();
            $diff = $now->diff($lastAt);
            
            if ($diff->days == 0) {
                $timeStr = $lastAt->format('H:i');
            } elseif ($diff->days == 1) {
                $timeStr = 'Hôm qua';
            } elseif ($diff->days < 7) {
                $timeStr = $diff->days . ' ngày trước';
            } else {
                $timeStr = $lastAt->format('d/m');
            }
            
            return [
                'conversation_id' => $conv->conversation_id,
                'title' => $title,
                'message_count' => $conv->message_count,
                'time' => $timeStr,
                'started_at' => $conv->started_at,
                'last_message_at' => $conv->last_message_at,
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $formattedConversations,
        ]);
    }

    /**
     * Get messages for a specific conversation
     */
    public function getMessages(Request $request)
    {
        $userId = $this->getUserIdFromToken($request);
        $sessionId = $request->input('session_id');
        $conversationId = $request->input('conversation_id');
        
        if (!$userId && !$sessionId) {
            return response()->json([
                'success' => false,
                'error' => 'Missing session_id'
            ], 400);
        }
        
        if (!$conversationId) {
            return response()->json([
                'success' => false,
                'error' => 'Missing conversation_id'
            ], 400);
        }
        
        $query = \DB::table('chatbot_conversations')
            ->select('id', 'question', 'answer', 'source', 'feedback', 'created_at')
            ->where('conversation_id', $conversationId);
            
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }
        
        $messages = $query->orderBy('created_at', 'asc')->limit(100)->get();
        
        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    /**
     * Start a new conversation
     */
    public function newConversation(Request $request)
    {
        // Generate new conversation ID
        $conversationId = Str::uuid()->toString();
        
        return response()->json([
            'success' => true,
            'conversation_id' => $conversationId,
            'message' => 'Ready for new conversation'
        ]);
    }

    /**
     * Get chatbot statistics (admin only)
     */
    public function getStats(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stats = [
            'total_conversations' => \DB::table('chatbot_conversations')->count(),
            'today_conversations' => \DB::table('chatbot_conversations')
                ->whereDate('created_at', today())
                ->count(),
            'source_breakdown' => \DB::table('chatbot_conversations')
                ->select('source', \DB::raw('COUNT(*) as count'))
                ->groupBy('source')
                ->get(),
            'feedback_breakdown' => \DB::table('chatbot_conversations')
                ->select('feedback', \DB::raw('COUNT(*) as count'))
                ->whereNotNull('feedback')
                ->groupBy('feedback')
                ->get(),
            'learned_rules_count' => \DB::table('chatbot_learned_rules')
                ->where('is_active', true)
                ->count(),
            'avg_response_time' => \DB::table('chatbot_conversations')
                ->whereNotNull('response_time_ms')
                ->avg('response_time_ms'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
