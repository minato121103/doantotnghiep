<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Bảng lưu recommendations cho users
        Schema::create('user_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('product_simple')->onDelete('cascade');
            $table->decimal('score', 8, 4)->comment('Điểm số recommendation (0-1)');
            $table->string('algorithm', 50)->comment('Thuật toán: collaborative, content, hybrid, popular');
            $table->integer('rank')->comment('Thứ hạng trong danh sách recommendations');
            $table->timestamps();
            
            $table->index(['user_id', 'rank']);
            $table->index(['user_id', 'score']);
            $table->unique(['user_id', 'product_id', 'algorithm']);
        });

        // Bảng lưu similar products
        Schema::create('product_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('product_simple')->onDelete('cascade');
            $table->foreignId('similar_product_id')->constrained('product_simple')->onDelete('cascade');
            $table->decimal('similarity_score', 8, 4)->comment('Độ tương đồng (0-1)');
            $table->string('algorithm', 50)->default('content');
            $table->integer('rank');
            $table->timestamps();
            
            $table->index(['product_id', 'rank']);
            $table->index(['product_id', 'similarity_score']);
            $table->unique(['product_id', 'similar_product_id']);
        });

        // Bảng log training history
        Schema::create('recommendation_training_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['running', 'success', 'failed'])->default('running');
            $table->integer('users_processed')->default(0);
            $table->integer('products_processed')->default(0);
            $table->integer('recommendations_created')->default(0);
            $table->decimal('duration_seconds', 10, 2)->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('created_at');
        });

        // Bảng lưu user interactions (để thu thập dữ liệu)
        Schema::create('user_product_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('product_simple')->onDelete('cascade');
            $table->enum('interaction_type', ['view', 'cart_add', 'purchase', 'review', 'wishlist']);
            $table->decimal('interaction_value', 5, 2)->default(1)->comment('Giá trị tương tác: view=1, cart=2, purchase=5, review=3');
            $table->timestamps();
            
            $table->index(['user_id', 'interaction_type']);
            $table->index(['product_id', 'interaction_type']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_product_interactions');
        Schema::dropIfExists('recommendation_training_logs');
        Schema::dropIfExists('product_recommendations');
        Schema::dropIfExists('user_recommendations');
    }
};
