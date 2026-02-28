<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add parent_id and likes_count to comments
        Schema::table('community_post_comments', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('user_id');
            $table->integer('likes_count')->default(0)->after('content');
            
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('community_post_comments')
                  ->onDelete('cascade');
        });

        // Comment likes table
        Schema::create('community_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('community_post_comments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['comment_id', 'user_id']);
            $table->index('comment_id');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('community_comment_likes');
        
        Schema::table('community_post_comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'likes_count']);
        });
    }
};
