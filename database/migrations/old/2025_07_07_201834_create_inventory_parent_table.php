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
        Schema::create('inventory_parent', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('purchase_order_id');
            $table->integer('storage_id');
            $table->string('pa_number')->nullable();
            $table->string('pa_reff_number')->nullable();
            $table->integer('stock')->default(0);
            $table->text('sales_docs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_parent');
    }
};
