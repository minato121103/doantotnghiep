<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetSteamAccountIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:reset-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset AUTO_INCREMENT ID của bảng steam_accounts và steam_account_games về 1';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Đang reset ID...');

        try {
            // Reset AUTO_INCREMENT cho steam_account_games (phải reset trước vì có foreign key)
            DB::statement('ALTER TABLE steam_account_games AUTO_INCREMENT = 1');
            $this->info('✓ Đã reset ID của bảng steam_account_games về 1');
            
            // Reset AUTO_INCREMENT cho steam_accounts
            DB::statement('ALTER TABLE steam_accounts AUTO_INCREMENT = 1');
            $this->info('✓ Đã reset ID của bảng steam_accounts về 1');
            
            $this->newLine();
            $this->info('✓ Hoàn thành! ID đã được reset về 1.');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Lỗi: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
