<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\ProductSimple;
use App\Models\SteamAccount;
use Carbon\Carbon;

class AITrainingDataSeeder extends Seeder
{
    /**
     * User preference patterns - mô phỏng sở thích thật
     */
    protected $userPatterns = [
        'action_gamer' => [
            'categories' => ['Hành Động', 'Phiêu Lưu'],
            'preferred_ratio' => 0.75, // 75% mua game yêu thích
            'rating_bias' => 1, // +1 rating cho game yêu thích
        ],
        'rpg_enthusiast' => [
            'categories' => ['RPG'],
            'preferred_ratio' => 0.80,
            'rating_bias' => 1,
        ],
        'sports_fan' => [
            'categories' => ['Thể Thao', 'Đua Xe'],
            'preferred_ratio' => 0.70,
            'rating_bias' => 1,
        ],
        'strategy_player' => [
            'categories' => ['Chiến Thuật', 'Giả Lập'],
            'preferred_ratio' => 0.75,
            'rating_bias' => 1,
        ],
        'indie_lover' => [
            'categories' => ['indie', 'Thông Thường'],
            'preferred_ratio' => 0.65,
            'rating_bias' => 1,
        ],
        'casual_gamer' => [
            'categories' => [], // Random
            'preferred_ratio' => 0,
            'rating_bias' => 0,
        ],
    ];

    protected $reviewComments = [
        1 => ['Game quá tệ, không đáng tiền!', 'Thất vọng hoàn toàn.', 'Không recommend.', 'Lãng phí tiền.'],
        2 => ['Game tạm, còn nhiều bug.', 'Không tốt như mong đợi.', 'Gameplay đơn điệu.', 'Giá hơi cao.'],
        3 => ['Game ổn cho giá này.', 'Chơi giải trí được.', 'Tạm được, có vài điểm hay.', 'Trung bình.'],
        4 => ['Game hay, đáng tiền!', 'Đồ họa đẹp, gameplay cuốn.', 'Recommend!', 'Sẽ mua game khác ở đây.'],
        5 => ['Tuyệt vời! 10/10!', 'Game đỉnh của đỉnh!', 'Quá xuất sắc!', 'Shop uy tín, game chất!'],
    ];

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔════════════════════════════════════════════════════════════╗');
        $this->command->info('║  🤖 AI TRAINING DATA GENERATOR (Complete Reset)           ║');
        $this->command->info('╚════════════════════════════════════════════════════════════╝');
        $this->command->info('');

        // Step 1: Xóa dữ liệu AI training (giữ nguyên products, steam_accounts gốc)
        $this->command->info('📋 Bước 1: Xóa dữ liệu training cũ...');
        $this->clearTrainingData();

        // Step 2: Reset steam_accounts về trạng thái ban đầu
        $this->command->info('📋 Bước 2: Reset steam_accounts về available...');
        $this->resetSteamAccounts();

        // Step 3: Lấy dữ liệu cần thiết
        $users = User::where('role', 'buyer')->get();
        $products = ProductSimple::all();
        $productsByCategory = $products->groupBy('category');

        if ($users->isEmpty()) {
            $this->command->error('❌ Không có user buyer! Chạy BuyerUsersSeeder trước.');
            return;
        }

        if ($products->isEmpty()) {
            $this->command->error('❌ Không có sản phẩm!');
            return;
        }

        // Assign patterns to users
        $patternKeys = array_keys($this->userPatterns);
        $usersPerPattern = ceil($users->count() / count($patternKeys));

        $this->command->info("📊 Phân bố {$users->count()} users theo {$usersPerPattern} users/pattern:");
        $this->displayPatternDistribution($patternKeys, $usersPerPattern, $users->count());

        // Step 4: Tạo dữ liệu
        $this->command->newLine();
        $this->command->info('📋 Bước 3: Tạo dữ liệu training mới...');

        $stats = [
            'deposits' => 0, 'orders' => 0, 'reviews' => 0, 
            'interactions' => 0, 'accounts_sold' => 0,
            'total_deposited' => 0, 'total_spent' => 0,
        ];

        $bar = $this->command->getOutput()->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $index => $user) {
            $patternIndex = min(floor($index / $usersPerPattern), count($patternKeys) - 1);
            $pattern = $this->userPatterns[$patternKeys[$patternIndex]];

            $this->processUser($user, $pattern, $products, $productsByCategory, $stats);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->newLine();

        // Display results
        $this->displayResults($stats);

        // Step 5: Train AI
        $this->command->newLine();
        $this->command->info('📋 Bước 4: Training AI Recommendation...');
        $this->call('recommendation:train', ['--force' => true]);

        // Step 6: Evaluate
        $this->command->newLine();
        $this->command->info('📋 Bước 5: Đánh giá kết quả...');
        $this->call('recommendation:evaluate', ['--k' => 10]);
    }

    /**
     * Xóa dữ liệu training cũ
     */
    protected function clearTrainingData(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Xóa dữ liệu AI training
        DB::table('user_recommendations')->truncate();
        DB::table('product_recommendations')->truncate();
        DB::table('user_product_interactions')->truncate();
        DB::table('reviews')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('transactions')->truncate();
        
        // Reset user stats nhưng giữ user
        DB::table('users')->where('role', 'buyer')->update([
            'balance' => 0,
            'total_orders' => 0,
            'total_spent' => 0,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('   ✅ Đã xóa: orders, reviews, transactions, interactions, recommendations');
    }

    /**
     * Reset steam_accounts về trạng thái ban đầu
     */
    protected function resetSteamAccounts(): void
    {
        // Reset tất cả steam_accounts về available với count mặc định
        DB::table('steam_accounts')->update([
            'status' => 'available',
            'sold_at' => null,
            'count' => 10, // Reset về count mặc định
        ]);
        
        $count = DB::table('steam_accounts')->count();
        $this->command->info("   ✅ Đã reset {$count} steam_accounts về available (count=10)");
    }

    /**
     * Xử lý từng user
     */
    protected function processUser($user, $pattern, $products, $productsByCategory, &$stats): void
    {
        // 1. NẠP TIỀN (2-4 lần)
        $numDeposits = rand(2, 4);
        $user->balance = 0;

        for ($i = 0; $i < $numDeposits; $i++) {
            $amount = rand(2, 15) * 100000; // 200k - 1.5M
            $depositDate = Carbon::now()->subDays(rand(60, 120))->subHours(rand(0, 23));
            
            $this->createDeposit($user, $amount, $depositDate);
            $user->balance += $amount;
            $stats['deposits']++;
            $stats['total_deposited'] += $amount;
        }

        $user->save();

        // 2. MUA HÀNG (8-20 orders tùy balance)
        $numOrders = rand(8, 20);
        $purchasedProductIds = [];

        for ($i = 0; $i < $numOrders && $user->balance > 30000; $i++) {
            // Chọn product theo pattern, tránh mua trùng
            $product = $this->selectProduct($products, $productsByCategory, $pattern, $purchasedProductIds);
            if (!$product) continue;

            // Parse giá
            $amount = $this->extractPrice($product->price);
            if ($amount <= 0) $amount = rand(50000, 300000);
            if ($user->balance < $amount) continue;

            // Tìm steam account còn hàng
            $steamAccount = $this->findAvailableSteamAccount($product->id);
            if (!$steamAccount) continue;

            // Tạo order
            $orderDate = Carbon::now()->subDays(rand(1, 50))->subHours(rand(0, 23));
            $orderId = $this->createOrder($user, $product, $steamAccount, $amount, $orderDate);

            if ($orderId) {
                // Trừ balance
                $this->createPurchaseTransaction($user, $amount, $orderId, $product->title, $orderDate);
                $user->balance -= $amount;
                $user->total_orders++;
                $user->total_spent += $amount;

                // Trừ count steam_account
                $newCount = $this->decrementSteamAccount($steamAccount->id, $orderDate);
                if ($newCount === 0) $stats['accounts_sold']++;

                // Tạo review
                $rating = $this->generateRating($product, $pattern);
                $this->createReview($orderId, $user->id, $product->id, $rating, $orderDate);
                $stats['reviews']++;

                // Tạo interactions
                $this->createInteractions($user->id, $product->id, $orderDate);
                $stats['interactions'] += 5;

                $purchasedProductIds[] = $product->id;
                $stats['orders']++;
                $stats['total_spent'] += $amount;
            }
        }

        $user->save();
    }

    protected function createDeposit($user, $amount, $date): void
    {
        DB::table('transactions')->insert([
            'user_id' => $user->id,
            'transaction_code' => 'DEP' . strtoupper(uniqid()) . rand(100, 999),
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $user->balance,
            'balance_after' => $user->balance + $amount,
            'status' => 'completed',
            'payment_method' => 'vnpay',
            'description' => 'Nạp tiền qua VNPay',
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }

    protected function createOrder($user, $product, $steamAccount, $amount, $date): ?int
    {
        return DB::table('orders')->insertGetId([
            'order_code' => 'ORD' . strtoupper(uniqid()),
            'buyer_id' => $user->id,
            'steam_account_id' => $steamAccount->id,
            'product_simple_id' => $product->id,
            'amount' => $amount,
            'fee' => 0,
            'payment_method' => 'balance',
            'status' => 'completed',
            'completed_at' => $date,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }

    protected function createPurchaseTransaction($user, $amount, $orderId, $productTitle, $date): void
    {
        DB::table('transactions')->insert([
            'user_id' => $user->id,
            'transaction_code' => 'PUR' . strtoupper(uniqid()) . rand(100, 999),
            'type' => 'purchase',
            'amount' => -$amount,
            'balance_before' => $user->balance,
            'balance_after' => $user->balance - $amount,
            'status' => 'completed',
            'payment_method' => 'balance',
            'description' => "Mua: {$productTitle}",
            'order_id' => $orderId,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }

    protected function decrementSteamAccount($accountId, $date): int
    {
        $account = DB::table('steam_accounts')->where('id', $accountId)->first();
        $newCount = max(0, ($account->count ?? 1) - 1);

        $updateData = ['count' => $newCount];
        if ($newCount === 0) {
            $updateData['status'] = 'sold';
            $updateData['sold_at'] = $date;
        }

        DB::table('steam_accounts')->where('id', $accountId)->update($updateData);
        return $newCount;
    }

    protected function createReview($orderId, $userId, $productId, $rating, $orderDate): void
    {
        $comment = $this->reviewComments[$rating][array_rand($this->reviewComments[$rating])];
        $reviewDate = $orderDate->copy()->addHours(rand(2, 72));

        DB::table('reviews')->insert([
            'order_id' => $orderId,
            'buyer_id' => $userId,
            'product_simple_id' => $productId,
            'rating' => $rating,
            'comment' => $comment,
            'is_verified_purchase' => true,
            'created_at' => $reviewDate,
            'updated_at' => $reviewDate,
        ]);
    }

    protected function createInteractions($userId, $productId, $orderDate): void
    {
        $interactions = [
            ['type' => 'view', 'value' => 1.0, 'hours_before' => rand(48, 96)],
            ['type' => 'view', 'value' => 1.0, 'hours_before' => rand(12, 48)],
            ['type' => 'cart_add', 'value' => 2.0, 'hours_before' => rand(1, 12)],
            ['type' => 'purchase', 'value' => 5.0, 'hours_before' => 0],
            ['type' => 'review', 'value' => 3.0, 'hours_before' => -rand(2, 72)],
        ];

        foreach ($interactions as $int) {
            DB::table('user_product_interactions')->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'interaction_type' => $int['type'],
                'interaction_value' => $int['value'],
                'created_at' => $orderDate->copy()->subHours($int['hours_before']),
                'updated_at' => $orderDate->copy()->subHours($int['hours_before']),
            ]);
        }
    }

    protected function selectProduct($products, $productsByCategory, $pattern, $excludeIds): ?ProductSimple
    {
        $available = $products->whereNotIn('id', $excludeIds);
        if ($available->isEmpty()) return null;

        // Check pattern preference
        if (!empty($pattern['categories']) && rand(1, 100) <= ($pattern['preferred_ratio'] * 100)) {
            foreach ($pattern['categories'] as $cat) {
                $catProducts = $available->where('category', $cat);
                if ($catProducts->isNotEmpty()) {
                    return $catProducts->random();
                }
            }
        }

        return $available->random();
    }

    protected function findAvailableSteamAccount($productId)
    {
        // Tìm account liên kết với product và còn count > 0
        $account = DB::table('steam_account_games')
            ->join('steam_accounts', 'steam_accounts.id', '=', 'steam_account_games.steam_account_id')
            ->where('steam_account_games.product_simple_id', $productId)
            ->where('steam_accounts.status', 'available')
            ->where('steam_accounts.count', '>', 0)
            ->select('steam_accounts.*')
            ->first();

        if ($account) return $account;

        // Fallback: random account còn hàng
        return DB::table('steam_accounts')
            ->where('status', 'available')
            ->where('count', '>', 0)
            ->inRandomOrder()
            ->first();
    }

    protected function generateRating($product, $pattern): int
    {
        $isPreferred = in_array($product->category, $pattern['categories'] ?? []);
        
        if ($isPreferred) {
            // Rating cao hơn cho game yêu thích (3-5, bias +1)
            return min(5, rand(3, 5) + ($pattern['rating_bias'] ?? 0));
        }

        // Rating bình thường với distribution tự nhiên
        $rand = rand(1, 100);
        if ($rand <= 5) return 1;      // 5% 1 sao
        if ($rand <= 15) return 2;     // 10% 2 sao  
        if ($rand <= 40) return 3;     // 25% 3 sao
        if ($rand <= 75) return 4;     // 35% 4 sao
        return 5;                       // 25% 5 sao
    }

    protected function extractPrice($priceString): float
    {
        if (!$priceString) return 0;
        preg_match_all('/[\d.,]+/', str_replace('.', '', $priceString), $matches);
        return !empty($matches[0]) ? (float) str_replace(',', '', end($matches[0])) : 0;
    }

    protected function displayPatternDistribution($patterns, $perPattern, $total): void
    {
        $this->command->table(
            ['Pattern', 'Categories', 'Users', 'Preferred Ratio'],
            collect($patterns)->map(function ($key, $i) use ($perPattern, $total) {
                $start = $i * $perPattern + 1;
                $end = min(($i + 1) * $perPattern, $total);
                $pattern = $this->userPatterns[$key];
                return [
                    $key,
                    implode(', ', $pattern['categories']) ?: 'Random',
                    "{$start} - {$end}",
                    ($pattern['preferred_ratio'] * 100) . '%'
                ];
            })->toArray()
        );
    }

    protected function displayResults($stats): void
    {
        $this->command->info('✅ Hoàn thành tạo dữ liệu!');
        $this->command->table(
            ['Metric', 'Value'],
            [
                ['💰 Giao dịch nạp tiền', number_format($stats['deposits'])],
                ['🛒 Đơn hàng', number_format($stats['orders'])],
                ['⭐ Reviews', number_format($stats['reviews'])],
                ['📊 Interactions', number_format($stats['interactions'])],
                ['🎮 Accounts sold (count=0)', number_format($stats['accounts_sold'])],
                ['💵 Tổng nạp', number_format($stats['total_deposited']) . 'đ'],
                ['💸 Tổng chi', number_format($stats['total_spent']) . 'đ'],
            ]
        );

        // Rating distribution
        $ratings = DB::table('reviews')
            ->select('rating', DB::raw('COUNT(*) as cnt'))
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();

        $this->command->newLine();
        $this->command->info('📈 Phân bố Rating:');
        $total = $ratings->sum('cnt');
        $this->command->table(
            ['Rating', 'Count', '%'],
            $ratings->map(fn($r) => [
                str_repeat('⭐', $r->rating),
                $r->cnt,
                round($r->cnt / $total * 100, 1) . '%'
            ])->toArray()
        );
    }
}
