<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\CommunityPost;
use App\Models\ProductSimple;
use Illuminate\Support\Facades\DB;

class CommunityPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all users
        $users = User::all();
        
        if ($users->count() < 3) {
            $this->command->warn('Cần ít nhất 3 users. Hãy seed users trước.');
            return;
        }
        
        // Get actual games from database
        $games = ProductSimple::select('id', 'title', 'category', 'image')
            ->whereNotNull('title')
            ->inRandomOrder()
            ->limit(30)
            ->get();
            
        if ($games->isEmpty()) {
            $this->command->warn('Không có game trong database. Hãy seed products trước.');
            return;
        }
        
        $this->command->info('Tìm thấy ' . $games->count() . ' games trong database');

        $this->command->info('Đang tạo friendships...');
        $this->seedFriendships($users);
        
        $this->command->info('Đang tạo community posts...');
        $this->seedCommunityPosts($users, $games);
        
        $this->command->info('Đang tạo likes và comments...');
        $this->seedLikesAndComments($users);
        
        $this->command->info('✅ Community data seeded thành công!');
    }

    /**
     * Create friendships for each user (around 10 friends each)
     */
    private function seedFriendships($users)
    {
        // Disable foreign key checks and clear existing friendships
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('friendships')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $userIds = $users->pluck('id')->toArray();
        $friendships = [];
        
        foreach ($users as $user) {
            // Get other users excluding self
            $otherUsers = array_diff($userIds, [$user->id]);
            
            // Pick random 8-12 friends for each user
            $friendCount = min(rand(8, 12), count($otherUsers));
            $friendIds = array_rand(array_flip($otherUsers), $friendCount);
            
            if (!is_array($friendIds)) {
                $friendIds = [$friendIds];
            }
            
            foreach ($friendIds as $friendId) {
                // Check if reverse friendship already exists
                $exists = isset($friendships[$friendId . '-' . $user->id]);
                
                if (!$exists && !isset($friendships[$user->id . '-' . $friendId])) {
                    $friendships[$user->id . '-' . $friendId] = [
                        'sender_id' => $user->id,
                        'receiver_id' => $friendId,
                        'status' => 'accepted',
                        'created_at' => now()->subDays(rand(1, 60)),
                        'updated_at' => now()->subDays(rand(0, 30)),
                    ];
                }
            }
        }
        
        // Batch insert friendships
        $chunks = array_chunk(array_values($friendships), 100);
        foreach ($chunks as $chunk) {
            DB::table('friendships')->insert($chunk);
        }
        
        $this->command->info('  - Đã tạo ' . count($friendships) . ' friendships');
    }

    /**
     * Create community posts based on actual games from database
     */
    private function seedCommunityPosts($users, $games)
    {
        // Disable foreign key checks to allow truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        DB::table('community_post_comments')->truncate();
        DB::table('community_post_likes')->truncate();
        CommunityPost::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Post templates that will be filled with actual game names
        $gamePostTemplates = [
            // Tìm người chơi cùng
            'Ai đang chơi {game} không? Mình đang tìm người chơi cùng! 🎮',
            'Tìm team {game} cố định để chơi rank. Inbox mình nhé!',
            'Có ai chơi {game} không? Add friend mình với: ',
            'Cần tìm 2-3 bạn chơi {game}. Yêu cầu: vui vẻ, không toxic 😊',
            
            // Review/Cảm nhận
            'Vừa mua {game} trên shop, game hay quá! 10/10 recommend 🔥',
            'Review nhanh {game}: Đồ họa đẹp, gameplay cuốn, worth every penny!',
            '{game} đang giảm giá, mọi người nên mua ngay! 💰',
            'Sau 50 giờ chơi {game}, mình phải nói là game này quá đỉnh!',
            
            // Tips/Hướng dẫn
            'Chia sẻ tips chơi {game} cho newbie. Comment hỏi mình nhé!',
            'Tips và tricks {game}: Những điều mình ước biết sớm hơn',
            'Hướng dẫn build character OP trong {game}',
            
            // Thảo luận
            'Mọi người đánh giá {game} như thế nào? Comment ý kiến đi!',
            '{game} có DLC mới rồi! Ai đã chơi chưa?',
            'So sánh {game} với phần trước: Cái nào hay hơn?',
            
            // Achievement/Khoe
            'Vừa hoàn thành 100% {game}! Mất 2 tuần nhưng worth it 🏆',
            'Clutch moment trong {game} tối qua! Ai có highlight hay share đi',
            'Đã rank Diamond trong {game} sau 3 tháng grind! 💎',
        ];
        
        // General posts (không thuộc game group nào)
        $generalPosts = [
            'Mọi người đang chơi game gì thế? Mình đang tìm game mới để chơi cuối tuần 🎮',
            'Vừa custom setup gaming mới xong! Cảm giác chơi game quá đã 😍',
            'Ai có tips build PC gaming tầm 20-25 triệu không ạ?',
            'Review headphone gaming mới mua: âm thanh 7.1 thật sự khác biệt!',
            'Cảm ơn anh em đã tham gia giải đấu tối qua! 🏆',
            'Mới mua bàn phím cơ, gõ sướng quá! Cherry MX Blue',
            'Đang sale game hot, ai đã mua những game nào rồi?',
            'Tìm người chơi cùng để giảm stress sau giờ làm. Chill gaming thôi!',
            'Setup góc chơi game nhỏ xinh của mình 🎨',
            'Cuối tuần này có ai rảnh chơi game online không?',
        ];
        
        $posts = [];
        
        // Create general posts (không thuộc game group)
        foreach ($generalPosts as $content) {
            $user = $users->random();
            
            $posts[] = [
                'user_id' => $user->id,
                'content' => $content,
                'images' => [],
                'videos' => [],
                'game_preference' => null, // Bài chung
                'likes_count' => rand(5, 40),
                'comments_count' => rand(2, 15),
                'privacy' => 'public',
                'is_active' => true,
                'created_at' => now()->subDays(rand(0, 20))->subHours(rand(0, 23)),
                'updated_at' => now()->subDays(rand(0, 10)),
            ];
        }
        
        // Create game-specific posts using actual games from database
        foreach ($games as $game) {
            // Mỗi game có 2-4 bài viết
            $postsCount = rand(2, 4);
            $selectedTemplates = array_rand($gamePostTemplates, $postsCount);
            
            if (!is_array($selectedTemplates)) {
                $selectedTemplates = [$selectedTemplates];
            }
            
            foreach ($selectedTemplates as $templateIndex) {
                $template = $gamePostTemplates[$templateIndex];
                $user = $users->random();
                
                // Replace {game} với tên game thực
                $content = str_replace('{game}', $game->title, $template);
                
                // Có thể sử dụng ảnh của game
                $useGameImage = rand(0, 10) > 5;
                $images = ($useGameImage && $game->image) ? [$game->image] : [];
                
                $posts[] = [
                    'user_id' => $user->id,
                    'content' => $content,
                    'images' => $images,
                    'videos' => [],
                    'game_preference' => $game->title, // Game group
                    'likes_count' => rand(3, 30),
                    'comments_count' => rand(1, 12),
                    'privacy' => 'public',
                    'is_active' => true,
                    'created_at' => now()->subDays(rand(0, 25))->subHours(rand(0, 23)),
                    'updated_at' => now()->subDays(rand(0, 10)),
                ];
            }
        }
        
        // Shuffle posts
        shuffle($posts);
        
        // Insert posts
        foreach ($posts as $post) {
            CommunityPost::create([
                'user_id' => $post['user_id'],
                'content' => $post['content'],
                'images' => $post['images'],
                'videos' => $post['videos'],
                'game_preference' => $post['game_preference'],
                'likes_count' => $post['likes_count'],
                'comments_count' => $post['comments_count'],
                'privacy' => $post['privacy'],
                'is_active' => $post['is_active'],
                'created_at' => $post['created_at'],
                'updated_at' => $post['updated_at'],
            ]);
        }
        
        $this->command->info('  - Đã tạo ' . count($posts) . ' posts');
        $this->command->info('  - Game groups: ' . $games->pluck('title')->unique()->count());
    }

    /**
     * Create likes and comments for posts
     */
    private function seedLikesAndComments($users)
    {
        $posts = CommunityPost::all();
        $userIds = $users->pluck('id')->toArray();
        
        $comments = [
            'Hay quá! 🔥',
            'Mình cũng đang chơi game này!',
            'Setup đẹp quá bạn ơi 😍',
            'Add friend mình với: ',
            'Cảm ơn đã chia sẻ!',
            'Quá đỉnh luôn!',
            'Mình cũng muốn join team',
            'Chúc mừng bạn nhé! 🎉',
            'Tips hay quá, thanks!',
            'Mình inbox bạn nha',
            'GG! Chơi hay lắm 👏',
            'Ai rảnh thì inbox mình nhé',
            'Chill game là nhất!',
            'Mình follow bạn rồi nè',
            'Hẹn gặp lại trong game!',
            'Server nào vậy bạn?',
            'Build này ngon đấy!',
            'Mình newbie có được không ạ?',
            'Có discord không bạn?',
            'Weekend này mình free nè',
        ];
        
        $likesData = [];
        $commentsData = [];
        
        foreach ($posts as $post) {
            // Create likes (random users like this post)
            $likeCount = min($post->likes_count, count($userIds));
            $likerIds = array_rand(array_flip($userIds), max(1, $likeCount));
            
            if (!is_array($likerIds)) {
                $likerIds = [$likerIds];
            }
            
            foreach ($likerIds as $likerId) {
                $likesData[] = [
                    'post_id' => $post->id,
                    'user_id' => $likerId,
                    'created_at' => now()->subDays(rand(0, 10)),
                    'updated_at' => now()->subDays(rand(0, 5)),
                ];
            }
            
            // Create comments
            $commentCount = min($post->comments_count, 5);
            for ($i = 0; $i < $commentCount; $i++) {
                $commentsData[] = [
                    'post_id' => $post->id,
                    'user_id' => $userIds[array_rand($userIds)],
                    'content' => $comments[array_rand($comments)],
                    'likes_count' => rand(0, 10),
                    'created_at' => now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
                    'updated_at' => now()->subDays(rand(0, 3)),
                ];
            }
        }
        
        // Batch insert likes
        $likeChunks = array_chunk($likesData, 100);
        foreach ($likeChunks as $chunk) {
            DB::table('community_post_likes')->insert($chunk);
        }
        
        // Batch insert comments
        $commentChunks = array_chunk($commentsData, 100);
        foreach ($commentChunks as $chunk) {
            DB::table('community_post_comments')->insert($chunk);
        }
        
        $this->command->info('  - Đã tạo ' . count($likesData) . ' likes');
        $this->command->info('  - Đã tạo ' . count($commentsData) . ' comments');
    }
}
