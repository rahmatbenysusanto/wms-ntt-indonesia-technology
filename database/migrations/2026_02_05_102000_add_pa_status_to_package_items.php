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
        Schema::table('product_package_item', function (Blueprint $table) {
            $table->integer('qty_pa')->default(0)->nullable();
        });

        Schema::table('product_package_item_sn', function (Blueprint $table) {
            $table->integer('status')->default(0)->nullable()->comment('0: Open, 1: Done');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_package_item', function (Blueprint $table) {
            $table->dropColumn('qty_pa');
        });

        Schema::table('product_package_item_sn', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
