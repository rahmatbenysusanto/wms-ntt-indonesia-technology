<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packing_list_details', function (Blueprint $table) {
            $table->json('serial_numbers')->nullable()->after('measurement');
        });
    }

    public function down(): void
    {
        Schema::table('packing_list_details', function (Blueprint $table) {
            $table->dropColumn('serial_numbers');
        });
    }
};
