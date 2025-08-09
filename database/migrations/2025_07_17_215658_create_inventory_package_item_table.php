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
        Schema::create('inventory_package_item', function (Blueprint $table) {
            $table->id();
            $table->integer('inventory_package_id');
            $table->integer('product_id');
            $table->integer('purchase_order_detail_id');
            $table->boolean('is_parent')->default(false);
            $table->boolean('direct_outbound')->default(false);
            $table->integer('qty')->default(0);
            $table->integer('inventory_item_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_package_item');
    }
};
