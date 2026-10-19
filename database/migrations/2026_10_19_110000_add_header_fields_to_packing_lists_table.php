<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packing_lists', function (Blueprint $table) {
            $table->string('dn_no')->nullable()->after('deliv_dest');
            $table->string('cust_po_no')->nullable()->after('dn_no');
            $table->string('pic')->nullable()->after('cust_po_no');
            $table->string('ship_via')->nullable()->after('pic');
        });
    }

    public function down(): void
    {
        Schema::table('packing_lists', function (Blueprint $table) {
            $table->dropColumn(['dn_no', 'cust_po_no', 'pic', 'ship_via']);
        });
    }
};
