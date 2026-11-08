<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PackingList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

Auth::login(App\Models\User::first());

$pl = PackingList::with(['details.repack', 'repacks.details'])->first();
if (!$pl || $pl->details->count() < 3) {
    echo "Need a packing list with >=3 details. count=" . ($pl?->details->count() ?? 0) . "\n";
    exit(0);
}

DB::beginTransaction();

try {
    $ids = $pl->details->pluck('id')->values();
    $koli1 = $ids->take(3)->all();
    $koli2 = $ids->slice(3)->all();

    $payload = [
        'id' => $pl->id,
        'repacks' => [
            ['koli_no' => 'Koli 1', 'qty_box' => 1, 'length' => 64, 'width' => 24, 'height' => 44, 'weight' => 11, 'note' => null, 'detail_ids' => $koli1],
        ],
    ];
    if (count($koli2)) {
        $payload['repacks'][] = ['koli_no' => 'Koli 2', 'qty_box' => 2, 'length' => 80, 'width' => 50, 'height' => 45, 'weight' => 38, 'note' => null, 'detail_ids' => $koli2];
    }

    $request = Illuminate\Http\Request::create('/outbound/packaging-list/repack', 'POST', $payload);
    $response = app(App\Http\Controllers\OutboundController::class)->packagingListRepackStore($request);
    $body = json_decode($response->getContent(), true);
    echo "store: " . json_encode($body) . "\n";

    $pl->refresh()->load(['details.repack', 'repacks.details']);
    echo "repacks: " . $pl->repacks->count() . "\n";
    foreach ($pl->repacks as $r) {
        echo "  {$r->koli_no}: qty_box={$r->qty_box} dims={$r->length}x{$r->width}x{$r->height} w={$r->weight} items=" . $r->details->count() . "\n";
    }
    echo "assigned details: " . $pl->details->whereNotNull('packing_list_repack_id')->count() . "/" . $pl->details->count() . "\n";

    $html = Illuminate\Support\Facades\View::make('pdf.packing-list', ['packingList' => $pl, 'mode' => 'final'])->render();
    echo "pdf final: rowspan=" . substr_count($html, 'rowspan') . ", has 64 X 24 X 44=" . (str_contains($html, '64 X 24 X 44') ? 'yes' : 'no') . "\n";

    // re-save (edit existing repack path)
    $payload['repacks'][0]['weight'] = 12;
    $response = app(App\Http\Controllers\OutboundController::class)->packagingListRepackStore(Illuminate\Http\Request::create('/x', 'POST', $payload));
    echo "re-save: " . $response->getContent() . "\n";
    $pl->refresh()->load('repacks');
    echo "after re-save repacks: " . $pl->repacks->count() . " (expect same count)\n";
} catch (\Throwable $e) {
    echo "FAIL: " . $e->getMessage() . " @ " . $e->getFile() . ":" . $e->getLine() . "\n";
}

DB::rollBack();
echo "rolled back, repacks now: " . App\Models\PackingListRepack::count() . "\n";
