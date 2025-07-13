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
        Schema::create('transfer_location_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('transfer_location_id');
            $table->enum('type', ['parent', 'child']);
            $table->integer('inventory_parent_detail_id')->nullable();
            $table->integer('inventory_child_detail_id')->nullable();
            $table->integer('qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_location_detail');
    }
};
