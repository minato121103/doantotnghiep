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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_simple_id')->constrained('product_simple')->onDelete('cascade'); // Game được đánh giá
            $table->integer('rating')->unsigned(); // 1-5 sao
            $table->text('comment')->nullable();
            $table->json('images')->nullable(); // Ảnh đính kèm
            $table->boolean('is_verified_purchase')->default(true);
            $table->timestamps();
            
            $table->unique('order_id'); // Mỗi đơn hàng chỉ được đánh giá 1 lần
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};

