<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\ProductSimple;

class TrainChatbot extends Command
{
    protected $signature = 'chatbot:train {--force : Force retrain all rules}';
    protected $description = 'Train chatbot from conversation history - tự học từ các cuộc hội thoại';

    /**
     * Categories mapping for Vietnamese
     */
    protected array $categoryKeywords = [
        'action' => ['hành động', 'action', 'bắn súng', 'shooter', 'fps', 'chiến đấu'],
        'adventure' => ['phiêu lưu', 'adventure', 'khám phá', 'thám hiểm'],
        'rpg' => ['rpg', 'nhập vai', 'role playing', 'role-playing'],
        'sports' => ['thể thao', 'sports', 'bóng đá', 'football', 'fifa', 'pes', 'racing'],
        'racing' => ['đua xe', 'racing', 'race', 'car', 'speed', 'drift'],
        'strategy' => ['chiến thuật', 'strategy', 'rts', 'chiến lược'],
        'simulation' => ['mô phỏng', 'simulation', 'sim', 'farming'],
        'puzzle' => ['giải đố', 'puzzle', 'logic', 'brain'],
        'horror' => ['kinh dị', 'horror', 'scary', 'sợ', 'ma'],
        'fighting' => ['đối kháng', 'fighting', 'võ thuật', 'combat'],
        'mmo' => ['mmo', 'online', 'multiplayer', 'nhiều người', 'co-op'],
        'survival' => ['sinh tồn', 'survival', 'craft', 'build'],
        'open_world' => ['open world', 'thế giới mở', 'sandbox'],
    ];

    /**
     * Intent patterns for Vietnamese questions
     */
    protected array $intentPatterns = [
        'product_search' => [
            'patterns' => ['tìm game', 'có game', 'game nào', 'gợi ý game', 'muốn chơi', 'đề xuất'],
            'response_type' => 'dynamic',
        ],
        'price_query' => [
            'patterns' => ['giá', 'bao nhiêu', 'tiền', 'price', 'cost'],
            'response_type' => 'dynamic',
        ],
        'category_search' => [
            'patterns' => ['thể loại', 'loại game', 'game.*hay', 'hay nhất'],
            'response_type' => 'dynamic',
        ],
        'recommendation' => [
            'patterns' => ['gợi ý', 'đề xuất', 'nên chơi', 'recommend', 'tư vấn'],
            'response_type' => 'dynamic',
        ],
        'comparison' => [
            'patterns' => ['so sánh', 'khác nhau', 'vs', 'hay hơn', 'tốt hơn'],
            'response_type' => 'dynamic',
        ],
        'order_status' => [
            'patterns' => ['đơn hàng', 'order', 'mua rồi', 'đã mua'],
            'response_type' => 'dynamic',
        ],
        'payment_help' => [
            'patterns' => ['thanh toán', 'nạp tiền', 'payment', 'trả tiền', 'mua'],
            'response_type' => 'static',
        ],
        'support' => [
            'patterns' => ['hỗ trợ', 'support', 'giúp', 'liên hệ', 'contact'],
            'response_type' => 'static',
        ],
    ];

    public function handle()
    {
        $this->info('🤖 Bắt đầu train chatbot từ dữ liệu...');
        $this->newLine();

        $force = $this->option('force');
        
        if ($force) {
            $this->warn('⚠️ Force mode: Xóa tất cả learned rules cũ...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('chatbot_learned_rules')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // 1. Train from good feedback conversations
        $this->trainFromConversations();

        // 2. Train from product data
        $this->trainFromProducts();

        // 3. Train from common patterns
        $this->trainCommonPatterns();

        // 4. Clear cache
        Cache::forget('chatbot_learned_rules');

        $this->newLine();
        $totalRules = DB::table('chatbot_learned_rules')->where('is_active', true)->count();
        $this->info("✅ Training hoàn tất! Tổng số rules: {$totalRules}");

        return Command::SUCCESS;
    }

    /**
     * Train from conversation history with good feedback
     */
    protected function trainFromConversations(): void
    {
        $this->info('📚 Phân tích cuộc hội thoại...');

        // Get conversations with good feedback from Gemini responses
        $goodConversations = DB::table('chatbot_conversations')
            ->where('source', 'gemini')
            ->where('feedback', 'good')
            ->get();

        $this->line("   Tìm thấy {$goodConversations->count()} cuộc hội thoại tốt từ Gemini");

        // Group similar questions
        $questionGroups = [];
        foreach ($goodConversations as $conv) {
            $normalized = $this->normalizeQuestion($conv->question);
            $hash = md5($normalized);
            
            if (!isset($questionGroups[$hash])) {
                $questionGroups[$hash] = [
                    'questions' => [],
                    'answers' => [],
                    'original' => $conv->question,
                    'normalized' => $normalized,
                ];
            }
            $questionGroups[$hash]['questions'][] = $conv->question;
            $questionGroups[$hash]['answers'][] = $conv->answer;
        }

        $rulesCreated = 0;
        foreach ($questionGroups as $group) {
            // Only create rules for questions asked multiple times
            if (count($group['questions']) >= 2) {
                $this->createLearnedRule(
                    $group['normalized'],
                    $group['questions'],
                    $group['answers'][0], // Use first answer as template
                    'learned_from_feedback'
                );
                $rulesCreated++;
            }
        }

        $this->line("   → Tạo {$rulesCreated} rules từ feedback");

        // Also learn from all Gemini conversations (even without explicit feedback)
        $allGeminiConv = DB::table('chatbot_conversations')
            ->where('source', 'gemini')
            ->whereNull('feedback')
            ->get();

        $this->line("   Phân tích thêm {$allGeminiConv->count()} cuộc hội thoại Gemini...");

        $additionalGroups = [];
        foreach ($allGeminiConv as $conv) {
            $normalized = $this->normalizeQuestion($conv->question);
            $intent = $this->detectIntent($conv->question);
            $key = $intent . '_' . md5($normalized);
            
            if (!isset($additionalGroups[$key])) {
                $additionalGroups[$key] = [
                    'questions' => [],
                    'answers' => [],
                    'intent' => $intent,
                    'normalized' => $normalized,
                ];
            }
            $additionalGroups[$key]['questions'][] = $conv->question;
            $additionalGroups[$key]['answers'][] = $conv->answer;
        }

        $additionalRules = 0;
        foreach ($additionalGroups as $group) {
            if (count($group['questions']) >= 3) { // Need 3+ similar questions
                $this->createLearnedRule(
                    $group['normalized'],
                    $group['questions'],
                    $group['answers'][0],
                    'learned_from_frequency'
                );
                $additionalRules++;
            }
        }

        $this->line("   → Tạo thêm {$additionalRules} rules từ tần suất");
    }

    /**
     * Train from product data - create rules for each game and category
     */
    protected function trainFromProducts(): void
    {
        $this->info('🎮 Tạo rules từ dữ liệu sản phẩm...');

        $products = ProductSimple::all();
        $this->line("   Tìm thấy {$products->count()} sản phẩm");

        $productRules = 0;
        foreach ($products as $product) {
            // Create rule for each product name
            $titleLower = mb_strtolower($product->title, 'UTF-8');
            $titleWords = preg_split('/\s+/', $titleLower);
            
            // Only create for products with recognizable names (2+ words)
            if (count($titleWords) >= 2) {
                $pattern = $this->buildProductPattern($product->title);
                $existingRule = DB::table('chatbot_learned_rules')
                    ->where('pattern', $pattern)
                    ->exists();

                if (!$existingRule) {
                    $price = $this->formatPrice($product->price);
                    
                    $response = "🎮 **{$product->title}**\n\n";
                    $response .= "💰 **Giá:** {$price}\n";
                    if ($product->category) {
                        $response .= "📁 **Thể loại:** {$product->category}\n";
                    }
                    if ($product->short_description) {
                        $response .= "\n📝 {$product->short_description}\n";
                    }
                    $response .= "\n👉 _Vào **Cửa hàng** để mua game này!_";

                    DB::table('chatbot_learned_rules')->insert([
                        'pattern' => $pattern,
                        'keywords' => json_encode($titleWords),
                        'intent' => 'product_info',
                        'response_template' => $response,
                        'response_type' => 'static',
                        'confidence_score' => 0.80,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $productRules++;
                }
            }
        }

        $this->line("   → Tạo {$productRules} rules cho sản phẩm");

        // Create rules for categories
        $categories = ProductSimple::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        $categoryRules = 0;
        foreach ($categories as $category) {
            $categoryLower = mb_strtolower($category, 'UTF-8');
            $pattern = $this->buildCategoryPattern($category);
            
            $existingRule = DB::table('chatbot_learned_rules')
                ->where('pattern', $pattern)
                ->exists();

            if (!$existingRule) {
                $topProducts = ProductSimple::where('category', $category)
                    ->orderByDesc('view_count')
                    ->limit(5)
                    ->get();

                if ($topProducts->isNotEmpty()) {
                    $response = "🎮 **Game {$category} hay nhất:**\n\n";
                    foreach ($topProducts as $i => $p) {
                        $num = $i + 1;
                        $price = $this->formatPrice($p->price);
                        $response .= "**{$num}. {$p->title}**\n💰 {$price}\n\n";
                    }
                    $response .= "👉 _Vào **Cửa hàng** để xem thêm!_";

                    DB::table('chatbot_learned_rules')->insert([
                        'pattern' => $pattern,
                        'keywords' => json_encode([$categoryLower, 'game', 'hay']),
                        'intent' => 'category_search',
                        'response_template' => $response,
                        'response_type' => 'static',
                        'confidence_score' => 0.75,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $categoryRules++;
                }
            }
        }

        $this->line("   → Tạo {$categoryRules} rules cho thể loại");
    }

    /**
     * Train common question patterns
     */
    protected function trainCommonPatterns(): void
    {
        $this->info('📝 Tạo rules cho các mẫu câu hỏi phổ biến...');

        $commonPatterns = [
            [
                'pattern' => '/(?:game|trò chơi).*(?:hay|tốt|đỉnh|best)/iu',
                'keywords' => ['game', 'hay', 'tốt', 'best'],
                'intent' => 'best_games',
                'response' => "🏆 **Game được yêu thích nhất:**\n\n{top_games}\n\n👉 _Đây là những game có lượt xem cao nhất!_",
                'response_type' => 'dynamic',
            ],
            [
                'pattern' => '/(?:game|trò chơi).*(?:mới|new|latest|vừa ra)/iu',
                'keywords' => ['game', 'mới', 'new', 'latest'],
                'intent' => 'new_games',
                'response' => "🆕 **Game mới nhất:**\n\n{new_games}\n\n👉 _Cập nhật liên tục!_",
                'response_type' => 'dynamic',
            ],
            [
                'pattern' => '/(?:game|trò chơi).*(?:rẻ|giảm giá|sale|khuyến mãi|cheap)/iu',
                'keywords' => ['game', 'rẻ', 'giảm giá', 'sale'],
                'intent' => 'cheap_games',
                'response' => "💸 **Game giá tốt:**\n\n{cheap_games}\n\n👉 _Săn deal ngay!_",
                'response_type' => 'dynamic',
            ],
            [
                'pattern' => '/(?:có bao nhiêu|tổng cộng|số lượng).*game/iu',
                'keywords' => ['bao nhiêu', 'tổng', 'số lượng', 'game'],
                'intent' => 'game_count',
                'response' => "📊 **Thống kê cửa hàng:**\n\n{store_stats}\n\n👉 _Khám phá ngay!_",
                'response_type' => 'dynamic',
            ],
        ];

        $created = 0;
        foreach ($commonPatterns as $p) {
            $exists = DB::table('chatbot_learned_rules')
                ->where('pattern', $p['pattern'])
                ->exists();

            if (!$exists) {
                DB::table('chatbot_learned_rules')->insert([
                    'pattern' => $p['pattern'],
                    'keywords' => json_encode($p['keywords']),
                    'intent' => $p['intent'],
                    'response_template' => $p['response'],
                    'response_type' => $p['response_type'],
                    'confidence_score' => 0.70,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $created++;
            }
        }

        $this->line("   → Tạo {$created} rules cho mẫu phổ biến");
    }

    /**
     * Normalize question for grouping similar questions
     */
    protected function normalizeQuestion(string $question): string
    {
        $normalized = mb_strtolower(trim($question), 'UTF-8');
        
        // Remove punctuation
        $normalized = preg_replace('/[?!.,;:]+/', '', $normalized);
        
        // Remove common filler words
        $fillers = ['ơi', 'à', 'ạ', 'nhé', 'nha', 'vậy', 'thế', 'đi', 'cho', 'tôi', 'mình', 'em', 'anh', 'chị'];
        foreach ($fillers as $filler) {
            $normalized = preg_replace('/\b' . preg_quote($filler, '/') . '\b/u', '', $normalized);
        }
        
        // Normalize whitespace
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        
        return trim($normalized);
    }

    /**
     * Detect intent from question
     */
    protected function detectIntent(string $question): string
    {
        $questionLower = mb_strtolower($question, 'UTF-8');
        
        foreach ($this->intentPatterns as $intent => $config) {
            foreach ($config['patterns'] as $pattern) {
                if (preg_match('/' . $pattern . '/iu', $questionLower)) {
                    return $intent;
                }
            }
        }
        
        return 'general';
    }

    /**
     * Build regex pattern for product name
     */
    protected function buildProductPattern(string $title): string
    {
        $titleLower = mb_strtolower($title, 'UTF-8');
        $escaped = preg_quote($titleLower, '/');
        
        // Allow flexible matching
        return '/(?:game\s+)?(?:' . $escaped . '|' . str_replace(' ', '.*', $escaped) . ')/iu';
    }

    /**
     * Build regex pattern for category
     */
    protected function buildCategoryPattern(string $category): string
    {
        $categoryLower = mb_strtolower($category, 'UTF-8');
        $escaped = preg_quote($categoryLower, '/');
        
        // Get Vietnamese keywords for this category
        $keywords = [$escaped];
        foreach ($this->categoryKeywords as $cat => $catKeywords) {
            if (stripos($category, $cat) !== false || stripos($cat, $category) !== false) {
                $keywords = array_merge($keywords, $catKeywords);
                break;
            }
        }
        
        $keywordPattern = implode('|', array_unique($keywords));
        return '/(?:game|trò chơi).*(?:' . $keywordPattern . ')/iu';
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
     * Create a learned rule from grouped questions
     */
    protected function createLearnedRule(string $normalized, array $questions, string $answer, string $source): void
    {
        // Build pattern from common words
        $words = preg_split('/\s+/', $normalized);
        $words = array_filter($words, fn($w) => mb_strlen($w) >= 2);
        
        if (count($words) < 2) return;
        
        // Create flexible pattern
        $patternParts = array_map(fn($w) => preg_quote($w, '/'), array_slice($words, 0, 4));
        $pattern = '/(?:' . implode('|', $patternParts) . ').*(?:' . implode('|', $patternParts) . ')/iu';
        
        // Check if similar pattern exists
        $exists = DB::table('chatbot_learned_rules')
            ->where('pattern', $pattern)
            ->exists();
        
        if ($exists) return;
        
        $intent = $this->detectIntent($questions[0]);
        
        DB::table('chatbot_learned_rules')->insert([
            'pattern' => $pattern,
            'keywords' => json_encode($words),
            'intent' => $source . '_' . $intent,
            'response_template' => $answer,
            'response_type' => 'static',
            'confidence_score' => 0.65,
            'usage_count' => count($questions),
            'positive_feedback' => count($questions),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
