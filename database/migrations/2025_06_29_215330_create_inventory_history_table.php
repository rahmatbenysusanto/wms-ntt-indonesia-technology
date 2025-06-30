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
        Schema::create('inventory_history', function (Blueprint $table) {
            $table->id();
            $table->integer('inventory_id');
            $table->integer('inventory_detail_id');
            $table->integer('quality_control_id')->nullable();
            $table->integer('quality_control_detail_id')->nullable();
            $table->integer('quality_control_item_id')->nullable();
            $table->integer('outbound_id')->nullable();
            $table->integer('outbound_detail_id')->nullable();
            $table->enum('type', ['inbound', 'outbound']);
            $table->integer('qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_history');
    }
};
