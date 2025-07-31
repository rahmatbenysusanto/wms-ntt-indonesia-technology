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
        Schema::create('spare_room_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('spare_room_id');
            $table->integer('product_id');
            $table->integer('outbound_detail_id');
            $table->integer('qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_room_detail');
    }
};
