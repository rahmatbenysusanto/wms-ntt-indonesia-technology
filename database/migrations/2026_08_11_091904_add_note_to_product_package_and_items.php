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
        // Add note to product_package (QC Master note)
        if (!Schema::hasColumn('product_package', 'note')) {
            Schema::table('product_package', function (Blueprint $table) {
                $table->text('note')->nullable()->after('status');
            });
        }

        // Add note to product_package_item (per-product note)
        if (!Schema::hasColumn('product_package_item', 'note')) {
            Schema::table('product_package_item', function (Blueprint $table) {
                $table->text('note')->nullable()->after('qty');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('product_package', 'note')) {
            Schema::table('product_package', function (Blueprint $table) {
                $table->dropColumn('note');
            });
        }

        if (Schema::hasColumn('product_package_item', 'note')) {
            Schema::table('product_package_item', function (Blueprint $table) {
                $table->dropColumn('note');
            });
        }
    }
};
