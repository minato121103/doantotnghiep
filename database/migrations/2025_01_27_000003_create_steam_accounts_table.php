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
        Schema::create('steam_accounts', function (Blueprint $table) {
            $table->id();
            
            // Thông tin đăng nhập tài khoản Steam
            $table->string('username', 100);
            $table->text('password'); // Mật khẩu sẽ được mã hóa
            $table->string('email', 255);
            $table->text('email_password'); // Mật khẩu Gmail sẽ được mã hóa
            
            // Trạng thái tài khoản
            $table->enum('status', ['available', 'sold', 'pending', 'suspended'])->default('available');
            
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('steam_accounts');
    }
};

