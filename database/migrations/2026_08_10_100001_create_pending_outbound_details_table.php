<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_outbound_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pending_outbound_id')->constrained('pending_outbounds')->onDelete('cascade');
            $table->foreignId('purchase_order_detail_id')->nullable()->constrained('purchase_order_detail')->onDelete('set null');
            $table->foreignId('inventory_package_item_id')->nullable()->constrained('inventory_package_item')->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained('product')->onDelete('set null');
            $table->string('sales_doc')->nullable();
            $table->string('material')->nullable();
            $table->string('item')->nullable();
            $table->text('po_item_desc')->nullable();
            $table->integer('qty')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_outbound_details');
    }
};
