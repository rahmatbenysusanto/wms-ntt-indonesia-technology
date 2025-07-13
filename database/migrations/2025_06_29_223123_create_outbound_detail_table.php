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
        Schema::create('outbound_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('outbound_id');
            $table->integer('product_id');
            $table->integer('inventory_parent_id')->nullable();
            $table->integer('inventory_parent_detail_id')->nullable();
            $table->integer('inventory_child_id')->nullable();
            $table->integer('inventory_child_detail_id')->nullable();
            $table->integer('qty');
            $table->json('serial_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound_detail');
    }
};
