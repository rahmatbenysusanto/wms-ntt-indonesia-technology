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
        Schema::create('product_child_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('product_child_id');
            $table->integer('product_id');
            $table->integer('purchase_order_detail_id');
            $table->string('sales_doc');
            $table->integer('qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_child_detail');
    }
};
