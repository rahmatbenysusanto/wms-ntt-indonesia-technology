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
        Schema::create('outbound', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->string('purc_doc');
            $table->string('sales_doc');
            $table->string('client');
            $table->integer('customer_id')->nullable();
            $table->string('deliv_loc');
            $table->string('deliv_dest');
            $table->integer('qty_item')->default(0);
            $table->timestamp('delivery_date');
            $table->string('delivery_note_number')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound');
    }
};
