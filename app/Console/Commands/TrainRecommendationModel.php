<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Log;

class TrainRecommendationModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recommendation:train 
                            {--force : Force retrain even if recently trained}
                            {--detailed : Show detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Train the AI recommendation model based on user orders, reviews, and interactions';

    protected $recommendationService;

    /**
     * Create a new command instance.
     */
    public function __construct(RecommendationService $recommendationService)
    {
        parent::__construct();
        $this->recommendationService = $recommendationService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('');
        $this->info('╔════════════════════════════════════════════════════════════╗');
        $this->info('║       🤖 AI RECOMMENDATION SYSTEM - TRAINING               ║');
        $this->info('╚════════════════════════════════════════════════════════════╝');
        $this->info('');

        $this->info('🚀 Bắt đầu training recommendation model...');
        $this->newLine();

        try {
            $this->info('📊 Bước 1: Thu thập dữ liệu interactions...');
            
            $startTime = microtime(true);
            
            // Show progress bar
            $this->output->write('   Processing: ');
            
            // Train the model
            $result = $this->recommendationService->train($this->option('force'));
            
            $duration = round(microtime(true) - $startTime, 2);
            
            $this->newLine();
            $this->newLine();
            $this->info('✅ Training hoàn thành!');
            $this->newLine();
            
            $this->table(
                ['Metric', 'Value'],
                [
                    ['⏱️  Thời gian', "{$duration} giây"],
                    ['👥 Users processed', $result['users_processed']],
                    ['📦 Products processed', $result['products_processed']],
                    ['🎯 Total recommendations', $result['recommendations_created']],
                ]
            );
            
            $this->newLine();
            $this->info('💡 Recommendations đã sẵn sàng để sử dụng!');
            $this->newLine();
            
            Log::info('Recommendation model trained successfully', $result);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Lỗi khi training: ' . $e->getMessage());
            $this->newLine();
            
            if ($this->option('detailed')) {
                $this->error('Stack trace:');
                $this->line($e->getTraceAsString());
            }
            
            Log::error('Recommendation training failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }
}
