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
        Schema::table('product_simple', function (Blueprint $table) {
            // Add id column as primary key if it doesn't exist
            if (!Schema::hasColumn('product_simple', 'id')) {
                $table->id()->first();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_simple', function (Blueprint $table) {
            // Remove id column if it exists
            if (Schema::hasColumn('product_simple', 'id')) {
                $table->dropColumn('id');
            }
        });
    }
};
