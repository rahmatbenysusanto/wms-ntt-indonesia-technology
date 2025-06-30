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
            $table->string('serial_number');
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
