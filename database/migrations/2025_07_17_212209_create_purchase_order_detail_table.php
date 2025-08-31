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
        Schema::create('purchase_order_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_order_id');
            $table->integer('product_id');
            $table->string('status');
            $table->integer('qty_qc')->default(0);
            $table->string('sales_doc');
            $table->integer('item')->nullable();
            $table->string('material')->nullable();
            $table->string('po_item_desc')->nullable();
            $table->string('prod_hierarchy_desc')->nullable();
            $table->string('acc_ass_cat')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('customer_name')->nullable();
            $table->integer('stor_loc')->nullable();
            $table->string('sloc_desc')->nullable();
            $table->string('valuation')->nullable();
            $table->integer('po_item_qty')->nullable();
            $table->decimal('net_order_price', 12, 2)->default(0);
            $table->string('currency')->nullable();
            $table->integer('price_idr')->default(0);
            $table->timestamp('price_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_detail');
    }
};
