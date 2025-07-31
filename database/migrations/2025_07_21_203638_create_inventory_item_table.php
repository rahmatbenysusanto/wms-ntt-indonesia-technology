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
        Schema::create('inventory_item', function (Blueprint $table) {
            $table->id();
            $table->string('purc_doc');
            $table->string('sales_doc');
            $table->integer('product_id');
            $table->integer('stock')->default(0);
            $table->integer('storage_id');
            $table->enum('type', ['inv', 'gr', 'pm', 'spare'])->default('inv');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_item');
    }
};
