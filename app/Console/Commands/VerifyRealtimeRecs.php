<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RecommendationService;
use App\Models\User;
use App\Models\ProductSimple;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class VerifyRealtimeRecs extends Command
{
    protected $signature = 'verify:realtime {userId} {productId}';
    protected $description = 'Verify real-time recommendations';

    public function handle(RecommendationService $service)
    {
        $userId = $this->argument('userId');
        $targetProductId = $this->argument('productId');

        $this->info("Verifying for User: $userId, Target Product View: $targetProductId");

        // 1. Clear cache
        Cache::forget("user_recommendations_{$userId}");
        $this->info("Cache cleared.");

        // 2. Initial Recommendations
        $initialRecs = $service->getUserRecommendations($userId, 5);
        $this->info("Initial Top 5:");
        foreach ($initialRecs as $rec) {
             $score = is_object($rec) ? ($rec->base_score ?? $rec->score ?? 0) : ($rec['score'] ?? 0);
             $title = is_object($rec) ? $rec->title : $rec['title'];
             $algo = is_object($rec) ? ($rec->algorithm ?? 'unknown') : ($rec['algorithm'] ?? 'unknown');
             $this->line("- $title (Score: $score, Algo: $algo)");
        }

        // 3. Record Interaction
        $this->info("\nRecording VIEW interaction for Product $targetProductId...");
        $service->recordInteraction($userId, $targetProductId, 'view');
        
        // 4. Get Recommendations again
        // Note: recordInteraction clears cache automatically
        $newRecs = $service->getUserRecommendations($userId, 5);
        $this->info("New Top 5:");
        foreach ($newRecs as $rec) {
             $score = is_object($rec) ? ($rec->base_score ?? $rec->score ?? 0) : ($rec['score'] ?? 0);
             $title = is_object($rec) ? $rec->title : $rec['title'];
             $algo = is_object($rec) ? ($rec->algorithm ?? 'unknown') : ($rec['algorithm'] ?? 'unknown');
             $isRealtime = strpos($algo, 'realtime') !== false;
             $style = $isRealtime ? 'info' : 'line';
             $this->$style("- $title (Score: $score, Algo: $algo)");
        }

        // 5. Cleanup (Optional, remove interaction to keep clean?)
        // keeping it for now to see effect
    }
}
