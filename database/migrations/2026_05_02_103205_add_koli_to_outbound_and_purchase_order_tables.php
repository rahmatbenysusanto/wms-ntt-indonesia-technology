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
        Schema::table('outbound', function (Blueprint $table) {
            $table->integer('koli')->nullable()->after('ntt_dn');
        });

        Schema::table('purchase_order', function (Blueprint $table) {
            $table->integer('koli')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outbound', function (Blueprint $table) {
            $table->dropColumn('koli');
        });

        Schema::table('purchase_order', function (Blueprint $table) {
            $table->dropColumn('koli');
        });
    }
};
