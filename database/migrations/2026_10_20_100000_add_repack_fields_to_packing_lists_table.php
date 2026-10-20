<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packing_lists', function (Blueprint $table) {
            $table->unsignedInteger('koli_qty')->nullable()->after('status');
            $table->decimal('repack_length', 8, 2)->nullable()->after('koli_qty');
            $table->decimal('repack_width', 8, 2)->nullable()->after('repack_length');
            $table->decimal('repack_height', 8, 2)->nullable()->after('repack_width');
            $table->decimal('repack_weight', 12, 2)->nullable()->after('repack_height');
            $table->dateTime('repacked_at')->nullable()->after('repack_weight');
            $table->foreignId('repacked_by')->nullable()->after('repacked_at')
                ->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('packing_lists', function (Blueprint $table) {
            $table->dropForeign(['repacked_by']);
            $table->dropColumn([
                'koli_qty',
                'repack_length',
                'repack_width',
                'repack_height',
                'repack_weight',
                'repacked_at',
                'repacked_by',
            ]);
        });
    }
};
