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
        Schema::create('spare_room', function (Blueprint $table) {
            $table->id();
            $table->integer('outbound_id');
            $table->timestamp('outbound_date');
            $table->string('number');
            $table->integer('qty_item')->default(0);
            $table->integer('qty')->default(0);
            $table->string('status');
            $table->string('deliv_dest')->nullable();
            $table->string('type');
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_room');
    }
};
