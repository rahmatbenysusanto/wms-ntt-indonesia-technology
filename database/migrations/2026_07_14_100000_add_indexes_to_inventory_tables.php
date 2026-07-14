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
        // ========== inventory_package ==========
        Schema::table('inventory_package', function (Blueprint $table) {
            $table->index('storage_id', 'idx_inv_pkg_storage_id');
            $table->index('qty', 'idx_inv_pkg_qty');
            $table->index('created_at', 'idx_inv_pkg_created_at');
            $table->index('number', 'idx_inv_pkg_number');
            $table->index('purchase_order_id', 'idx_inv_pkg_purchase_order_id');
        });

        // ========== inventory_package_item ==========
        Schema::table('inventory_package_item', function (Blueprint $table) {
            $table->index('inventory_package_id', 'idx_inv_pkg_item_package_id');
            $table->index('purchase_order_detail_id', 'idx_inv_pkg_item_po_detail_id');
            $table->index('product_id', 'idx_inv_pkg_item_product_id');
        });

        // ========== inventory_package_item_sn ==========
        Schema::table('inventory_package_item_sn', function (Blueprint $table) {
            $table->index('inventory_package_item_id', 'idx_inv_pkg_item_sn_item_id');
            $table->index('serial_number', 'idx_inv_pkg_item_sn_serial');
        });

        // ========== purchase_order ==========
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->index('purc_doc', 'idx_po_purc_doc');
            $table->index('customer_id', 'idx_po_customer_id');
        });

        // ========== purchase_order_detail ==========
        Schema::table('purchase_order_detail', function (Blueprint $table) {
            $table->index('purchase_order_id', 'idx_po_detail_po_id');
            $table->index('material', 'idx_po_detail_material');
            $table->index('sales_doc', 'idx_po_detail_sales_doc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_package', function (Blueprint $table) {
            $table->dropIndex('idx_inv_pkg_storage_id');
            $table->dropIndex('idx_inv_pkg_qty');
            $table->dropIndex('idx_inv_pkg_created_at');
            $table->dropIndex('idx_inv_pkg_number');
            $table->dropIndex('idx_inv_pkg_purchase_order_id');
        });

        Schema::table('inventory_package_item', function (Blueprint $table) {
            $table->dropIndex('idx_inv_pkg_item_package_id');
            $table->dropIndex('idx_inv_pkg_item_po_detail_id');
            $table->dropIndex('idx_inv_pkg_item_product_id');
        });

        Schema::table('inventory_package_item_sn', function (Blueprint $table) {
            $table->dropIndex('idx_inv_pkg_item_sn_item_id');
            $table->dropIndex('idx_inv_pkg_item_sn_serial');
        });

        Schema::table('purchase_order', function (Blueprint $table) {
            $table->dropIndex('idx_po_purc_doc');
            $table->dropIndex('idx_po_customer_id');
        });

        Schema::table('purchase_order_detail', function (Blueprint $table) {
            $table->dropIndex('idx_po_detail_po_id');
            $table->dropIndex('idx_po_detail_material');
            $table->dropIndex('idx_po_detail_sales_doc');
        });
    }
};
