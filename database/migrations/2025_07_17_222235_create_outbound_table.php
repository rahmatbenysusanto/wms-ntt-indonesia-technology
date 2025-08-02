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
            $table->integer('customer_id');
            $table->string('purc_doc');
            $table->json('sales_docs');
            $table->timestamp('outbound_date');
            $table->string('number');
            $table->integer('qty_item')->default('0');
            $table->integer('qty')->default('0');
            $table->string('type');
            $table->string('status');
            $table->string('deliv_loc');
            $table->string('deliv_dest');
            $table->text('note')->nullable();
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
