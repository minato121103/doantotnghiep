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
        Schema::table('product_simple', function (Blueprint $table) {
            $table->string('type', 50)->nullable()->after('category');
        });

        // Cập nhật dữ liệu hiện có dựa trên short_description
        // Nếu short_description chứa "offline" → type = "offline"
        DB::table('product_simple')
            ->whereNotNull('short_description')
            ->whereRaw('LOWER(short_description) LIKE ?', ['%offline%'])
            ->update(['type' => 'offline']);

        // Nếu short_description chứa "Giao tài khoản Steam + Mail" → type = "online"
        // (chỉ cập nhật những record không có "offline" trong short_description)
        DB::table('product_simple')
            ->whereNotNull('short_description')
            ->whereRaw('short_description LIKE ?', ['%Giao tài khoản Steam + Mail%'])
            ->whereRaw('LOWER(short_description) NOT LIKE ?', ['%offline%'])
            ->update(['type' => 'online']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_simple', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
