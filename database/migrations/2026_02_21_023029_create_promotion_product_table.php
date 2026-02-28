<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('promotion_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('product_simple_id');
            $table->foreign('product_simple_id')->references('id')->on('product_simple')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['promotion_id', 'product_simple_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('promotion_product');
    }
};
