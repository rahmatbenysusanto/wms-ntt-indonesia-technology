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
        Schema::create('transfer_location', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->integer('inventory_parent_id');
            $table->string('purc_doc');
            $table->integer('old_location');
            $table->integer('new_location');
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_location');
    }
};
