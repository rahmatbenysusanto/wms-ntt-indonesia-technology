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
        Schema::create('serial_number', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_order_id');
            $table->integer('purchase_order_detail_id');
            $table->integer('product_id')->nullable();
            $table->integer('product_parent_id')->nullable();
            $table->integer('product_parent_detail_id')->nullable();
            $table->integer('product_child_id')->nullable();
            $table->integer('product_child_detail_id')->nullable();
            $table->integer('inventory_parent_id')->nullable();
            $table->integer('inventory_parent_detail_id')->nullable();
            $table->integer('inventory_child_id')->nullable();
            $table->integer('inventory_child_detail_id')->nullable();
            $table->string('serial_number');
            $table->integer('qty')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serial_number');
    }
};
