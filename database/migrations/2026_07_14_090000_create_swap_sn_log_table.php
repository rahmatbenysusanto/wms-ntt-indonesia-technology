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
        Schema::create('swap_sn_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_package_item_sn_id')->nullable();
            $table->unsignedBigInteger('inventory_package_id')->nullable();
            $table->unsignedBigInteger('inventory_package_item_id')->nullable();
            $table->string('old_serial_number');
            $table->string('new_serial_number');
            $table->string('reference_serial_number')->nullable();
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();

            $table->foreign('inventory_package_id')
                ->references('id')->on('inventory_package')
                ->onDelete('set null');
            $table->foreign('inventory_package_item_id')
                ->references('id')->on('inventory_package_item')
                ->onDelete('set null');
            $table->foreign('inventory_package_item_sn_id')
                ->references('id')->on('inventory_package_item_sn')
                ->onDelete('set null');
            $table->foreign('changed_by')
                ->references('id')->on('users')
                ->onDelete('set null');

            $table->index('old_serial_number');
            $table->index('new_serial_number');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('swap_sn_log');
    }
};
