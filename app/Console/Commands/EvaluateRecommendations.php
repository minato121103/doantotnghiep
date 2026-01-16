<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ProductSimple;
use App\Models\User;

class EvaluateRecommendations extends Command
{
    protected $signature = 'recommendation:evaluate {--k=10 : Top K recommendations to evaluate}';
    protected $description = 'Evaluate recommendation system using Precision@K, Recall@K, and other metrics';

    public function handle()
    {
        $k = (int) $this->option('k');
        
        $this->info('');
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║  📊 RECOMMENDATION SYSTEM EVALUATION                       ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->info('');

        // Get users with both recommendations and actual purchases
        $users = User::where('role', 'buyer')->get();
        
        $precisions = [];
        $recalls = [];
        $hits = 0;
        $totalUsers = 0;
        $categoryMatches = [];

        $this->info("🔍 Evaluating recommendations for {$users->count()} users with K={$k}...");
        $this->newLine();

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            // Get user's actual purchases (ground truth)
            $actualPurchases = DB::table('orders')
                ->where('buyer_id', $user->id)
                ->where('status', 'completed')
                ->pluck('product_simple_id')
                ->toArray();

            if (empty($actualPurchases)) {
                $bar->advance();
                continue;
            }

            // Get user's recommendations (top K)
            $recommendations = DB::table('user_recommendations')
                ->where('user_id', $user->id)
                ->orderBy('rank')
                ->limit($k)
                ->pluck('product_id')
                ->toArray();

            if (empty($recommendations)) {
                $bar->advance();
                continue;
            }

            $totalUsers++;

            // Calculate Precision@K: How many recommended items did user actually purchase?
            // Note: In real scenario, we'd hold out some purchases for testing
            // Here we check if recommended items are from same category as purchased
            
            // Get categories of purchased products
            $purchasedCategories = ProductSimple::whereIn('id', $actualPurchases)
                ->pluck('category')
                ->unique()
                ->toArray();

            // Get categories of recommended products
            $recommendedProducts = ProductSimple::whereIn('id', $recommendations)->get();
            
            $relevant = 0;
            foreach ($recommendedProducts as $product) {
                if (in_array($product->category, $purchasedCategories)) {
                    $relevant++;
                }
            }

            // Precision@K: relevant recommendations / K
            $precision = $relevant / $k;
            $precisions[] = $precision;

            // Check if at least one recommendation matches user's interests
            if ($relevant > 0) {
                $hits++;
            }

            // Category match rate
            $categoryMatches[] = $relevant / $k;

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->newLine();

        // Calculate averages
        $avgPrecision = !empty($precisions) ? array_sum($precisions) / count($precisions) : 0;
        $hitRate = $totalUsers > 0 ? $hits / $totalUsers : 0;
        $avgCategoryMatch = !empty($categoryMatches) ? array_sum($categoryMatches) / count($categoryMatches) : 0;

        // Coverage
        $totalProducts = ProductSimple::count();
        $recommendedProducts = DB::table('user_recommendations')->distinct('product_id')->count('product_id');
        $coverage = $recommendedProducts / $totalProducts;

        // Score distribution
        $scoreStats = DB::table('user_recommendations')
            ->selectRaw('
                AVG(score) as avg_score,
                MIN(score) as min_score,
                MAX(score) as max_score,
                STDDEV(score) as std_score
            ')
            ->first();

        // Display results
        $this->info('📈 EVALUATION RESULTS:');
        $this->newLine();

        $this->table(
            ['Metric', 'Value', 'Interpretation'],
            [
                ['Precision@'.$k.' (Category Match)', round($avgPrecision * 100, 1) . '%', $this->interpretPrecision($avgPrecision)],
                ['Hit Rate', round($hitRate * 100, 1) . '%', $this->interpretHitRate($hitRate)],
                ['Coverage', round($coverage * 100, 1) . '%', $coverage > 0.8 ? '✅ Excellent' : ($coverage > 0.6 ? '⚠️ Good' : '❌ Low')],
                ['Avg Category Match', round($avgCategoryMatch * 100, 1) . '%', $avgCategoryMatch > 0.5 ? '✅ Good' : '⚠️ Needs improvement'],
            ]
        );

        $this->newLine();
        $this->info('📊 SCORE DISTRIBUTION:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Average Score', round($scoreStats->avg_score, 4)],
                ['Min Score', round($scoreStats->min_score, 4)],
                ['Max Score', round($scoreStats->max_score, 4)],
                ['Std Deviation', round($scoreStats->std_score ?? 0, 4)],
            ]
        );

        $this->newLine();
        $this->info('🎯 OVERALL ASSESSMENT:');
        
        $overallScore = ($avgPrecision * 0.4 + $hitRate * 0.3 + $coverage * 0.2 + $avgCategoryMatch * 0.1);
        $grade = $this->getGrade($overallScore);
        
        $this->table(
            ['Overall Score', 'Grade', 'Verdict'],
            [
                [round($overallScore * 100, 1) . '%', $grade['letter'], $grade['description']]
            ]
        );

        $this->newLine();
        $this->info("💡 Note: This evaluation uses category matching as a proxy for relevance.");
        $this->info("   For production, use A/B testing with real user click/purchase data.");
        $this->newLine();

        return Command::SUCCESS;
    }

    private function interpretPrecision($precision)
    {
        if ($precision >= 0.6) return '✅ Excellent - Most recommendations match user interests';
        if ($precision >= 0.4) return '✅ Good - Many recommendations are relevant';
        if ($precision >= 0.2) return '⚠️ Fair - Some recommendations are relevant';
        return '❌ Poor - Recommendations need improvement';
    }

    private function interpretHitRate($hitRate)
    {
        if ($hitRate >= 0.8) return '✅ Excellent - Almost all users get relevant recommendations';
        if ($hitRate >= 0.6) return '✅ Good - Most users get relevant recommendations';
        if ($hitRate >= 0.4) return '⚠️ Fair - Many users get relevant recommendations';
        return '❌ Poor - Few users get relevant recommendations';
    }

    private function getGrade($score)
    {
        if ($score >= 0.8) return ['letter' => 'A', 'description' => '🏆 Excellent - Production ready'];
        if ($score >= 0.7) return ['letter' => 'B+', 'description' => '✅ Very Good - Minor improvements possible'];
        if ($score >= 0.6) return ['letter' => 'B', 'description' => '✅ Good - Suitable for demo/thesis'];
        if ($score >= 0.5) return ['letter' => 'C+', 'description' => '⚠️ Fair - Needs some improvements'];
        if ($score >= 0.4) return ['letter' => 'C', 'description' => '⚠️ Below Average'];
        return ['letter' => 'D', 'description' => '❌ Needs significant improvement'];
    }
}
