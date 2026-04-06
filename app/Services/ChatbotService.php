<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\ProductSimple;
use App\Models\User;

class ChatbotService
{
    /**
     * Các categories trong hệ thống
     */
    protected array $categories = [
        'action' => ['hành động', 'action', 'bắn súng', 'shooter', 'fps'],
        'adventure' => ['phiêu lưu', 'adventure', 'khám phá'],
        'rpg' => ['rpg', 'nhập vai', 'role playing', 'role-playing'],
        'sports' => ['thể thao', 'sports', 'bóng đá', 'football', 'fifa', 'pes'],
        'racing' => ['đua xe', 'racing', 'race', 'drift', 'tốc độ', 'đường đua', 'xe đua'],
        'strategy' => ['chiến thuật', 'strategy', 'rts'],
        'simulation' => ['mô phỏng', 'simulation', 'sim'],
        'puzzle' => ['giải đố', 'puzzle', 'logic'],
        'horror' => ['kinh dị', 'horror', 'scary'],
        'fighting' => ['đối kháng', 'fighting', 'võ thuật'],
        'mmo' => ['mmo', 'online', 'multiplayer', 'nhiều người'],
    ];

    protected array $racingKeywords = [
        'đua xe', 'racing', 'race', 'drift', 'tốc độ', 'speed',
        'đường đua', 'xe đua', 'formula', 'nascar', 'rally',
        'need for speed', 'nfs', 'forza', 'gran turismo',
        'asphalt', 'f1', 'dirt', 'wrc', 'dua xe',
        'đua ô tô', 'đua mô tô', 'tay đua', 'siêu xe',
        'mario kart', 'burnout', 'crew', 'hot wheels',
    ];

    protected array $fixedRules = [
        [
            'patterns' => ['bạn là ai', 'giới thiệu bản thân', 'who are you', 'tên bạn là gì', 'bạn tên gì', 'you are', 'introduce yourself'],
            'intent' => 'self_intro',
            'response' => "👋 **Xin chào! Tôi là GameTech AI**\n\n🤖 Tôi là trợ lý thông minh của **GameTech Game Store**!\n\n✨ **Tôi có thể:**\n• 🎮 Tìm và gợi ý game phù hợp với bạn\n• 💰 Tra cứu giá cả và thông tin game\n• 📦 Kiểm tra đơn hàng của bạn\n• 💳 Hướng dẫn thanh toán, nạp tiền\n• 💬 Trò chuyện và lắng nghe bạn\n\n🌟 Tôi được huấn luyện để hiểu nhiều chủ đề khác nhau - không chỉ về game!\n\nHãy hỏi tôi bất cứ điều gì nhé! 😊",
        ],
    ];

    /**
     * Xử lý tin nhắn và trả lời
     */
    public function processMessage(string $message, ?int $userId = null, ?string $sessionId = null, ?string $conversationId = null): array
    {
        $startTime = microtime(true);
        $message = trim($message);
        $lowerMessage = mb_strtolower($message, 'UTF-8');
        
        // 1. Check Fixed Rules
        $fixedResponse = $this->checkFixedRules($lowerMessage);
        if ($fixedResponse) {
            return $this->saveAndReturn($message, $fixedResponse['response'], 'rule', $userId, $sessionId, $conversationId, $startTime);
        }
        
        // 2. Check Learned Rules
        $learnedResponse = $this->checkLearnedRules($lowerMessage, $userId);
        if ($learnedResponse) {
            return $this->saveAndReturn($message, $learnedResponse['response'], 'learned', $userId, $sessionId, $conversationId, $startTime, $learnedResponse['rule_id']);
        }
        
        // 2.5. Racing game fallback — chuyên gia game đua xe, gọi Gemini với context chuyên sâu
        $racingResponse = $this->handleRacingGameFallback($message, $lowerMessage, $userId, $conversationId);
        if ($racingResponse) {
            return $this->saveAndReturn($message, $racingResponse['response'], 'gemini', $userId, $sessionId, $conversationId, $startTime, null, $racingResponse['products'] ?? []);
        }
        
        // 3. Check Product/Category queries — dữ liệu game chỉ là nguồn, Gemini trả lời tự nhiên
        $productResponse = $this->handleProductQuery($message, $lowerMessage, $userId);
        if ($productResponse) {
            $conversationHistory = $this->getConversationHistory($conversationId);
            $products = ProductSimple::whereIn('id', $productResponse['products'] ?? [])->get();
            $productContextText = $this->buildProductContextText($products);
            $geminiResponse = $this->callGeminiAI($message, $userId, $conversationHistory, $productContextText);
            return $this->saveAndReturn($message, $geminiResponse, 'gemini', $userId, $sessionId, $conversationId, $startTime, null, $productResponse['products'] ?? []);
        }
        
        // 4. Check Order query
        if ($userId) {
            $orderResponse = $this->handleOrderQuery($lowerMessage, $userId);
            if ($orderResponse) {
                return $this->saveAndReturn($message, $orderResponse, 'rule', $userId, $sessionId, $conversationId, $startTime);
            }
        }
        
        // 5. Follow-up: user muốn tìm hiểu kỹ về 1 game / 1 thể loại — dữ liệu game là nguồn, Gemini trả lời tự nhiên
        $conversationHistory = $this->getConversationHistory($conversationId);
        $followUpResponse = $this->handleFollowUpGameOrCategory($message, $lowerMessage, $conversationHistory, $userId);
        if ($followUpResponse) {
            $products = ProductSimple::whereIn('id', $followUpResponse['products'] ?? [])->get();
            $productContextText = $this->buildProductContextText($products);
            $geminiResponse = $this->callGeminiAI($message, $userId, $conversationHistory, $productContextText);
            return $this->saveAndReturn($message, $geminiResponse, 'gemini', $userId, $sessionId, $conversationId, $startTime, null, $followUpResponse['products'] ?? []);
        }
        
        // 6. Call Gemini AI with conversation history for context
        $geminiResponse = $this->callGeminiAI($message, $userId, $conversationHistory);
        return $this->saveAndReturn($message, $geminiResponse, 'gemini', $userId, $sessionId, $conversationId, $startTime);
    }

    /**
     * Get conversation history for context
     */
    protected function getConversationHistory(?string $conversationId): array
    {
        if (!$conversationId) {
            return [];
        }

        // Get last 10 messages from this conversation for context
        $messages = DB::table('chatbot_conversations')
            ->where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get(['question', 'answer']);

        $history = [];
        foreach ($messages as $msg) {
            $history[] = ['role' => 'user', 'content' => $msg->question];
            $history[] = ['role' => 'model', 'content' => $msg->answer];
        }

        return $history;
    }

    /**
     * Check fixed rules
     */
    protected function checkFixedRules(string $lowerMessage): ?array
    {
        foreach ($this->fixedRules as $rule) {
            foreach ($rule['patterns'] as $pattern) {
                // Use word boundary matching to avoid false positives
                // e.g. "thi" should not match "hi"
                if ($this->matchWholeWord($lowerMessage, $pattern)) {
                    return [
                        'intent' => $rule['intent'],
                        'response' => $this->processResponseLinks($rule['response']),
                    ];
                }
            }
        }
        return null;
    }

    /**
     * Process response to add actual URLs
     */
    protected function processResponseLinks(string $response): string
    {
        $baseUrl = config('app.url', 'http://127.0.0.1:8000');
        return str_replace('{url}', $baseUrl, $response);
    }

    /**
     * Check if pattern matches as a whole word (not substring)
     */
    protected function matchWholeWord(string $text, string $pattern): bool
    {
        // For short patterns (1-2 chars), require word boundaries
        // For longer patterns, allow substring matching for phrases
        if (mb_strlen($pattern) <= 3) {
            // Use regex with word boundaries for short patterns
            $escapedPattern = preg_quote($pattern, '/');
            return (bool) preg_match('/(?:^|[\s,.!?])' . $escapedPattern . '(?:[\s,.!?]|$)/ui', $text);
        }
        
        // For longer patterns, simple contains is fine
        return str_contains($text, $pattern);
    }

    /**
     * Check learned rules from database
     */
    protected function checkLearnedRules(string $lowerMessage, ?int $userId): ?array
    {
        $rules = Cache::remember('chatbot_learned_rules', 300, function () {
            return DB::table('chatbot_learned_rules')
                ->where('is_active', true)
                ->where('confidence_score', '>=', 0.5)
                ->orderByDesc('confidence_score')
                ->orderByDesc('usage_count')
                ->get();
        });

        foreach ($rules as $rule) {
            if (@preg_match($rule->pattern, $lowerMessage)) {
                // Increment usage count
                DB::table('chatbot_learned_rules')
                    ->where('id', $rule->id)
                    ->increment('usage_count');
                
                // If dynamic, process template
                $response = $rule->response_template;
                if ($rule->response_type === 'dynamic') {
                    $response = $this->processDynamicTemplate($response, $lowerMessage, $userId);
                }
                
                // Add links to response
                $response = $this->addLinksToResponse($response);
                
                return [
                    'rule_id' => $rule->id,
                    'response' => $response,
                ];
            }
        }
        return null;
    }

    /**
     * Add links to game names and navigation in response
     */
    protected function addLinksToResponse(string $response): string
    {
        $baseUrl = config('app.url', 'http://127.0.0.1:8000');
        
        // Skip if already has markdown links
        if (str_contains($response, '](')) {
            return $response;
        }
        
        // Replace common navigation terms with links
        $navReplacements = [
            '**Cửa hàng**' => "[**Cửa hàng**]({$baseUrl}/store)",
            '**Đơn hàng**' => "[**Đơn hàng**]({$baseUrl}/orders)",
            '**Hỗ trợ**' => "[**Hỗ trợ**]({$baseUrl}/support)",
            '**Cộng đồng**' => "[**Cộng đồng**]({$baseUrl}/community)",
            '**Ví của tôi**' => "[**Ví của tôi**]({$baseUrl}/wallet)",
            '**Tin tức**' => "[**Tin tức**]({$baseUrl}/news)",
        ];
        
        foreach ($navReplacements as $search => $replacement) {
            $response = str_replace($search, $replacement, $response);
        }
        
        // Try to find and link game names from database
        // Pattern: **1. Game Name** (with numbered list items)
        if (preg_match_all('/\*\*(\d+)\.\s*(.+?)\*\*/u', $response, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $fullMatch = $match[0]; // e.g. **1. GTA: San Andreas**
                $number = $match[1];     // e.g. 1
                $gameName = trim($match[2]); // e.g. GTA: San Andreas
                
                // Skip if too short
                if (mb_strlen($gameName) < 3) {
                    continue;
                }
                
                // Extract main game name (before " – " or " - " or "(" )
                $searchName = $gameName;
                if (preg_match('/^(.+?)(?:\s*[–\-\(])/u', $gameName, $nameMatch)) {
                    $searchName = trim($nameMatch[1]);
                }
                
                // Search for this game in database
                $game = ProductSimple::where('title', 'LIKE', "%{$searchName}%")->first();
                if ($game) {
                    $gameUrl = "{$baseUrl}/game/{$game->id}";
                    // Replace with linked version
                    $linked = "[**{$number}. {$gameName}**]({$gameUrl})";
                    $response = str_replace($fullMatch, $linked, $response);
                }
            }
        }
        
        return $response;
    }

    /**
     * Handle product/category queries
     */
    protected function handleProductQuery(string $message, string $lowerMessage, ?int $userId): ?array
    {
        $foundProducts = [];
        $response = null;
        
        // Skip contextual/follow-up questions - let Gemini handle with conversation history
        // These questions need context from previous messages
        $contextualPatterns = [
            '/\b(vậy|thì|nên|nào|gì)\b.*\?*$/iu',  // "vậy tôi nên...", "game nào...", "chơi gì..."
            '/^(vậy|thế|rồi|còn)\s/iu',             // Starts with context words
            '/nên\s+(chơi|mua|chọn)\s+(game\s+)?(gì|nào)/iu', // "nên chơi game gì"
        ];
        
        foreach ($contextualPatterns as $pattern) {
            if (preg_match($pattern, $lowerMessage)) {
                return null; // Let Gemini handle with context
            }
        }
        
        // 1. Search for specific game
        $gamePatterns = [
            '/(?:game|trò chơi|tìm|kiếm|có)\s+(?:tên\s+)?["\']?([^"\'?]+)["\']?(?:\s+không|\s+hay|\s+giá)?/iu',
            '/([a-zA-Z0-9\s]{3,30})\s+(?:giá|bao nhiêu|có hay|có không)/iu',
        ];
        
        foreach ($gamePatterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $searchTerm = trim($matches[1]);
                // Skip if search term is a question word
                if (in_array(mb_strtolower($searchTerm), ['gì', 'nào', 'gì?', 'nào?', 'j', 'gi'])) {
                    continue;
                }
                if (strlen($searchTerm) >= 3) {
                    $products = ProductSimple::where('title', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('short_description', 'LIKE', "%{$searchTerm}%")
                        ->limit(5)
                        ->get();
                    
                    if ($products->isNotEmpty()) {
                        $foundProducts = $products->pluck('id')->toArray();
                        $response = $this->formatProductsResponse($products, "Đây là kết quả tìm kiếm cho '{$searchTerm}':");
                        
                        // Log for recommendation
                        if ($userId) {
                            $this->logProductInterest($userId, $foundProducts, 2.0);
                        }
                        
                        return ['response' => $response, 'products' => $foundProducts];
                    }
                }
            }
        }
        
        // 2. Search by category
        foreach ($this->categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($lowerMessage, $keyword)) {
                    $products = ProductSimple::where('category', 'LIKE', "%{$category}%")
                        ->orWhere('type', 'LIKE', "%{$category}%")
                        ->orderByDesc('view_count')
                        ->limit(5)
                        ->get();
                    
                    if ($products->isNotEmpty()) {
                        $foundProducts = $products->pluck('id')->toArray();
                        $categoryName = $this->getCategoryDisplayName($category);
                        $response = $this->formatProductsResponse($products, "Game {$categoryName} hay nhất:");
                        
                        // Log for recommendation
                        if ($userId) {
                            $this->logProductInterest($userId, $foundProducts, 1.5);
                        }
                        
                        return ['response' => $response, 'products' => $foundProducts];
                    }
                }
            }
        }
        
        // 3. Price query
        if (preg_match('/(?:giá|bao nhiêu|price)/iu', $lowerMessage)) {
            // Extract game name and search
            $cleanMessage = preg_replace('/(?:giá|bao nhiêu|tiền|price|của|game)/iu', '', $lowerMessage);
            $cleanMessage = trim($cleanMessage);
            
            if (strlen($cleanMessage) >= 3) {
                $product = ProductSimple::where('title', 'LIKE', "%{$cleanMessage}%")->first();
                if ($product) {
                    $formattedPrice = $this->formatPrice($product->price);
                    $response = "🎮 **{$product->title}**\n\n💰 **Giá:** {$formattedPrice}\n\n📝 {$product->short_description}\n\n👉 _Vào **Cửa hàng** để mua game này!_";
                    
                    if ($userId) {
                        $this->logProductInterest($userId, [$product->id], 2.0);
                    }
                    
                    return ['response' => $response, 'products' => [$product->id]];
                }
            }
        }
        
        // 4. New games
        if (preg_match('/(?:game mới|mới ra|new|latest|mới nhất)/iu', $lowerMessage)) {
            $products = ProductSimple::orderByDesc('id')->limit(5)->get();
            if ($products->isNotEmpty()) {
                $foundProducts = $products->pluck('id')->toArray();
                $response = $this->formatProductsResponse($products, "Game mới nhất tại GameTech:");
                
                if ($userId) {
                    $this->logProductInterest($userId, $foundProducts, 1.0);
                }
                
                return ['response' => $response, 'products' => $foundProducts];
            }
        }
        
        // 5. Popular/Hot games
        if (preg_match('/(?:hot|phổ biến|popular|bán chạy|hay nhất|best)/iu', $lowerMessage)) {
            $products = ProductSimple::orderByDesc('view_count')->limit(5)->get();
            if ($products->isNotEmpty()) {
                $foundProducts = $products->pluck('id')->toArray();
                $response = $this->formatProductsResponse($products, "Game hot nhất hiện nay:");
                
                if ($userId) {
                    $this->logProductInterest($userId, $foundProducts, 1.0);
                }
                
                return ['response' => $response, 'products' => $foundProducts];
            }
        }
        
        return null;
    }

    /**
     * Handle order query
     */
    protected function handleOrderQuery(string $lowerMessage, int $userId): ?string
    {
        if (!preg_match('/(?:đơn hàng|order|mua|giao dịch)/iu', $lowerMessage)) {
            return null;
        }
        
        $baseUrl = config('app.url', 'http://127.0.0.1:8000');
        
        $orders = DB::table('orders')
            ->where('buyer_id', $userId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        
        if ($orders->isEmpty()) {
            return "Bạn chưa có đơn hàng nào. 📦\n\nHãy khám phá [**Cửa hàng**]({$baseUrl}/store) và mua game đầu tiên nhé! 🎮";
        }
        
        $response = "📦 **Đơn hàng gần đây của bạn:**\n\n";
        foreach ($orders as $order) {
            $statusEmoji = match($order->status) {
                'completed' => '✅',
                'pending' => '⏳',
                'processing' => '🔄',
                'cancelled' => '❌',
                default => '📋',
            };
            $statusText = match($order->status) {
                'completed' => 'Hoàn thành',
                'pending' => 'Chờ xử lý',
                'processing' => 'Đang xử lý',
                'cancelled' => 'Đã hủy',
                default => $order->status,
            };
            $price = number_format($order->amount, 0, ',', '.') . 'đ';
            $date = date('d/m/Y', strtotime($order->created_at));
            
            $response .= "{$statusEmoji} **#{$order->order_code}** - {$statusText}\n";
            $response .= "   💰 {$price} | 📅 {$date}\n\n";
        }
        
        $response .= "👉 [**Xem tất cả đơn hàng**]({$baseUrl}/orders)";
        return $response;
    }

    /**
     * Xử lý khi user muốn tìm hiểu kỹ về 1 game hoặc 1 thể loại đã được gợi ý trong hội thoại.
     * Trích xuất từ tin nhắn bot gần nhất và trả về game thật từ DB (có link).
     */
    protected function handleFollowUpGameOrCategory(string $message, string $lowerMessage, array $conversationHistory, ?int $userId): ?array
    {
        if (empty($conversationHistory)) {
            return null;
        }

        // Có phải câu follow-up về game/thể loại không?
        $followUpPatterns = [
            '/game\s+đó|thể\s+loại\s+đó|game\s+ấy|thể\s+loại\s+ấy/iu',
            '/tìm\s+hiểu\s+(thêm|kỹ|về)/iu',
            '/kể\s+thêm|gợi\s+ý\s+thêm|chi\s+tiết\s+(về|game)?/iu',
            '/game\s+liên\s+quan|game\s+tương\s+tự/iu',
            '/giới\s+thiệu\s+(game|chi\s+tiết)/iu',
            '/game\s+nào\s+hay|cụ\s+thể\s+(là|game)|ví\s+dụ\s+game/iu',
            '/muốn\s+(xem|biết)\s+(thêm|về)\s+game/iu',
        ];
        $isFollowUp = false;
        foreach ($followUpPatterns as $pattern) {
            if (preg_match($pattern, $lowerMessage)) {
                $isFollowUp = true;
                break;
            }
        }
        if (!$isFollowUp) {
            return null;
        }

        // Lấy tin nhắn bot gần nhất (câu trả lời cuối)
        $lastBotContent = '';
        for ($i = count($conversationHistory) - 1; $i >= 0; $i--) {
            if (($conversationHistory[$i]['role'] ?? '') === 'model') {
                $lastBotContent = $conversationHistory[$i]['content'] ?? '';
                break;
            }
        }
        if ($lastBotContent === '') {
            return null;
        }

        $lastBotLower = mb_strtolower($lastBotContent, 'UTF-8');
        $baseUrl = config('app.url', 'http://127.0.0.1:8000');

        // 1. Trích thể loại từ câu bot (phiêu lưu, hành động, rpg, ...)
        $matchedCategory = null;
        foreach ($this->categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (mb_strlen($keyword) >= 2 && str_contains($lastBotLower, $keyword)) {
                    $matchedCategory = $category;
                    break 2;
                }
            }
        }

        if ($matchedCategory !== null) {
            $products = ProductSimple::where('category', 'LIKE', "%{$matchedCategory}%")
                ->orWhere('type', 'LIKE', "%{$matchedCategory}%")
                ->orderByDesc('view_count')
                ->limit(6)
                ->get();
            if ($products->isNotEmpty()) {
                $categoryName = $this->getCategoryDisplayName($matchedCategory);
                $response = $this->formatProductsResponse($products, "Game {$categoryName} hay tại GameTech (gợi ý chi tiết):");
                if ($userId) {
                    $this->logProductInterest($userId, $products->pluck('id')->toArray(), 1.5);
                }
                return ['response' => $response, 'products' => $products->pluck('id')->toArray()];
            }
        }

        // 2. Trích tên game từ câu bot: **Tên game**, "game X", "1. Tên game", [Tên](url)
        $possibleGameNames = [];
        if (preg_match_all('/\*\*(.+?)\*\*/u', $lastBotContent, $m)) {
            foreach ($m[1] as $t) {
                $t = trim($t);
                if (preg_match('/^\d+\.\s*/', $t)) {
                    $t = preg_replace('/^\d+\.\s*/', '', $t);
                }
                if (mb_strlen($t) >= 3 && !preg_match('/^(cửa hàng|đơn hàng|hỗ trợ|game|link|url)$/iu', $t)) {
                    $possibleGameNames[] = $t;
                }
            }
        }
        if (preg_match_all('/\[([^\]]+)\]\([^)]+\)/u', $lastBotContent, $m)) {
            foreach ($m[1] as $t) {
                $t = trim(preg_replace('/^\d+\.\s*/', '', $t));
                if (mb_strlen($t) >= 3) {
                    $possibleGameNames[] = $t;
                }
            }
        }
        // Số + tên (1. GTA V, 2. Red Dead)
        if (preg_match_all('/\d+\.\s*([^\n*\[\]]{3,60})/u', $lastBotContent, $m)) {
            foreach ($m[1] as $t) {
                $t = trim($t);
                if (mb_strlen($t) >= 3) {
                    $possibleGameNames[] = $t;
                }
            }
        }

        foreach (array_slice(array_unique($possibleGameNames), 0, 3) as $searchName) {
            $product = ProductSimple::where('title', 'LIKE', '%' . $searchName . '%')->first();
            if ($product) {
                $products = ProductSimple::where('id', $product->id)
                    ->orWhere('category', $product->category)
                    ->orderByDesc('view_count')
                    ->limit(5)
                    ->get();
                if ($products->isEmpty()) {
                    $products = collect([$product]);
                }
                $response = $this->formatProductsResponse($products, "Chi tiết game và game liên quan:");
                if ($userId) {
                    $this->logProductInterest($userId, $products->pluck('id')->toArray(), 2.0);
                }
                return ['response' => $response, 'products' => $products->pluck('id')->toArray()];
            }
        }

        // 3. User nói "game đó"/"thể loại đó" nhưng không trích được → gợi ý game hot
        $products = ProductSimple::orderByDesc('view_count')->limit(5)->get();
        if ($products->isNotEmpty()) {
            $response = $this->formatProductsResponse($products, "Gợi ý game hay tại GameTech:");
            if ($userId) {
                $this->logProductInterest($userId, $products->pluck('id')->toArray(), 1.0);
            }
            return ['response' => $response, 'products' => $products->pluck('id')->toArray()];
        }

        return null;
    }

    /**
     * Fallback chuyên biệt cho các câu hỏi về game đua xe.
     * Phát hiện nhiều dạng câu hỏi: tâm trạng, gợi ý, thông tin, so sánh, tương tự.
     * Gọi Gemini với context chuyên sâu về racing games.
     */
    protected function handleRacingGameFallback(string $message, string $lowerMessage, ?int $userId, ?string $conversationId): ?array
    {
        $isRacingQuery = false;
        foreach ($this->racingKeywords as $keyword) {
            if (str_contains($lowerMessage, $keyword)) {
                $isRacingQuery = true;
                break;
            }
        }

        if (!$isRacingQuery) {
            return null;
        }

        $racingGames = ProductSimple::where(function ($q) {
            $q->where('category', 'LIKE', '%racing%')
              ->orWhere('category', 'LIKE', '%đua xe%')
              ->orWhere('category', 'LIKE', '%race%')
              ->orWhere('type', 'LIKE', '%racing%')
              ->orWhere('type', 'LIKE', '%race%')
              ->orWhere('title', 'LIKE', '%racing%')
              ->orWhere('title', 'LIKE', '%speed%')
              ->orWhere('title', 'LIKE', '%forza%')
              ->orWhere('title', 'LIKE', '%drift%')
              ->orWhere('title', 'LIKE', '%rally%')
              ->orWhere('title', 'LIKE', '%race%')
              ->orWhere('title', 'LIKE', '%gran turismo%')
              ->orWhere('title', 'LIKE', '%f1 %')
              ->orWhere('title', 'LIKE', '%need for speed%')
              ->orWhere('title', 'LIKE', '%nfs%')
              ->orWhere('title', 'LIKE', '%burnout%')
              ->orWhere('title', 'LIKE', '%asphalt%')
              ->orWhere('title', 'LIKE', '%mario kart%')
              ->orWhere('title', 'LIKE', '%hot wheels%')
              ->orWhere('title', 'LIKE', '%crew%')
              ->orWhere('short_description', 'LIKE', '%đua xe%')
              ->orWhere('short_description', 'LIKE', '%racing%')
              ->orWhere('short_description', 'LIKE', '%race%');
        })->orderByDesc('view_count')->limit(10)->get();

        $questionType = $this->detectRacingQuestionType($lowerMessage);
        $racingContext = $this->buildRacingGameContext($racingGames, $questionType);
        $conversationHistory = $this->getConversationHistory($conversationId);

        $geminiResponse = $this->callGeminiAI($message, $userId, $conversationHistory, $racingContext);
        $geminiResponse = $this->addLinksToResponse($geminiResponse);

        $productIds = $racingGames->pluck('id')->toArray();

        if ($userId && !empty($productIds)) {
            $this->logProductInterest($userId, array_slice($productIds, 0, 5), 1.5);
        }

        return [
            'response' => $geminiResponse,
            'products' => $productIds,
        ];
    }

    /**
     * Phát hiện loại câu hỏi về game đua xe để cung cấp context phù hợp cho Gemini.
     */
    protected function detectRacingQuestionType(string $lowerMessage): string
    {
        if (preg_match('/(?:hôm nay|tâm trạng|cảm thấy|đang|tôi)\s*(?:buồn|vui|chán|stress|mệt|hào hứng|phấn khích|thư giãn|giải trí|lo lắng|cô đơn|bực|tức|sợ|nhớ)/iu', $lowerMessage)) {
            return 'mood';
        }

        if (preg_match('/so\s*sánh|khác\s*nhau|giống\s*nhau|vs\s+|versus|đối\s*đầu|hay\s*hơn/iu', $lowerMessage)) {
            return 'comparison';
        }

        if (preg_match('/giống|tương\s*tự|similar|na\s*ná|gần\s*giống|kiểu\s*như|giống\s*game/iu', $lowerMessage)) {
            return 'similar';
        }

        if (preg_match('/(?:như\s*nào|thế\s*nào|ra\s*sao|thông\s*tin|tìm\s*hiểu|chi\s*tiết|review|đánh\s*giá|gameplay|cấu\s*hình|có\s*gì\s*hay|hay\s*ở\s*chỗ)/iu', $lowerMessage)) {
            return 'info';
        }

        return 'suggestion';
    }

    /**
     * Xây dựng context chuyên sâu về game đua xe gửi cho Gemini.
     * Bao gồm: kiến thức thể loại, game trong DB, hướng dẫn theo loại câu hỏi.
     */
    protected function buildRacingGameContext($racingGames, string $questionType = 'suggestion'): string
    {
        $baseUrl = config('app.url', 'http://127.0.0.1:8000');

        $context = "[CHUYÊN GIA GAME ĐUA XE - Trả lời chuyên sâu, tự nhiên về game đua xe]\n\n";

        $context .= match ($questionType) {
            'mood' => "USER ĐANG CHIA SẺ TÂM TRẠNG và muốn game đua xe. Hãy:\n1. Đồng cảm với cảm xúc của họ trước\n2. Gợi ý 2-3 game đua xe PHÙ HỢP tâm trạng (buồn/stress → game arcade nhẹ nhàng thư giãn; vui/hào hứng → game tốc độ kịch tính; chán → game đua xe thế giới mở khám phá)\n3. Giải thích ngắn tại sao game đó hợp tâm trạng\n\n",
            'comparison' => "USER MUỐN SO SÁNH game đua xe. Hãy so sánh chi tiết:\n- Gameplay & độ khó\n- Đồ họa & hiệu ứng\n- Chế độ chơi (online/offline/co-op)\n- Điểm mạnh / điểm yếu từng game\n- Phù hợp với ai\n- Giá cả nếu có trong cửa hàng\nDùng bullet points để dễ đọc.\n\n",
            'similar' => "USER MUỐN TÌM GAME TƯƠNG TỰ. Hãy:\n1. Phân tích đặc điểm game được nhắc (gameplay, phong cách, thể loại con)\n2. Gợi ý 3-5 game đua xe có đặc điểm tương tự\n3. Giải thích ngắn điểm giống nhau\n\n",
            'info' => "USER MUỐN TÌM HIỂU CHI TIẾT game đua xe. Hãy cung cấp:\n- Giới thiệu tổng quan\n- Gameplay & cơ chế chơi\n- Đồ họa & âm thanh\n- Chế độ chơi\n- Ưu điểm & nhược điểm\n- Đánh giá tổng thể\n- Phù hợp với ai\n\n",
            default => "USER MUỐN GỢI Ý GAME ĐUA XE. Hãy gợi ý 3-5 game phù hợp, mỗi game kèm mô tả ngắn 1-2 dòng về điểm nổi bật.\n\n",
        };

        $context .= "KIẾN THỨC GAME ĐUA XE (dùng để trả lời chuyên sâu):\n";
        $context .= "• Arcade Racing: Dễ chơi, vui nhộn, power-ups (Need for Speed, Burnout, Mario Kart, Asphalt)\n";
        $context .= "• Simulation Racing: Vật lý chân thực, đòi hỏi kỹ năng (Gran Turismo, Forza Motorsport, Assetto Corsa, iRacing)\n";
        $context .= "• Open World Racing: Thế giới mở tự do khám phá (Forza Horizon, The Crew, Need for Speed Heat/Unbound)\n";
        $context .= "• Kart Racing: Hoạt hình, dễ thương, party game (Mario Kart, Crash Team Racing, Hot Wheels Unleashed)\n";
        $context .= "• Rally/Off-road: Đường đất, địa hình khó (Dirt Rally, WRC, MudRunner)\n";
        $context .= "• Street Racing: Đường phố, drift, tuning xe (Need for Speed Underground/Most Wanted, Initial D)\n";
        $context .= "• F1/Motorsport: Giải đua chuyên nghiệp (F1 series, MotoGP, NASCAR Heat)\n\n";

        if ($racingGames->isNotEmpty()) {
            $context .= "GAME ĐUA XE CÓ SẴN TẠI CỬA HÀNG (ƯU TIÊN gợi ý những game này kèm link):\n";
            foreach ($racingGames as $game) {
                $price = $this->formatPrice($game->price ?? 0);
                $link = "{$baseUrl}/game/{$game->id}";
                $desc = $game->short_description ?? '';
                $context .= "- [{$game->title}]({$link}) | Giá: {$price}";
                if ($game->category) {
                    $context .= " | Thể loại: {$game->category}";
                }
                if ($desc) {
                    $context .= " | Mô tả: {$desc}";
                }
                $context .= "\n";
            }
            $context .= "\n";
        } else {
            $context .= "HIỆN TẠI CỬA HÀNG CHƯA CÓ GAME ĐUA XE. Hãy gợi ý game đua xe nổi tiếng dựa trên kiến thức chung và mời user ghé [**Cửa hàng**]({$baseUrl}/store) để xem game mới nhất.\n\n";
        }

        $context .= "QUY TẮC:\n";
        $context .= "- Trả lời tiếng Việt, thân thiện, có emoji phù hợp\n";
        $context .= "- Ưu tiên game có trong cửa hàng, kèm link markdown [**Tên game**](link)\n";
        $context .= "- Nếu game không có trong cửa hàng, vẫn gợi ý nhưng ghi chú rõ\n";
        $context .= "- Trả lời tự nhiên như chuyên gia game, không liệt kê cứng nhắc\n";
        $context .= "- Link cửa hàng: [**Cửa hàng**]({$baseUrl}/store)\n";

        return $context;
    }

    /**
     * Dữ liệu game dạng text để Gemini dùng làm nguồn tham khảo (không copy nguyên template).
     */
    protected function buildProductContextText($products): string
    {
        if ($products->isEmpty()) {
            return '';
        }
        $baseUrl = config('app.url', 'http://127.0.0.1:8000');
        $lines = [];
        foreach ($products as $p) {
            $price = $this->formatPrice($p->price ?? 0);
            $link = "{$baseUrl}/game/{$p->id}";
            $desc = $p->short_description ?? '';
            $lines[] = "- {$p->title} | Giá: {$price} | Thể loại: " . ($p->category ?? '') . " | Link: {$link}" . ($desc ? " | Mô tả ngắn: {$desc}" : '');
        }
        return implode("\n", $lines);
    }

    /**
     * Call Gemini AI API.
     * Khi $productContextText có giá trị: dữ liệu game chỉ là nguồn tham khảo, Gemini trả lời tự nhiên (như Gemini/ChatGPT), không copy template.
     */
    protected function callGeminiAI(string $message, ?int $userId, array $conversationHistory = [], ?string $productContextText = null): string
    {
        $apiKey = env('GEMINI_API_KEY');
        
        if (empty($apiKey)) {
            Log::warning('Gemini API key not configured');
            return $this->getSmartFallbackResponse($message);
        }
        
        try {
            // Build context about the website
            $systemContext = $this->buildSystemContext($userId);
            
            // Build multi-turn conversation format for Gemini
            $contents = [];
            
            // First message: system context
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $systemContext . "\n\nHãy nhớ vai trò của bạn và trả lời các câu hỏi tiếp theo."]]
            ];
            $contents[] = [
                'role' => 'model', 
                'parts' => [['text' => "Tôi đã hiểu! Tôi là trợ lý AI của GameTech Game Store. Tôi sẵn sàng giúp bạn tìm game, tra cứu thông tin, và trả lời mọi câu hỏi. Hãy hỏi tôi bất cứ điều gì!"]]
            ];
            
            // Add conversation history for context
            foreach ($conversationHistory as $msg) {
                $contents[] = [
                    'role' => $msg['role'],
                    'parts' => [['text' => $msg['content']]]
                ];
            }
            
            // Khi có dữ liệu game: inject làm nguồn tham khảo, bắt Gemini trả lời tự nhiên
            if ($productContextText !== null && $productContextText !== '') {
                $contents[] = [
                    'role' => 'user',
                    'parts' => [['text' => "[Dữ liệu game từ cửa hàng - chỉ dùng làm nguồn tham khảo]\n\n" . $productContextText . "\n\n---\nHãy dùng thông tin trên để trả lời câu hỏi tiếp theo của user một cách **tự nhiên** như Gemini/ChatGPT: kết hợp đề xuất, đánh giá nhẹ nếu phù hợp, nhắc link khi cần. **Không** copy nguyên template, **không** liệt kê cứng nhắc kiểu '1. X 2. Y'. Trả lời ngắn gọn, thân thiện, có thể dùng emoji."]]
                ];
                $contents[] = [
                    'role' => 'model',
                    'parts' => [['text' => "Tôi sẽ dùng dữ liệu game trên để trả lời tự nhiên, không copy template."]]
                ];
            }
            
            // Add current message
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $message]]
            ];
            
            // Sử dụng model gemini-2.5-flash (Feb 2026)
            $model = env('GEMINI_MODEL', 'gemini-2.5-flash');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
            
            Log::debug('Calling Gemini API', ['model' => $model, 'message' => $message, 'history_count' => count($conversationHistory)]);
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($url, [
                'contents' => $contents
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                
                if ($text) {
                    Log::debug('Gemini API success', ['response_length' => strlen($text)]);
                    return $text;
                }
                
                // Check if blocked by safety
                if (isset($data['candidates'][0]['finishReason']) && $data['candidates'][0]['finishReason'] === 'SAFETY') {
                    Log::warning('Gemini response blocked by safety filter');
                    return "Tôi hiểu bạn đang cảm thấy như vậy. 😊 Nếu bạn cần tâm sự hay muốn tìm game giải trí, tôi luôn sẵn sàng giúp đỡ!\n\nBạn có muốn tôi gợi ý một số game thư giãn không?";
                }
            }
            
            // Handle errors
            $status = $response->status();
            $errorBody = $response->body();
            
            Log::warning('Gemini API failed', [
                'status' => $status,
                'body' => substr($errorBody, 0, 500)
            ]);
            
            // Rate limit exceeded
            if ($status === 429) {
                Log::warning('Gemini API rate limit exceeded');
            }
            
            // Model not found - try fallback model
            if ($status === 404) {
                Log::error('Gemini model not found, trying fallback');
                return $this->callGeminiFallbackModel($message);
            }
            
            return $this->getSmartFallbackResponse($message);
            
        } catch (\Exception $e) {
            Log::error('Gemini API error', ['error' => $e->getMessage()]);
            return $this->getSmartFallbackResponse($message);
        }
    }

    /**
     * Try fallback Gemini models when primary model fails
     */
    protected function callGeminiFallbackModel(string $prompt): string
    {
        $apiKey = env('GEMINI_API_KEY');
        
        // List of fallback models to try (updated Feb 2026)
        $fallbackModels = [
            'gemini-2.5-flash',
            'gemini-2.0-flash',
            'gemini-2.0-flash-lite',
        ];
        
        foreach ($fallbackModels as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                
                Log::debug('Trying fallback Gemini model', ['model' => $model]);
                
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    
                    if ($text) {
                        Log::info('Fallback model success', ['model' => $model]);
                        return $text;
                    }
                }
                
                // Skip rate limited models
                if ($response->status() === 429) {
                    Log::debug('Fallback model rate limited', ['model' => $model]);
                    continue;
                }
                
            } catch (\Exception $e) {
                Log::debug('Fallback model error', ['model' => $model, 'error' => $e->getMessage()]);
                continue;
            }
        }
        
        // All models failed
        return $this->getSmartFallbackResponse($prompt);
    }

    /**
     * Build system context for Gemini
     */
    protected function buildSystemContext(?int $userId): string
    {
        $baseUrl = config('app.url', 'http://127.0.0.1:8000');
        
        $context = "Bạn là trợ lý AI thân thiện của GameTech - cửa hàng bán game Steam online. Tên bạn là GameTech AI.

THÔNG TIN WEBSITE:
- Tên: GameTech Game Store
- URL: {$baseUrl}
- Dịch vụ: Bán tài khoản Steam có sẵn game bản quyền
- Thanh toán: VNPay, MoMo, ZaloPay, Banking
- Bảo hành: Đổi tài khoản nếu có lỗi trong 24h

ĐƯỜNG DẪN QUAN TRỌNG (dùng để tạo link):
- Cửa hàng: {$baseUrl}/store
- Cửa hàng theo thể loại: {$baseUrl}/store?category=TEN_CATEGORY
- Chi tiết game: {$baseUrl}/game/ID_GAME
- Đơn hàng: {$baseUrl}/orders
- Ví tiền: {$baseUrl}/wallet
- Hỗ trợ: {$baseUrl}/support
- Cộng đồng: {$baseUrl}/community
- Tin tức: {$baseUrl}/news

PHONG CÁCH TRẢ LỜI (tự nhiên như ChatGPT/Gemini, không theo kịch bản cố định):
- Trò chuyện tự nhiên, đồng cảm, dùng emoji phù hợp. Trả lời bằng tiếng Việt.
- Khi user buồn/không vui/stress: trước hết an ủi, thấu hiểu; sau đó nếu phù hợp mới gợi ý 1–2 thể loại hoặc game cụ thể (ví dụ game phiêu lưu nhẹ, giải đố, mô phỏng thư giãn) — không liệt kê dài, chọn gợi ý hợp tâm trạng.
- Khi user vui/chán: có thể gợi ý game theo sở thích hoặc thể loại họ nhắc đến.
- Có thể trò chuyện nhiều chủ đề, không chỉ game. Dùng markdown: **bold**, *italic*, bullet points.
- QUAN TRỌNG: Khi đề cập game, trang web, chức năng — dùng link markdown [Tên](URL). Ví dụ: [**Cửa hàng**]({$baseUrl}/store), [**Cộng đồng**]({$baseUrl}/community).
- Luôn tích cực, hữu ích, ngắn gọn nhưng đủ ý.
- Khi gợi ý game: hãy nêu CỤ THỂ tên game từ danh sách dưới và dùng link [**Tên game**]({$baseUrl}/game/ID). Không chỉ nói thể loại chung chung.
- Khi được cung cấp **dữ liệu game từ cửa hàng** (danh sách game kèm giá, link): trả lời **tự nhiên như Gemini/ChatGPT** — kết hợp thông tin để đề xuất, đánh giá nhẹ nếu phù hợp, nhắc link khi cần. **Không** copy nguyên template, **không** liệt kê cứng nhắc kiểu \"1. X 2. Y\". Trả lời ngắn gọn, thân thiện.
- Khi user hỏi \"tìm hiểu thêm\", \"game đó\", \"thể loại đó\": hệ thống sẽ cung cấp dữ liệu game; bạn trả lời tự nhiên dựa trên đó.

VÍ DỤ TRẢ LỜI CÓ LINK:
- 'Bạn có thể vào [**Cửa hàng**]({$baseUrl}/store) để xem tất cả game'
- 'Gợi ý game phiêu lưu: [**Tên game 1**]({$baseUrl}/game/ID1), [**Tên game 2**]({$baseUrl}/game/ID2)'

";
        
        // Game phổ biến (top 10)
        $popularProducts = ProductSimple::orderByDesc('view_count')->limit(10)->get(['id', 'title', 'category', 'price']);
        if ($popularProducts->isNotEmpty()) {
            $context .= "GAME PHỔ BIẾN (gợi ý cụ thể, dùng link):\n";
            foreach ($popularProducts as $p) {
                $context .= "- [{$p->title}]({$baseUrl}/game/{$p->id}) - {$p->category} - " . $this->formatPrice($p->price) . "\n";
            }
        }

        // Thêm game theo từng thể loại (3 game/loại) để gợi ý theo genre
        $categories = ProductSimple::select('category')->distinct()->pluck('category')->filter()->toArray();
        if (!empty($categories)) {
            $context .= "\nGAME THEO THỂ LOẠI (gợi ý cụ thể khi user nhắc thể loại):\n";
            foreach ($categories as $cat) {
                if (empty($cat)) {
                    continue;
                }
                $byCat = ProductSimple::where('category', $cat)->orderByDesc('view_count')->limit(4)->get(['id', 'title', 'price']);
                if ($byCat->isNotEmpty()) {
                    $context .= "- {$cat}: ";
                    $context .= $byCat->map(fn ($p) => "[{$p->title}]({$baseUrl}/game/{$p->id})")->join(', ');
                    $context .= "\n";
                }
            }
        }
        
        return $context;
    }

    /**
     * Fallback response when Gemini fails - không dùng từ khóa, chỉ thông báo chung.
     */
    protected function getFallbackResponse(string $message): string
    {
        $baseUrl = config('app.url', 'http://127.0.0.1:8000');
        return "Xin lỗi, tôi đang gặp chút sự cố. 🤔 Bạn thử gửi lại sau giây lát nhé!\n\n" .
               "💡 **Trong lúc chờ:**\n" .
               "• 🎮 [**Cửa hàng**]({$baseUrl}/store) - Tìm game theo thể loại\n" .
               "• 📦 [**Đơn hàng**]({$baseUrl}/orders) - Xem đơn hàng của bạn\n" .
               "• 💬 [**Hỗ trợ**]({$baseUrl}/support) - Liên hệ hỗ trợ\n\n" .
               "Tôi luôn ở đây khi bạn cần! 😊";
    }

    /**
     * Smart fallback khi Gemini không gọi được - không phản hồi theo từ khóa, chỉ thông báo chung.
     */
    protected function getSmartFallbackResponse(string $message): string
    {
        $baseUrl = config('app.url', 'http://127.0.0.1:8000');
        return "Xin lỗi, tôi đang gặp chút sự cố. 🤔 Bạn thử gửi lại sau giây lát nhé!\n\n" .
               "💡 **Trong lúc chờ:**\n" .
               "• 🎮 [**Cửa hàng**]({$baseUrl}/store) - Tìm game theo thể loại\n" .
               "• 📦 [**Đơn hàng**]({$baseUrl}/orders) - Xem đơn hàng của bạn\n" .
               "• 💬 [**Hỗ trợ**]({$baseUrl}/support) - Liên hệ hỗ trợ\n\n" .
               "Tôi luôn ở đây khi bạn cần! 😊";
    }

    /**
     * Format products response with links
     */
    protected function formatProductsResponse($products, string $title): string
    {
        $baseUrl = config('app.url', 'http://127.0.0.1:8000');
        $response = "🎮 **{$title}**\n\n";
        
        foreach ($products as $index => $product) {
            $num = $index + 1;
            $price = $this->formatPrice($product->price);
            $gameUrl = "{$baseUrl}/game/{$product->id}";
            $response .= "**{$num}. [{$product->title}]({$gameUrl})**\n";
            $response .= "💰 {$price}";
            if ($product->category) {
                $categoryUrl = "{$baseUrl}/store?category=" . urlencode($product->category);
                $response .= " • [{$product->category}]({$categoryUrl})";
            }
            $response .= "\n\n";
        }
        
        $storeUrl = "{$baseUrl}/store";
        $response .= "👉 _Vào **[Cửa hàng]({$storeUrl})** để xem thêm!_";
        return $response;
    }

    /**
     * Get category display name
     */
    protected function getCategoryDisplayName(string $category): string
    {
        $names = [
            'action' => 'Hành động',
            'adventure' => 'Phiêu lưu',
            'rpg' => 'Nhập vai (RPG)',
            'sports' => 'Thể thao',
            'racing' => 'Đua xe',
            'strategy' => 'Chiến thuật',
            'simulation' => 'Mô phỏng',
            'puzzle' => 'Giải đố',
            'horror' => 'Kinh dị',
            'fighting' => 'Đối kháng',
            'mmo' => 'Online nhiều người',
        ];
        return $names[$category] ?? ucfirst($category);
    }

    /**
     * Format price value (handles both numeric and string formats)
     */
    protected function formatPrice($price): string
    {
        if (is_string($price)) {
            // Extract numeric value from string like "660.000 ₫"
            preg_match('/[\d,.]+/', $price, $matches);
            $price = $matches[0] ?? 0;
            $price = (float) str_replace(['.', ','], ['', '.'], $price);
        }
        return number_format((float) $price, 0, ',', '.') . 'đ';
    }

    /**
     * Process dynamic template
     */
    protected function processDynamicTemplate(string $template, string $message, ?int $userId): string
    {
        // Replace {products} placeholder with actual products
        if (str_contains($template, '{products}')) {
            $products = ProductSimple::orderByDesc('view_count')->limit(5)->get();
            $productList = $this->formatProductList($products);
            $template = str_replace('{products}', $productList, $template);
        }
        
        // Replace {top_games} with top viewed games
        if (str_contains($template, '{top_games}')) {
            $products = ProductSimple::orderByDesc('view_count')->limit(5)->get();
            $productList = $this->formatProductList($products);
            $template = str_replace('{top_games}', $productList, $template);
        }
        
        // Replace {new_games} with newest games
        if (str_contains($template, '{new_games}')) {
            $products = ProductSimple::orderByDesc('id')->limit(5)->get();
            $productList = $this->formatProductList($products);
            $template = str_replace('{new_games}', $productList, $template);
        }
        
        // Replace {cheap_games} with cheapest games
        if (str_contains($template, '{cheap_games}')) {
            $products = ProductSimple::orderBy('price')->limit(5)->get();
            $productList = $this->formatProductList($products);
            $template = str_replace('{cheap_games}', $productList, $template);
        }
        
        // Replace {store_stats} with store statistics
        if (str_contains($template, '{store_stats}')) {
            $totalGames = ProductSimple::count();
            $categories = ProductSimple::select('category')->distinct()->count();
            $avgPrice = $this->formatPrice(ProductSimple::avg('price') ?? 0);
            $minPrice = $this->formatPrice(ProductSimple::min('price') ?? 0);
            $maxPrice = $this->formatPrice(ProductSimple::max('price') ?? 0);
            
            $stats = "🎮 **Tổng game:** {$totalGames}\n";
            $stats .= "📁 **Thể loại:** {$categories}\n";
            $stats .= "💰 **Giá:** {$minPrice} - {$maxPrice}\n";
            $stats .= "📊 **Giá TB:** {$avgPrice}";
            
            $template = str_replace('{store_stats}', $stats, $template);
        }
        
        return $template;
    }
    
    /**
     * Format product list for templates with links
     */
    protected function formatProductList($products): string
    {
        $baseUrl = config('app.url', 'http://127.0.0.1:8000');
        $list = '';
        foreach ($products as $i => $p) {
            $num = $i + 1;
            $price = $this->formatPrice($p->price);
            $gameUrl = "{$baseUrl}/game/{$p->id}";
            $list .= "**{$num}. [{$p->title}]({$gameUrl})**\n💰 {$price}";
            if (!empty($p->category)) {
                $categoryUrl = "{$baseUrl}/store?category=" . urlencode($p->category);
                $list .= " • [{$p->category}]({$categoryUrl})";
            }
            $list .= "\n\n";
        }
        
        $storeUrl = "{$baseUrl}/store";
        $list .= "👉 _Vào [**Cửa hàng**]({$storeUrl}) để xem thêm!_";
        return $list;
    }

    /**
     * Log product interest for recommendation system
     */
    protected function logProductInterest(int $userId, array $productIds, float $value): void
    {
        foreach ($productIds as $productId) {
            try {
                DB::table('user_product_interactions')->insert([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'interaction_type' => 'view',
                    'interaction_value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Ignore duplicate or constraint errors
                Log::debug('Failed to log product interest', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Save conversation and return response
     */
    protected function saveAndReturn(
        string $question,
        string $answer,
        string $source,
        ?int $userId,
        ?string $sessionId,
        ?string $conversationId,
        float $startTime,
        ?int $learnedRuleId = null,
        array $extractedProducts = []
    ): array {
        $responseTime = (int) ((microtime(true) - $startTime) * 1000);
        
        // Generate conversation title from first question (truncate to 50 chars)
        $conversationTitle = mb_strlen($question) > 50 
            ? mb_substr($question, 0, 50) . '...' 
            : $question;
        
        try {
            $recordId = DB::table('chatbot_conversations')->insertGetId([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'conversation_id' => $conversationId,
                'conversation_title' => $conversationTitle,
                'question' => $question,
                'answer' => $answer,
                'source' => $source,
                'learned_rule_id' => $learnedRuleId,
                'extracted_products' => !empty($extractedProducts) ? json_encode($extractedProducts) : null,
                'response_time_ms' => $responseTime,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save chatbot conversation', ['error' => $e->getMessage()]);
            $recordId = null;
        }
        
        return [
            'answer' => $answer,
            'source' => $source,
            'conversation_id' => $conversationId,
            'record_id' => $recordId,
            'response_time_ms' => $responseTime,
        ];
    }

    /**
     * Process feedback for learning
     */
    public function processFeedback(int $conversationId, string $feedback): bool
    {
        try {
            // Update conversation feedback
            DB::table('chatbot_conversations')
                ->where('id', $conversationId)
                ->update(['feedback' => $feedback, 'updated_at' => now()]);
            
            // Get conversation
            $conversation = DB::table('chatbot_conversations')->find($conversationId);
            
            if (!$conversation) {
                return false;
            }
            
            // If from learned rule, update its feedback count
            if ($conversation->learned_rule_id) {
                $column = $feedback === 'good' ? 'positive_feedback' : 'negative_feedback';
                DB::table('chatbot_learned_rules')
                    ->where('id', $conversation->learned_rule_id)
                    ->increment($column);
                
                // Recalculate confidence score
                $this->recalculateConfidence($conversation->learned_rule_id);
            }
            
            // If good feedback from Gemini, consider creating learned rule
            if ($feedback === 'good' && $conversation->source === 'gemini') {
                $this->considerCreatingLearnedRule($conversation);
            }
            
            // Clear cache
            Cache::forget('chatbot_learned_rules');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to process feedback', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Recalculate confidence score for a learned rule
     */
    protected function recalculateConfidence(int $ruleId): void
    {
        $rule = DB::table('chatbot_learned_rules')->find($ruleId);
        if (!$rule) return;
        
        $total = $rule->positive_feedback + $rule->negative_feedback;
        if ($total === 0) return;
        
        $newScore = $rule->positive_feedback / $total;
        
        // Deactivate if too low
        $isActive = $newScore >= 0.3;
        
        DB::table('chatbot_learned_rules')
            ->where('id', $ruleId)
            ->update([
                'confidence_score' => $newScore,
                'is_active' => $isActive,
                'updated_at' => now(),
            ]);
    }

    /**
     * Consider creating a learned rule from successful Gemini response
     */
    protected function considerCreatingLearnedRule($conversation): void
    {
        // Find similar questions with good feedback
        $similarCount = DB::table('chatbot_conversations')
            ->where('source', 'gemini')
            ->where('feedback', 'good')
            ->where('question', 'LIKE', '%' . substr($conversation->question, 0, 20) . '%')
            ->count();
        
        // If 3+ similar successful questions, create a learned rule
        if ($similarCount >= 3) {
            // This is a simplified version - in production, you'd use NLP for pattern extraction
            $pattern = '/' . preg_quote(mb_strtolower($conversation->question), '/') . '/iu';
            
            // Check if rule already exists
            $exists = DB::table('chatbot_learned_rules')
                ->where('pattern', $pattern)
                ->exists();
            
            if (!$exists) {
                DB::table('chatbot_learned_rules')->insert([
                    'pattern' => $pattern,
                    'keywords' => json_encode(explode(' ', $conversation->question)),
                    'intent' => 'learned_from_gemini',
                    'response_template' => $conversation->answer,
                    'response_type' => 'static',
                    'confidence_score' => 0.60,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                Log::info('Created new learned rule from Gemini response', [
                    'question' => $conversation->question,
                ]);
            }
        }
    }

    /**
     * Get suggested questions
     */
    public function getSuggestions(): array
    {
        return [
            '🎮 Game hot nhất',
            '🏎️ Game đua xe',
            '⚔️ Game hành động',
            '💰 Cách thanh toán',
            '📦 Đơn hàng của tôi',
            '❓ Hỗ trợ',
        ];
    }
}
