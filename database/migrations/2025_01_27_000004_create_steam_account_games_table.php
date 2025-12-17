<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('steam_account_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('steam_account_id')->constrained('steam_accounts')->onDelete('cascade');
            $table->foreignId('product_simple_id')->constrained('product_simple')->onDelete('cascade');
            $table->boolean('is_highlighted')->default(false);
            $table->timestamps();
            
            $table->unique(['steam_account_id', 'product_simple_id']);
            $table->index('product_simple_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('steam_account_games');
    }
};

