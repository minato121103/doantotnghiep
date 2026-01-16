<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BuyerUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('12345678');
        $users = [];
        $now = now();

        for ($i = 1; $i <= 100; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $users[] = [
                'name' => 'User ' . $num,
                'email' => 'user' . $num . '@gmail.com',
                'password' => $password,
                'role' => 'buyer',
                'status' => 'active',
                'balance' => rand(50000, 500000),
                'total_orders' => 0,
                'total_spent' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert theo batch
        foreach (array_chunk($users, 50) as $chunk) {
            DB::table('users')->insert($chunk);
        }

        $this->command->info('✅ Đã tạo 100 users với role buyer thành công!');
        $this->command->info('📧 Email: user01@gmail.com → user100@gmail.com');
        $this->command->info('🔑 Mật khẩu: 12345678');
    }
}
