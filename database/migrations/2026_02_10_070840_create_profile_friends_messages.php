<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add bio and cover_image to users
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('avatar');
            $table->string('cover_image')->nullable()->after('bio');
        });

        // Friendships table
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'blocked'])->default('pending');
            $table->timestamps();

            $table->unique(['sender_id', 'receiver_id']);
            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('status');
        });

        // Messages table
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['sender_id', 'receiver_id']);
            $table->index('receiver_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('friendships');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bio', 'cover_image']);
        });
    }
};
