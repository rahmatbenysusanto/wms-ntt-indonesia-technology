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
        Schema::create('quality_control_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('quality_control_id');
            $table->integer('purchase_order_detail_id');
            $table->integer('qty')->default(0);
            $table->string('status');
            $table->integer('storage_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_control_detail');
    }
};
