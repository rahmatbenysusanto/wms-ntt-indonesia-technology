<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packing_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customer')->onDelete('set null');
            $table->json('sales_docs')->nullable();
            $table->integer('qty_item')->default(0);
            $table->integer('qty')->default(0);
            $table->dateTime('delivery_date')->nullable();
            $table->string('deliv_loc')->nullable();
            $table->string('deliv_dest')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['draft', 'released', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('released_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packing_lists');
    }
};