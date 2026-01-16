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
        // Convert existing timestamp values to text format before changing column type
        DB::statement('UPDATE news SET published_at = DATE_FORMAT(published_at, "%Y-%m-%d %H:%i:%s") WHERE published_at IS NOT NULL');
        
        Schema::table('news', function (Blueprint $table) {
            $table->string('published_at', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('news', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->change();
        });
    }
};
