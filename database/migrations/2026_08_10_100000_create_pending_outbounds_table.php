<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_outbounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer')->onDelete('cascade');
            $table->string('purc_doc')->nullable();
            $table->json('sales_docs')->nullable();
            $table->integer('qty_item')->default(0);
            $table->integer('qty')->default(0);
            $table->dateTime('delivery_date')->nullable();
            $table->string('delivery_note_number')->nullable();
            $table->string('ntt_dn')->nullable();
            $table->string('deliv_loc')->nullable();
            $table->string('deliv_dest')->nullable();
            $table->integer('koli')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'converted', 'cancelled'])->default('pending');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('converted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('converted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_outbounds');
    }
};
