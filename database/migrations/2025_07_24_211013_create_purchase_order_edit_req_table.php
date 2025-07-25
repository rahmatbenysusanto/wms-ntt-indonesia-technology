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
        Schema::create('purchase_order_edit_req', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_order_id');
            $table->integer('request_by');
            $table->string('type');
            $table->text('note')->nullable();
            $table->string('status');
            $table->json('details');
            $table->integer('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_edit_req');
    }
};
