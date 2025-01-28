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
        // price is now represented as a float with default 0
        Schema::table('products', function (Blueprint $table) {
            $table->float('price', 8, 2)->default(0)->change();
        });

        // default value for stock quantity and unique product id
        Schema::table('stock', function (Blueprint $table) {
            $table->integer('stock_quantity')->default(0)->change();
            $table->unique(['product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('price')->change();
        });

        Schema::table('stock', function (Blueprint $table) {
            $table->integer('stock_quantity')->change();
            $table->dropUnique(['product_id']);
        });
    }
};
