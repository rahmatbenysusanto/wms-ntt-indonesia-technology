<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packing_list_details', function (Blueprint $table) {
            $table->string('part_number')->nullable()->after('po_item_desc');
            $table->string('hs_code')->nullable()->after('part_number');
            $table->decimal('gross_weight', 12, 2)->nullable()->after('qty');
            $table->string('measurement')->nullable()->after('gross_weight');
        });
    }

    public function down(): void
    {
        Schema::table('packing_list_details', function (Blueprint $table) {
            $table->dropColumn(['part_number', 'hs_code', 'gross_weight', 'measurement']);
        });
    }
};
