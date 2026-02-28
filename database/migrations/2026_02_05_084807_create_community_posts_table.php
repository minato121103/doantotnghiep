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
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->json('images')->nullable(); // Array of image URLs
            $table->json('videos')->nullable(); // Array of video URLs
            $table->string('game_preference')->nullable(); // Game they want to play
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->enum('privacy', ['public', 'friends', 'private'])->default('public');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
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
        Schema::dropIfExists('community_posts');
    }
};
