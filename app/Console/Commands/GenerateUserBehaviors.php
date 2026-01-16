<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ProductSimple;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateUserBehaviors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'behaviors:generate 
                            {--users=50 : Số lượng users giả lập cần tạo}
                            {--interactions=500 : Tổng số interactions cần tạo}
                            {--clear : Xóa toàn bộ dữ liệu cũ trước khi tạo mới}
                            {--realistic : Tạo dữ liệu thực tế hơn với các patterns}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo dữ liệu hành vi người dùng giả lập để train AI Recommendation System';

    /**
     * Các loại interaction và trọng số
     */
    protected $interactionTypes = [
        'view' => ['value' => 1.0, 'weight' => 50],      // 50% là view
        'cart_add' => ['value' => 2.0, 'weight' => 20],  // 20% là thêm giỏ
        'wishlist' => ['value' => 1.5, 'weight' => 10],  // 10% là wishlist  
        'purchase' => ['value' => 5.0, 'weight' => 15],  // 15% là mua
        'review' => ['value' => 3.0, 'weight' => 5],     // 5% là review
    ];

    /**
     * Preference patterns - Mô phỏng sở thích người dùng thực
     */
    protected $userPatterns = [
        'action_lover' => ['Hành Động', 'Nhập Vai', 'Bắn Súng', 'FPS'],
        'rpg_fan' => ['RPG', 'Nhập Vai', 'Phiêu Lưu', 'JRPG'],
        'sports_gamer' => ['Thể Thao', 'Đua Xe', 'Mô Phỏng', 'Racing'],
        'adventure_seeker' => ['Phiêu Lưu', 'Thế Giới Mở', 'Sinh Tồn', 'Horror'],
        'casual_player' => ['Indie', 'Giải Đố', 'Mô Phỏng', 'Casual'],
        'strategy_mind' => ['Chiến Thuật', 'Chiến Lược', 'RTS', 'Turn-based'],
        'mixed' => [], // Random tất cả
    ];

    public function handle()
    {
        $this->info('');
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║  🎮 USER BEHAVIOR GENERATOR - AI Training Data            ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->info('');

        $numUsers = (int) $this->option('users');
        $numInteractions = (int) $this->option('interactions');
        $isRealistic = $this->option('realistic');

        // Xóa dữ liệu cũ nếu có flag --clear
        if ($this->option('clear')) {
            if ($this->confirm('⚠️ Bạn có chắc muốn xóa toàn bộ dữ liệu interactions cũ?')) {
                DB::table('user_product_interactions')->truncate();
                $this->info('🗑️  Đã xóa toàn bộ dữ liệu cũ.');
            }
        }

        // Lấy danh sách products
        $products = ProductSimple::all();
        if ($products->isEmpty()) {
            $this->error('❌ Không có sản phẩm nào trong database!');
            return Command::FAILURE;
        }

        // Lấy hoặc tạo users giả lập
        $users = $this->getOrCreateUsers($numUsers);
        
        $this->info("📊 Chuẩn bị tạo dữ liệu:");
        $this->table(
            ['Thông số', 'Giá trị'],
            [
                ['👥 Số users', count($users)],
                ['📦 Số products', $products->count()],
                ['🎯 Số interactions', $numInteractions],
                ['🧠 Mode', $isRealistic ? 'Realistic (có patterns)' : 'Random'],
            ]
        );
        $this->newLine();

        // Tạo interactions
        $this->info('⏳ Đang tạo interactions...');
        $bar = $this->output->createProgressBar($numInteractions);
        $bar->start();

        $createdCount = 0;
        $interactions = [];

        // Gán pattern cho mỗi user nếu realistic mode
        $userPatternMap = [];
        if ($isRealistic) {
            $patternKeys = array_keys($this->userPatterns);
            foreach ($users as $user) {
                $userPatternMap[$user->id] = $patternKeys[array_rand($patternKeys)];
            }
        }

        for ($i = 0; $i < $numInteractions; $i++) {
            $user = $users->random();
            $interactionType = $this->weightedRandom($this->interactionTypes);
            
            // Chọn product dựa trên pattern của user hoặc random
            if ($isRealistic && isset($userPatternMap[$user->id])) {
                $product = $this->selectProductByPattern(
                    $products, 
                    $this->userPatterns[$userPatternMap[$user->id]]
                );
            } else {
                $product = $products->random();
            }

            // Tạo timestamp ngẫu nhiên trong 30 ngày gần đây
            $createdAt = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            $interactions[] = [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'interaction_type' => $interactionType,
                'interaction_value' => $this->interactionTypes[$interactionType]['value'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            $createdCount++;
            $bar->advance();

            // Insert theo batch để tối ưu
            if (count($interactions) >= 100) {
                DB::table('user_product_interactions')->insert($interactions);
                $interactions = [];
            }
        }

        // Insert phần còn lại
        if (!empty($interactions)) {
            DB::table('user_product_interactions')->insert($interactions);
        }

        $bar->finish();
        $this->newLine();
        $this->newLine();

        // Thống kê kết quả
        $stats = DB::table('user_product_interactions')
            ->select('interaction_type', DB::raw('COUNT(*) as count'))
            ->groupBy('interaction_type')
            ->get();

        $this->info('✅ Hoàn thành! Thống kê interactions:');
        $this->table(
            ['Loại Interaction', 'Số lượng', 'Điểm giá trị'],
            $stats->map(function ($stat) {
                return [
                    $this->getInteractionEmoji($stat->interaction_type) . ' ' . ucfirst($stat->interaction_type),
                    $stat->count,
                    $this->interactionTypes[$stat->interaction_type]['value']
                ];
            })->toArray()
        );

        $totalInteractions = DB::table('user_product_interactions')->count();
        $uniqueUsers = DB::table('user_product_interactions')->distinct('user_id')->count('user_id');
        $uniqueProducts = DB::table('user_product_interactions')->distinct('product_id')->count('product_id');

        $this->newLine();
        $this->info("📈 Tổng quan:");
        $this->table(
            ['Metric', 'Value'],
            [
                ['🎯 Tổng interactions', number_format($totalInteractions)],
                ['👥 Users có tương tác', $uniqueUsers],
                ['📦 Products được tương tác', $uniqueProducts],
                ['📊 Avg interactions/user', round($totalInteractions / max($uniqueUsers, 1), 1)],
            ]
        );

        $this->newLine();
        $this->info('💡 Chạy "php artisan recommendation:train" để train AI model với dữ liệu mới!');
        $this->newLine();

        return Command::SUCCESS;
    }

    /**
     * Lấy hoặc tạo users giả lập
     */
    protected function getOrCreateUsers($count)
    {
        $existingUsers = User::all();
        
        if ($existingUsers->count() >= $count) {
            return $existingUsers->take($count);
        }

        // Tạo thêm users nếu cần
        $this->info("👥 Tạo thêm users giả lập...");
        
        $toCreate = $count - $existingUsers->count();
        $newUsers = [];

        for ($i = 0; $i < $toCreate; $i++) {
            $timestamp = now();
            $newUsers[] = [
                'name' => 'User_' . uniqid(),
                'email' => 'user_' . uniqid() . '@gametech.test',
                'password' => bcrypt('password123'),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        if (!empty($newUsers)) {
            DB::table('users')->insert($newUsers);
        }

        return User::take($count)->get();
    }

    /**
     * Chọn ngẫu nhiên có trọng số
     */
    protected function weightedRandom($items)
    {
        $totalWeight = array_sum(array_column($items, 'weight'));
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($items as $key => $item) {
            $currentWeight += $item['weight'];
            if ($random <= $currentWeight) {
                return $key;
            }
        }
        
        return array_key_first($items);
    }

    /**
     * Chọn product theo pattern (ưu tiên category phù hợp)
     */
    protected function selectProductByPattern($products, $preferredCategories)
    {
        if (empty($preferredCategories)) {
            return $products->random();
        }

        // 70% chọn theo preference, 30% random
        if (rand(1, 100) <= 70) {
            $matching = $products->filter(function ($product) use ($preferredCategories) {
                foreach ($preferredCategories as $cat) {
                    if (stripos($product->category, $cat) !== false) {
                        return true;
                    }
                }
                return false;
            });

            if ($matching->isNotEmpty()) {
                return $matching->random();
            }
        }

        return $products->random();
    }

    /**
     * Lấy emoji cho loại interaction
     */
    protected function getInteractionEmoji($type)
    {
        return match($type) {
            'view' => '👁️',
            'cart_add' => '🛒',
            'purchase' => '💰',
            'review' => '⭐',
            'wishlist' => '❤️',
            default => '📌'
        };
    }
}
