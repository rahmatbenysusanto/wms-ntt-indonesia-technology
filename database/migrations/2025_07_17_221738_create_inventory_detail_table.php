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
        Schema::create('inventory_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('inventory_id');
            $table->integer('purchase_order_detail_id');
            $table->integer('storage_id');
            $table->integer('inventory_package_item_id');
            $table->string('sales_doc');
            $table->integer('qty')->default('0');
            $table->timestamp('aging_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_detail');
    }
};
