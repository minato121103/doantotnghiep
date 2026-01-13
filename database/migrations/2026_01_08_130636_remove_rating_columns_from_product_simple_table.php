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
            if (Schema::hasColumn('product_simple', 'rating_count')) {
                $table->dropColumn('rating_count');
            }
            if (Schema::hasColumn('product_simple', 'average_rating')) {
                $table->dropColumn('average_rating');
            }
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
            $table->integer('rating_count')->default(0)->after('view_count');
            $table->decimal('average_rating', 3, 2)->nullable()->after('rating_count');
        });
    }
};
