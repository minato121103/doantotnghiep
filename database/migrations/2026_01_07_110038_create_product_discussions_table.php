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
        Schema::create('product_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_simple_id')->constrained('product_simple')->onDelete('cascade'); // Sản phẩm/game
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // User đăng nhập (null nếu ẩn danh)
            $table->string('author_name')->nullable(); // Tên hiển thị (nếu không đăng nhập)
            $table->text('content'); // Nội dung bình luận
            $table->foreignId('parent_id')->nullable()->constrained('product_discussions')->onDelete('cascade'); // Reply comment (null = comment gốc)
            $table->integer('like_count')->default(0); // Số lượt thích
            $table->enum('status', ['approved', 'pending', 'spam', 'deleted'])->default('approved'); // Trạng thái
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes để tối ưu query
            $table->index(['product_simple_id', 'status']);
            $table->index(['parent_id', 'status']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_discussions');
    }
};
