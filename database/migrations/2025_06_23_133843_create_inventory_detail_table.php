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
            $table->integer('quality_control_detail_id');
            $table->enum('type', ['parent', 'child']);
            $table->string('purc_doc');
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
        Schema::dropIfExists('inventory_detail');
    }
};
