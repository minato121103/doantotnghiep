<?php

namespace Database\Seeders;

use App\Models\ProductSimple;
use App\Models\SteamAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OnlineGameAccountsSeeder extends Seeder
{
    public const ACCOUNTS_PER_GAME = 30;
    public const USERNAME_PREFIX = 'gametechonl';
    public const PASSWORD = 'gametech123@';
    public const EMAIL_PASSWORD = 'gametech123';

    /**
     * Tạo 30 tài khoản Steam cho mỗi game có type = "online".
     * Username: gametechonl01, gametechonl02, ... (tăng dần toàn cục)
     * Email: gametechonl01@gmail.com, gametechonl02@gmail.com, ...
     */
    public function run(): void
    {
        $onlineGames = ProductSimple::where('type', 'online')->orderBy('id')->get();
        if ($onlineGames->isEmpty()) {
            $this->command->warn('Không có game nào có type = "online".');
            return;
        }

        // Bắt đầu từ số tiếp theo sau username lớn nhất dạng gametechonl*
        $startNum = $this->getNextAccountNumber();

        $this->command->info("Tìm thấy {$onlineGames->count()} game online. Bắt đầu từ " . self::USERNAME_PREFIX . str_pad($startNum, 2, '0', STR_PAD_LEFT) . '.');

        $currentNum = $startNum;
        $created = 0;

        foreach ($onlineGames as $game) {
            for ($i = 0; $i < self::ACCOUNTS_PER_GAME; $i++) {
                $numStr = str_pad($currentNum, 2, '0', STR_PAD_LEFT);
                $username = self::USERNAME_PREFIX . $numStr;
                $email = self::USERNAME_PREFIX . $numStr . '@gmail.com';

                $account = SteamAccount::create([
                    'username' => $username,
                    'password' => self::PASSWORD,
                    'email' => $email,
                    'email_password' => self::EMAIL_PASSWORD,
                    'count' => 1,
                    'status' => 'available',
                ]);

                $account->games()->attach($game->id, ['is_highlighted' => false]);
                $created++;
                $currentNum++;
            }
            $this->command->info("  → Game #{$game->id} ({$game->title}): đã tạo " . self::ACCOUNTS_PER_GAME . " tài khoản.");
        }

        $this->command->info("Hoàn thành. Tổng cộng đã tạo {$created} tài khoản cho {$onlineGames->count()} game online.");
    }

    /**
     * Lấy số thứ tự tiếp theo để tránh trùng username (gametechonl01, 02, ...).
     */
    private function getNextAccountNumber(): int
    {
        $prefixLen = strlen(self::USERNAME_PREFIX);
        $max = DB::table('steam_accounts')
            ->where('username', 'like', self::USERNAME_PREFIX . '%')
            ->pluck('username')
            ->map(function ($username) use ($prefixLen) {
                $suffix = substr($username, $prefixLen);
                return is_numeric($suffix) ? (int) $suffix : 0;
            })
            ->max();

        return ($max ?? 0) + 1;
    }
}
