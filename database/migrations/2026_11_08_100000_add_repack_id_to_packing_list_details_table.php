<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packing_list_details', function (Blueprint $table) {
            $table->foreignId('packing_list_repack_id')->nullable()->after('packing_list_id')
                ->constrained('packing_list_repacks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('packing_list_details', function (Blueprint $table) {
            $table->dropForeign(['packing_list_repack_id']);
            $table->dropColumn('packing_list_repack_id');
        });
    }
};
