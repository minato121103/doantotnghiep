<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bảng lưu các rules đã học từ câu hỏi người dùng
        Schema::create('chatbot_learned_rules', function (Blueprint $table) {
            $table->id();
            $table->string('pattern', 500)->comment('Regex pattern để match câu hỏi');
            $table->json('keywords')->nullable()->comment('Các từ khóa trích xuất');
            $table->string('intent', 100)->comment('Loại intent: product_search, price_query, order_status...');
            $table->text('response_template')->comment('Template câu trả lời');
            $table->enum('response_type', ['static', 'dynamic'])->default('static')->comment('static = trả lời cố định, dynamic = cần query DB');
            $table->decimal('confidence_score', 3, 2)->default(0.50)->comment('Độ tin cậy 0.00 - 1.00');
            $table->unsignedInteger('usage_count')->default(0)->comment('Số lần được sử dụng');
            $table->unsignedInteger('positive_feedback')->default(0)->comment('Số feedback tích cực');
            $table->unsignedInteger('negative_feedback')->default(0)->comment('Số feedback tiêu cực');
            $table->boolean('is_active')->default(true)->comment('Có đang active không');
            $table->timestamps();
            
            $table->index('intent');
            $table->index('is_active');
            $table->index('confidence_score');
        });

        // Bảng lưu lịch sử hội thoại để phân tích và học
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('NULL nếu là khách');
            $table->string('session_id', 100)->nullable()->comment('Session ID cho khách');
            $table->text('question')->comment('Câu hỏi người dùng');
            $table->text('answer')->comment('Câu trả lời chatbot');
            $table->enum('source', ['learned', 'rule', 'gemini'])->comment('Nguồn trả lời');
            $table->foreignId('learned_rule_id')->nullable()->constrained('chatbot_learned_rules')->onDelete('set null');
            $table->enum('feedback', ['good', 'bad'])->nullable()->comment('User feedback');
            $table->json('extracted_intents')->nullable()->comment('Các intents đã trích xuất');
            $table->json('extracted_products')->nullable()->comment('Các products đã tìm thấy');
            $table->unsignedInteger('response_time_ms')->nullable()->comment('Thời gian response');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('session_id');
            $table->index('source');
            $table->index('feedback');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_conversations');
        Schema::dropIfExists('chatbot_learned_rules');
    }
};
