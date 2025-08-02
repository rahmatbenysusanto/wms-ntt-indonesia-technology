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
        Schema::create('inventory_package', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_order_id');
            $table->integer('storage_id');
            $table->string('number');
            $table->string('reff_number')->nullable();
            $table->integer('qty_item')->default(0);
            $table->integer('qty')->default(0);
            $table->json('sales_docs')->nullable();
            $table->integer('product_package_id')->nullable();
            $table->text('note')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_package');
    }
};
