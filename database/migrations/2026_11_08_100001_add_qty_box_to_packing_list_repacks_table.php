<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packing_list_repacks', function (Blueprint $table) {
            $table->unsignedInteger('qty_box')->default(1)->after('koli_no');
        });
    }

    public function down(): void
    {
        Schema::table('packing_list_repacks', function (Blueprint $table) {
            $table->dropColumn('qty_box');
        });
    }
};
