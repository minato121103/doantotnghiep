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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->enum('role', ['admin', 'buyer', 'editor'])->default('buyer')->after('avatar');
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active')->after('role');
            $table->decimal('balance', 15, 2)->default(0)->after('status');
            $table->integer('total_orders')->default(0)->after('balance');
            $table->integer('total_spent')->default(0)->after('total_orders');
            $table->string('address')->nullable()->after('total_spent');
            $table->date('birthday')->nullable()->after('address');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birthday');
        });
        
        // Thêm soft deletes nếu chưa có
        if (!Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'avatar',
                'role',
                'status',
                'balance',
                'total_orders',
                'total_spent',
                'address',
                'birthday',
                'gender',
                'deleted_at'
            ]);
        });
    }
};

