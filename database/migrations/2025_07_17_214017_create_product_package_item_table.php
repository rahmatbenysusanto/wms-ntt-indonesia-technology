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
        Schema::create('product_package_item', function (Blueprint $table) {
            $table->id();
            $table->integer('product_package_id');
            $table->integer('product_id');
            $table->integer('purchase_order_detail_id');
            $table->boolean('is_parent')->default(false);
            $table->boolean('direct_outbound')->default(false);
            $table->integer('qty')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_package_item');
    }
};
