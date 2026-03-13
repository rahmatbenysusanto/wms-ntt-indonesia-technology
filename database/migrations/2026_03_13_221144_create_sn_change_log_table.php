<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sn_change_log', function (Blueprint $table) {
            $table->id();
            // Referensi ke inventory_package_item_sn yang diubah
            $table->unsignedBigInteger('inventory_package_item_sn_id')->nullable();
            // Referensi ke box (inventory_package)
            $table->unsignedBigInteger('inventory_package_id')->nullable();
            // Referensi ke item di dalam box
            $table->unsignedBigInteger('inventory_package_item_id')->nullable();
            // SN lama dan baru
            $table->string('old_serial_number')->nullable();
            $table->string('new_serial_number')->nullable();
            // Catatan opsional
            $table->text('notes')->nullable();
            // User yang melakukan perubahan
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();

            $table->foreign('inventory_package_id')->references('id')->on('inventory_package')->onDelete('set null');
            $table->foreign('inventory_package_item_id')->references('id')->on('inventory_package_item')->onDelete('set null');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sn_change_log');
    }
};
