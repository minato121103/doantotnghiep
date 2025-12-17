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
        Schema::table('product_simple', function (Blueprint $table) {
            $table->string('image', 255)->nullable();
            $table->string('title', 255)->nullable();
            $table->string('price', 100)->nullable();
            $table->text('short_description')->nullable();
            $table->text('detail_description')->nullable();
            $table->string('category', 255)->nullable();
            $table->text('tags')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('rating_count')->default(0);
            $table->decimal('average_rating', 3, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_simple', function (Blueprint $table) {
            //
        });
    }
};
