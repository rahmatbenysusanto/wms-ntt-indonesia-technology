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
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();
            $table->string('purc_doc');
            $table->integer('customer_id');
            $table->integer('vendor_id');
            $table->integer('sales_docs_qty')->default(0);
            $table->integer('material_qty')->default(0);
            $table->integer('items_qty')->default(0);
            $table->string('status');
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order');
    }
};
