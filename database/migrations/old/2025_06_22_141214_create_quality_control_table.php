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
        Schema::create('quality_control', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->integer('purchase_order_id');
            $table->string('sales_doc')->nullable();
            $table->integer('qty_parent')->default(0);
            $table->string('status');
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_control');
    }
};
