<?php
/**
 * Self-check flow repack packing list.
 * Jalankan: php repack_flow_check.php
 * Semua perubahan DB di-rollback.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\OutboundController;
use App\Models\PackingList;
use App\Models\PackingListRepack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

$pl = PackingList::with(['details', 'repacks.details'])->first();
if (!$pl || $pl->details->count() < 3) {
    echo "SKIP: butuh packing list dengan minimal 3 detail. count=" . ($pl?->details->count() ?? 0) . "\n";
    exit(0);
}

$before = PackingListRepack::count();
$fail = 0;
$assert = function (bool $ok, string $label) use (&$fail) {
    echo ($ok ? 'PASS' : 'FAIL') . " - {$label}\n";
    if (!$ok) $fail++;
};

DB::beginTransaction();
try {
    $controller = app(OutboundController::class);
    $ids = $pl->details->pluck('id')->values();

    // 1. Dua koli, hanya koli 1 yang memilih item -> sisanya auto masuk koli 1
    $payload = [
        'id' => $pl->id,
        'repacks' => [
            ['koli_no' => 'Koli 1', 'qty_box' => 1, 'length' => 64, 'width' => 24, 'height' => 44, 'weight' => 11, 'detail_ids' => $ids->take(3)->all()],
            ['koli_no' => 'Koli 2', 'qty_box' => 2, 'length' => 80, 'width' => 50, 'height' => 45, 'weight' => 38, 'detail_ids' => []],
        ],
    ];
    $res = json_decode($controller->packagingListRepackStore(Request::create('/x', 'POST', $payload))->getContent(), true);
    $assert(($res['status'] ?? false) === true, 'repack store sukses');

    $pl->refresh()->load(['details', 'repacks']);
    $assert($pl->repacks->count() === 2, 'jumlah koli = 2');
    $assert($pl->details->whereNull('packing_list_repack_id')->count() === 0, 'semua item punya koli (tidak ada sisa)');
    $koli1 = $pl->repacks->firstWhere('koli_no', 'Koli 1');
    $assert($koli1->details()->count() === $pl->details->count(), 'item tidak dicentang ikut koli pertama');

    // 2. PDF final: satu tabel item saja, repack tampil sebagai rowspan
    $html = View::make('pdf.packing-list', ['packingList' => $pl->load(['details', 'repacks', 'customer', 'createdBy', 'releasedBy', 'repackedBy']), 'mode' => 'final'])->render();
    $assert(substr_count($html, 'class="tbl"') === 1, 'PDF final hanya 1 tabel item (ditemukan ' . substr_count($html, 'class="tbl"') . ')');
    $assert(!str_contains($html, 'Total Koli</th>'), 'tidak ada tabel ringkasan repack terpisah');
    $assert(str_contains($html, 'rowspan'), 'PDF final memakai merged cell');
    $assert(str_contains($html, '64 X 24 X 44'), 'dimensi koli tampil di tabel item');

    // 3. Simpan ulang tidak menduplikasi koli
    $controller->packagingListRepackStore(Request::create('/x', 'POST', $payload));
    $pl->refresh();
    $assert($pl->repacks()->count() === 2, 're-save tidak menduplikasi koli');
} catch (\Throwable $e) {
    $assert(false, 'exception: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
} finally {
    DB::rollBack();
}

$after = PackingListRepack::count();
$assert($before === $after, "rollback bersih ({$before} -> {$after})");

echo $fail === 0 ? "\nALL PASS\n" : "\n{$fail} CHECK GAGAL\n";
exit($fail === 0 ? 0 : 1);