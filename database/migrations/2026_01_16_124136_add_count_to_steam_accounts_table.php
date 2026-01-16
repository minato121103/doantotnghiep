<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add count column
        Schema::table('steam_accounts', function (Blueprint $table) {
            $table->integer('count')->default(1)->after('email_password');
        });

        // Update existing accounts:
        // - Accounts without email/email_password (offline) get count = 10
        // - Accounts with email/email_password get count = 1
        // - Sold accounts get count = 0
        DB::table('steam_accounts')
            ->where(function($query) {
                $query->whereNull('email')
                      ->orWhere('email', '')
                      ->orWhereNull('email_password')
                      ->orWhere('email_password', '');
            })
            ->where('status', 'available')
            ->update(['count' => 10]);

        DB::table('steam_accounts')
            ->where('status', 'sold')
            ->update(['count' => 0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('steam_accounts', function (Blueprint $table) {
            $table->dropColumn('count');
        });
    }
};
