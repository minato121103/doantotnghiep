<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update all existing orders to completed status
        DB::table('orders')
            ->whereIn('status', ['pending', 'processing'])
            ->update([
                'status' => 'completed',
                'completed_at' => DB::raw('COALESCE(completed_at, NOW())')
            ]);
        
        // Also update steam accounts that are in 'pending' status to 'sold'
        DB::table('steam_accounts')
            ->where('status', 'pending')
            ->update([
                'status' => 'sold',
                'sold_at' => DB::raw('COALESCE(sold_at, NOW())')
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Cannot reverse this migration as we don't know original statuses
    }
};
