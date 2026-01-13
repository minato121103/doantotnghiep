<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductSimple;
use App\Models\SteamAccount;
use Illuminate\Support\Facades\DB;

class GenerateSteamAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:generate-accounts {--fresh : Xóa tất cả dữ liệu cũ trước khi tạo mới} {--reset-ids : Reset ID về 1 sau khi xóa dữ liệu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo 3 tài khoản Steam cho mỗi game trong database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Xóa dữ liệu cũ nếu có option --fresh
        if ($this->option('fresh')) {
            if (!$this->confirm('Bạn có chắc muốn xóa TẤT CẢ dữ liệu tài khoản Steam cũ? Hành động này không thể hoàn tác!')) {
                $this->info('Đã hủy.');
                return Command::SUCCESS;
            }

            $this->info('Đang xóa dữ liệu cũ...');
            
            DB::beginTransaction();
            try {
                // Đếm số lượng trước khi xóa
                $countAccounts = SteamAccount::withTrashed()->count();
                $countPivot = DB::table('steam_account_games')->count();
                
                // Xóa pivot table trước (vì có foreign key)
                DB::table('steam_account_games')->delete();
                
                // Xóa tất cả tài khoản (force delete để xóa cả soft deleted)
                SteamAccount::withTrashed()->forceDelete();
                
                DB::commit();
                
                $this->info("✓ Đã xóa {$countAccounts} tài khoản và {$countPivot} liên kết.");
                
                // Reset ID nếu có option --reset-ids
                if ($this->option('reset-ids')) {
                    $this->info('Đang reset ID về 1...');
                    try {
                        // Reset AUTO_INCREMENT cho steam_account_games (phải reset trước vì có foreign key)
                        DB::statement('ALTER TABLE steam_account_games AUTO_INCREMENT = 1');
                        
                        // Reset AUTO_INCREMENT cho steam_accounts
                        DB::statement('ALTER TABLE steam_accounts AUTO_INCREMENT = 1');
                        
                        $this->info('✓ Đã reset ID về 1.');
                    } catch (\Exception $e) {
                        $this->warn("Cảnh báo: Không thể reset ID - " . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Lỗi khi xóa dữ liệu: " . $e->getMessage());
                return Command::FAILURE;
            }
        }

        $this->info('Bắt đầu tạo tài khoản Steam...');

        // Lấy tất cả game
        $games = ProductSimple::all();
        
        if ($games->isEmpty()) {
            $this->error('Không tìm thấy game nào trong database!');
            return Command::FAILURE;
        }

        $this->info("Tìm thấy {$games->count()} game. Bắt đầu tạo tài khoản...");

        // Đếm số tài khoản hiện có để tạo username tăng dần
        // Nếu đã xóa dữ liệu cũ, bắt đầu từ 1
        $accountCounter = $this->option('fresh') ? 1 : (SteamAccount::withTrashed()->count() + 1);

        $totalCreated = 0;
        $totalLinked = 0;

        DB::beginTransaction();
        try {
            foreach ($games as $game) {
                $this->info("Đang xử lý game: {$game->title} (ID: {$game->id})");

                // Tạo 3 tài khoản cho mỗi game
                for ($i = 1; $i <= 3; $i++) {
                    // Tạo username: gametech01, gametech02, ...
                    $username = 'gametech' . str_pad($accountCounter, 2, '0', STR_PAD_LEFT);
                    
                    // Mật khẩu Steam
                    $steamPassword = 'GameTech123@';
                    
                    // Email và mật khẩu email (chỉ cho game type "online")
                    $email = null;
                    $emailPassword = null;
                    
                    if ($game->type === 'online') {
                        $email = $username . '@gmail.com';
                        $emailPassword = 'Gametech123@';
                    }

                    // Kiểm tra xem username đã tồn tại chưa
                    $existingAccount = SteamAccount::withTrashed()
                        ->where('username', $username)
                        ->first();

                    if ($existingAccount) {
                        $this->warn("  Tài khoản {$username} đã tồn tại, bỏ qua...");
                        $accountCounter++;
                        continue;
                    }

                    // Tạo tài khoản Steam
                    $steamAccount = new SteamAccount();
                    $steamAccount->username = $username;
                    $steamAccount->password = $steamPassword; // Model sẽ tự động mã hóa
                    $steamAccount->email = $email; // null cho game offline
                    $steamAccount->email_password = $emailPassword; // null cho game offline, Model sẽ tự động xử lý
                    $steamAccount->status = 'available';
                    $steamAccount->save();

                    $totalCreated++;

                    // Liên kết tài khoản với game qua pivot table
                    // Vì mỗi tài khoản chỉ chứa 1 game, nên game đó luôn được highlight
                    $isHighlighted = true;
                    
                    DB::table('steam_account_games')->insert([
                        'steam_account_id' => $steamAccount->id,
                        'product_simple_id' => $game->id,
                        'is_highlighted' => $isHighlighted,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $totalLinked++;
                    $this->line("  ✓ Đã tạo tài khoản: {$username}" . ($email ? " (Email: {$email})" : ""));

                    $accountCounter++;
                }
            }

            DB::commit();
            
            $this->newLine();
            $this->info("✓ Hoàn thành!");
            $this->info("  - Đã tạo: {$totalCreated} tài khoản Steam");
            $this->info("  - Đã liên kết: {$totalLinked} tài khoản với game");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Lỗi: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
