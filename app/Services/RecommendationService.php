<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\ProductSimple;
use App\Models\User;

class RecommendationService
{
    /**
     * Train the recommendation model
     * Kết hợp: Collaborative Filtering + Content-Based + Popularity
     */
    public function train(bool $force = false): array
    {
        $startTime = microtime(true);
        
        // Create training log
        $logId = DB::table('recommendation_training_logs')->insertGetId([
            'status' => 'running',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            // Clear old recommendations
            DB::table('user_recommendations')->truncate();
            DB::table('product_recommendations')->truncate();

            // 1. Build User-Product interaction matrix
            $interactions = $this->buildInteractionMatrix();
            
            // 2. Generate user recommendations using hybrid approach
            $usersProcessed = $this->generateUserRecommendations($interactions);
            
            // 3. Generate similar products (content-based)
            $productsProcessed = $this->generateProductRecommendations();
            
            // 4. Generate popular recommendations for new users
            $this->generatePopularRecommendations();

            $duration = round(microtime(true) - $startTime, 2);
            
            $totalRecommendations = DB::table('user_recommendations')->count() + 
                                   DB::table('product_recommendations')->count();

            // Update log
            DB::table('recommendation_training_logs')
                ->where('id', $logId)
                ->update([
                    'status' => 'success',
                    'users_processed' => $usersProcessed,
                    'products_processed' => $productsProcessed,
                    'recommendations_created' => $totalRecommendations,
                    'duration_seconds' => $duration,
                    'metadata' => json_encode([
                        'interaction_count' => count($interactions),
                        'algorithms' => ['collaborative', 'content', 'popular', 'hybrid']
                    ]),
                    'updated_at' => now(),
                ]);

            // Clear cache
            $this->clearCache();

            return [
                'users_processed' => $usersProcessed,
                'products_processed' => $productsProcessed,
                'recommendations_created' => $totalRecommendations,
                'duration' => $duration,
            ];

        } catch (\Exception $e) {
            DB::table('recommendation_training_logs')
                ->where('id', $logId)
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'duration_seconds' => round(microtime(true) - $startTime, 2),
                    'updated_at' => now(),
                ]);

            throw $e;
        }
    }

    /**
     * Build user-product interaction matrix from orders, reviews, views
     */
    private function buildInteractionMatrix(): array
    {
        $interactions = [];

        // Get from completed orders (weight: 5)
        $orders = DB::table('orders')
            ->where('status', 'completed')
            ->select('buyer_id as user_id', 'product_simple_id as product_id')
            ->get();

        foreach ($orders as $order) {
            $key = $order->user_id . '_' . $order->product_id;
            if (!isset($interactions[$key])) {
                $interactions[$key] = [
                    'user_id' => $order->user_id,
                    'product_id' => $order->product_id,
                    'score' => 0
                ];
            }
            $interactions[$key]['score'] += 5;
        }

        // Get from reviews (weight based on rating: 1-5)
        $reviews = DB::table('reviews')
            ->whereNotNull('rating')
            ->select('buyer_id as user_id', 'product_simple_id as product_id', 'rating')
            ->get();

        foreach ($reviews as $review) {
            $key = $review->user_id . '_' . $review->product_id;
            if (!isset($interactions[$key])) {
                $interactions[$key] = [
                    'user_id' => $review->user_id,
                    'product_id' => $review->product_id,
                    'score' => 0
                ];
            }
            $interactions[$key]['score'] += $review->rating;
        }

        // Get from user_product_interactions table if exists
        if (Schema::hasTable('user_product_interactions')) {
            $viewInteractions = DB::table('user_product_interactions')
                ->select('user_id', 'product_id', DB::raw('SUM(interaction_value) as total_value'))
                ->groupBy('user_id', 'product_id')
                ->get();

            foreach ($viewInteractions as $interaction) {
                $key = $interaction->user_id . '_' . $interaction->product_id;
                if (!isset($interactions[$key])) {
                    $interactions[$key] = [
                        'user_id' => $interaction->user_id,
                        'product_id' => $interaction->product_id,
                        'score' => 0
                    ];
                }
                $interactions[$key]['score'] += $interaction->total_value;
            }
        }

        return array_values($interactions);
    }

    /**
     * Generate user recommendations using Collaborative Filtering
     */
    private function generateUserRecommendations(array $interactions): int
    {
        if (empty($interactions)) {
            return 0;
        }

        // Group interactions by user
        $userInteractions = [];
        foreach ($interactions as $interaction) {
            $userId = $interaction['user_id'];
            if (!isset($userInteractions[$userId])) {
                $userInteractions[$userId] = [];
            }
            $userInteractions[$userId][$interaction['product_id']] = $interaction['score'];
        }

        // Get all products
        $allProducts = ProductSimple::pluck('id')->toArray();
        
        $usersProcessed = 0;
        $recommendations = [];

        foreach ($userInteractions as $userId => $userProducts) {
            // Find similar users (users who bought same products)
            $similarUsers = $this->findSimilarUsers($userId, $userProducts, $userInteractions);
            
            // Get products from similar users that this user hasn't interacted with
            $recommendedProducts = $this->getRecommendedProducts(
                $userId, 
                $userProducts, 
                $similarUsers, 
                $userInteractions,
                $allProducts
            );

            // Also add content-based recommendations
            $contentBased = $this->getContentBasedRecommendations($userId, array_keys($userProducts));
            
            // Merge and rank
            foreach ($recommendedProducts as $productId => $score) {
                if (isset($contentBased[$productId])) {
                    $recommendedProducts[$productId] = ($score + $contentBased[$productId]) / 2;
                }
            }
            foreach ($contentBased as $productId => $score) {
                if (!isset($recommendedProducts[$productId])) {
                    $recommendedProducts[$productId] = $score * 0.8; // Lower weight for content-only
                }
            }

            // Sort by score and take top 20
            arsort($recommendedProducts);
            $topRecommendations = array_slice($recommendedProducts, 0, 20, true);

            // Normalize scores: chia cho max score để có range 0-1 nhưng vẫn giữ sự khác biệt
            $maxScore = !empty($topRecommendations) ? max($topRecommendations) : 1;
            
            $rank = 1;
            foreach ($topRecommendations as $productId => $score) {
                // Normalize: score / maxScore để giữ tỷ lệ tương đối
                $normalizedScore = $maxScore > 0 ? round($score / $maxScore, 4) : 0;
                
                $recommendations[] = [
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'score' => $normalizedScore,
                    'algorithm' => 'hybrid',
                    'rank' => $rank++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $usersProcessed++;
        }

        // Batch insert
        if (!empty($recommendations)) {
            foreach (array_chunk($recommendations, 500) as $chunk) {
                DB::table('user_recommendations')->insert($chunk);
            }
        }

        return $usersProcessed;
    }

    /**
     * Find similar users using cosine similarity
     */
    private function findSimilarUsers(int $targetUserId, array $targetProducts, array $allUserInteractions): array
    {
        $similarities = [];

        foreach ($allUserInteractions as $userId => $products) {
            if ($userId == $targetUserId) continue;

            // Calculate cosine similarity
            $similarity = $this->cosineSimilarity($targetProducts, $products);
            
            if ($similarity > 0.1) { // Threshold
                $similarities[$userId] = $similarity;
            }
        }

        // Sort by similarity and return top 10
        arsort($similarities);
        return array_slice($similarities, 0, 10, true);
    }

    /**
     * Calculate cosine similarity between two users
     */
    private function cosineSimilarity(array $vector1, array $vector2): float
    {
        $intersection = array_intersect_key($vector1, $vector2);
        
        if (empty($intersection)) {
            return 0;
        }

        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        foreach ($intersection as $key => $value) {
            $dotProduct += $vector1[$key] * $vector2[$key];
        }

        foreach ($vector1 as $value) {
            $magnitude1 += $value * $value;
        }

        foreach ($vector2 as $value) {
            $magnitude2 += $value * $value;
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0;
        }

        return $dotProduct / ($magnitude1 * $magnitude2);
    }

    /**
     * Get recommended products from similar users
     * Uses weighted average: Σ(similarity × score) / Σ(similarity)
     */
    private function getRecommendedProducts(
        int $userId, 
        array $userProducts, 
        array $similarUsers, 
        array $allUserInteractions,
        array $allProducts
    ): array {
        $scores = [];
        $totalSimilarity = []; // Tổng similarity cho mỗi product

        foreach ($similarUsers as $similarUserId => $similarity) {
            $similarUserProducts = $allUserInteractions[$similarUserId] ?? [];
            
            foreach ($similarUserProducts as $productId => $score) {
                // Skip products user already has
                if (isset($userProducts[$productId])) continue;
                
                if (!isset($scores[$productId])) {
                    $scores[$productId] = 0;
                    $totalSimilarity[$productId] = 0;
                }
                
                // Cộng dồn: similarity × score
                $scores[$productId] += $similarity * $score;
                // Cộng dồn tổng similarity
                $totalSimilarity[$productId] += $similarity;
            }
        }

        // Chia cho tổng similarity để có điểm trung bình có trọng số
        foreach ($scores as $productId => $score) {
            if ($totalSimilarity[$productId] > 0) {
                $scores[$productId] = $score / $totalSimilarity[$productId];
            }
        }

        return $scores;
    }

    /**
     * Get content-based recommendations based on category and tags
     */
    private function getContentBasedRecommendations(int $userId, array $purchasedProductIds): array
    {
        if (empty($purchasedProductIds)) {
            return [];
        }

        // Get categories and tags from purchased products
        $purchasedProducts = ProductSimple::whereIn('id', $purchasedProductIds)->get();
        
        $categories = $purchasedProducts->pluck('category')->unique()->filter()->toArray();
        $allTags = [];
        foreach ($purchasedProducts as $product) {
            if (!empty($product->tags)) {
                $allTags = array_merge($allTags, (array)$product->tags);
            }
        }
        $allTags = array_unique($allTags);

        // Find similar products
        $similarProducts = ProductSimple::whereNotIn('id', $purchasedProductIds)
            ->where(function ($query) use ($categories, $allTags) {
                if (!empty($categories)) {
                    $query->whereIn('category', $categories);
                }
                if (!empty($allTags)) {
                    foreach ($allTags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->get();

        $scores = [];
        foreach ($similarProducts as $product) {
            $score = 0;
            
            // Category match
            if (in_array($product->category, $categories)) {
                $score += 3;
            }
            
            // Tag matches
            if (!empty($product->tags)) {
                $tagMatches = count(array_intersect((array)$product->tags, $allTags));
                $score += $tagMatches * 2;
            }
            
            // View count bonus
            $score += min(2, ($product->view_count ?? 0) / 1000);
            
            $scores[$product->id] = $score;
        }

        return $scores;
    }

    /**
     * Generate product-to-product recommendations (similar products)
     */
    private function generateProductRecommendations(): int
    {
        $products = ProductSimple::all();
        $recommendations = [];
        $productsProcessed = 0;

        foreach ($products as $product) {
            $similar = $this->findSimilarProducts($product, $products);
            
            $rank = 1;
            foreach ($similar as $similarProductId => $score) {
                $recommendations[] = [
                    'product_id' => $product->id,
                    'similar_product_id' => $similarProductId,
                    'similarity_score' => min(1, $score / 10),
                    'algorithm' => 'content',
                    'rank' => $rank++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            $productsProcessed++;
        }

        // Batch insert
        if (!empty($recommendations)) {
            foreach (array_chunk($recommendations, 500) as $chunk) {
                DB::table('product_recommendations')->insert($chunk);
            }
        }

        return $productsProcessed;
    }

    /**
     * Find similar products based on category, tags, type
     */
    private function findSimilarProducts(ProductSimple $targetProduct, $allProducts): array
    {
        $scores = [];

        foreach ($allProducts as $product) {
            if ($product->id == $targetProduct->id) continue;

            $score = 0;

            // Same category
            if ($product->category == $targetProduct->category) {
                $score += 5;
            }

            // Same type (online/offline)
            if ($product->type == $targetProduct->type) {
                $score += 2;
            }

            // Tag similarity
            $targetTags = (array)($targetProduct->tags ?? []);
            $productTags = (array)($product->tags ?? []);
            
            if (!empty($targetTags) && !empty($productTags)) {
                $commonTags = count(array_intersect($targetTags, $productTags));
                $score += $commonTags * 3;
            }

            // Price similarity (within 20% range)
            $targetPrice = $this->extractPrice($targetProduct->price);
            $productPrice = $this->extractPrice($product->price);
            
            if ($targetPrice > 0 && $productPrice > 0) {
                $priceDiff = abs($targetPrice - $productPrice) / $targetPrice;
                if ($priceDiff <= 0.2) {
                    $score += 2;
                }
            }

            if ($score > 0) {
                $scores[$product->id] = $score;
            }
        }

        // Sort and return top 10
        arsort($scores);
        return array_slice($scores, 0, 10, true);
    }

    /**
     * Extract numeric price from string
     */
    private function extractPrice(string $priceString): float
    {
        // Get the last number in the string (current price)
        preg_match_all('/[\d,.]+/', $priceString, $matches);
        if (!empty($matches[0])) {
            $price = end($matches[0]);
            return (float)str_replace(['.', ','], ['', '.'], $price);
        }
        return 0;
    }

    /**
     * Generate popular recommendations for new users
     */
    private function generatePopularRecommendations(): void
    {
        // Get most popular products by orders and views
        $popular = DB::table('product_simple')
            ->leftJoin('orders', 'product_simple.id', '=', 'orders.product_simple_id')
            ->select(
                'product_simple.id',
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('COALESCE(product_simple.view_count, 0) as view_count')
            )
            ->groupBy('product_simple.id', 'product_simple.view_count')
            ->orderByRaw('COUNT(orders.id) DESC, COALESCE(product_simple.view_count, 0) DESC')
            ->limit(20)
            ->get();

        // Store in cache for new users
        Cache::put('popular_recommendations', $popular->pluck('id')->toArray(), now()->addDay());
    }

    /**
     * Get recommendations for a user
     */
    public function getUserRecommendations(int $userId, int $limit = 10): array
    {
        $cacheKey = "user_recommendations_{$userId}";
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($userId, $limit) {
            // Check if user has personalized recommendations
            $recommendations = DB::table('user_recommendations')
                ->join('product_simple', 'user_recommendations.product_id', '=', 'product_simple.id')
                ->where('user_recommendations.user_id', $userId)
                ->orderBy('user_recommendations.rank')
                ->limit($limit)
                ->select('product_simple.*', 'user_recommendations.score', 'user_recommendations.algorithm')
                ->get()
                ->toArray();

            // If no personalized recommendations, return popular items
            if (empty($recommendations)) {
                $popularIds = Cache::get('popular_recommendations', []);
                
                if (!empty($popularIds)) {
                    $recommendations = ProductSimple::whereIn('id', array_slice($popularIds, 0, $limit))
                        ->get()
                        ->map(function ($product) {
                            $product->score = 0.5;
                            $product->algorithm = 'popular';
                            return $product;
                        })
                        ->toArray();
                }
            }

            return $recommendations;
        });
    }

    /**
     * Get similar products
     */
    public function getSimilarProducts(int $productId, int $limit = 10): array
    {
        $cacheKey = "similar_products_{$productId}";
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($productId, $limit) {
            return DB::table('product_recommendations')
                ->join('product_simple', 'product_recommendations.similar_product_id', '=', 'product_simple.id')
                ->where('product_recommendations.product_id', $productId)
                ->orderBy('product_recommendations.rank')
                ->limit($limit)
                ->select('product_simple.*', 'product_recommendations.similarity_score')
                ->get()
                ->toArray();
        });
    }

    /**
     * Record user interaction
     */
    public function recordInteraction(int $userId, int $productId, string $type): void
    {
        $values = [
            'view' => 1,
            'cart_add' => 2,
            'purchase' => 5,
            'review' => 3,
            'wishlist' => 2,
        ];

        DB::table('user_product_interactions')->insert([
            'user_id' => $userId,
            'product_id' => $productId,
            'interaction_type' => $type,
            'interaction_value' => $values[$type] ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Clear user's recommendation cache
        Cache::forget("user_recommendations_{$userId}");
    }

    /**
     * Clear all recommendation caches
     */
    public function clearCache(): void
    {
        // Clear popular recommendations
        Cache::forget('popular_recommendations');
        
        // Clear user-specific caches (this is simplified, in production use tags)
        $userIds = DB::table('user_recommendations')->distinct()->pluck('user_id');
        foreach ($userIds as $userId) {
            Cache::forget("user_recommendations_{$userId}");
        }
        
        // Clear product caches
        $productIds = DB::table('product_recommendations')->distinct()->pluck('product_id');
        foreach ($productIds as $productId) {
            Cache::forget("similar_products_{$productId}");
        }
    }

    /**
     * Get training statistics
     */
    public function getStats(): array
    {
        return [
            'users_with_recommendations' => DB::table('user_recommendations')
                ->distinct('user_id')->count('user_id'),
            'products_with_recommendations' => DB::table('product_recommendations')
                ->distinct('product_id')->count('product_id'),
            'total_user_recommendations' => DB::table('user_recommendations')->count(),
            'total_product_recommendations' => DB::table('product_recommendations')->count(),
            'total_interactions' => DB::table('user_product_interactions')->count(),
            'last_training' => DB::table('recommendation_training_logs')
                ->latest('created_at')
                ->first(),
        ];
    }
}
