<?php

namespace App\Http\Controllers;

use App\Models\GeneralRoom;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\Outbound;
use App\Models\OutboundDetail;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPurcDoc = 0;
        $totalSalesDoc = 0;
        $totalStock = 0;
        $stockGR = 0;

        $listPO = [];

        $totalPO = 0;
        $totalQtyPO = 0;
        $totalOutbound = 0;
        $totalQtyOutbound = 0;

        $title = 'Dashboard';
        return view('dashboard.index', compact('title', 'totalPurcDoc', 'totalSalesDoc', 'listPO', 'totalStock', 'stockGR', 'totalPO', 'totalQtyPO', 'totalOutbound', 'totalQtyOutbound'));
    }

    public function dashboardPo(): View
    {
        $listPO = PurchaseOrder::with('customer')->whereIn('status', ['process', 'done', 'close'])->latest()->paginate(10);

        foreach ($listPO as $po) {
            $po->qty_po = DB::table('purchase_order_detail')->where('purchase_order_id', $po->id)->sum('qty_qc');
            $po->stock = DB::table('inventory')
                ->where('purchase_order_id', $po->id)
                ->where('type', 'inv')
                ->value('stock');
            $po->qty_outbound = DB::table('outbound_detail')
                ->leftJoin('inventory_package_item', 'inventory_package_item.id', '=', 'outbound_detail.inventory_package_item_id')
                ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_package_item.purchase_order_detail_id')
                ->where('purchase_order_detail.purchase_order_id', $po->id)
                ->sum('outbound_detail.qty');
        }

        $title = 'Dashboard PO';
        return view('dashboard.po.index', compact('title', 'listPO'));
    }

    public function dashboardAging(): View
    {
        $queryAging = DB::table('inventory_detail')
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_detail.purchase_order_detail_id')
            ->where('qty', '!=', 0);

        $agingType1 = $queryAging->whereBetween('inventory_detail.aging_date', [Carbon::now()->subDays(90)->startOfDay(), Carbon::now()->subDays(1)->endOfDay()])
            ->select([
                DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total'),
                DB::raw('SUM(inventory_detail.qty) as qty'),
            ])
            ->first();

        $agingType2 = $queryAging->whereBetween('inventory_detail.aging_date', [Carbon::now()->subDays(180)->startOfDay(), Carbon::now()->subDays(91)->endOfDay()])
            ->select([
                DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total'),
                DB::raw('SUM(inventory_detail.qty) as qty'),
            ])
            ->first();

        $agingType3 = $queryAging->whereBetween('inventory_detail.aging_date', [Carbon::now()->subDays(365)->startOfDay(), Carbon::now()->subDays(181)->endOfDay()])
            ->select([
                DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total'),
                DB::raw('SUM(inventory_detail.qty) as qty'),
            ])
            ->first();

        $agingType4 = $queryAging->where('inventory_detail.aging_date', '<', Carbon::now()->subDays(365)->startOfDay())
            ->select([
                DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total'),
                DB::raw('SUM(inventory_detail.qty) as qty'),
            ])
            ->first();

        $title = 'Dashboard Aging';
        return view('dashboard.aging.index', compact('title', 'agingType1', 'agingType2', 'agingType3', 'agingType4'));
    }

    public function dashboardAgingDetail(Request $request): View
    {
        switch ($request->query('type')) {
            case 1:
                $text = '1 - 90 Day';
                $start = Carbon::now()->subDays(90)->startOfDay();
                $end = Carbon::now()->subDays(1)->endOfDay();

                $inventoryDetail = DB::table('inventory_detail')
                    ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
                    ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
                    ->whereBetween('inventory_detail.aging_date', [$start, $end])
                    ->where('inventory_detail.qty', '!=', 0)
                    ->select([
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc',
                        'inventory_detail.aging_date',
                        DB::raw('SUM(inventory_detail.qty) as qty'),
                        DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total')
                    ])
                    ->groupBy(
                        'purchase_order.purc_doc',
                        'inventory_detail.aging_date',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc'
                    )
                    ->paginate(10)
                    ->appends([
                        'type' => $request->query('type')
                    ]);

                break;
            case 2:
                $text = '91 - 180 Day';
                $start = Carbon::now()->subDays(180)->startOfDay();
                $end = Carbon::now()->subDays(91)->endOfDay();

                $inventoryDetail = DB::table('inventory_detail')
                    ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
                    ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
                    ->whereBetween('inventory_detail.aging_date', [$start, $end])
                    ->where('inventory_detail.qty', '!=', 0)
                    ->select([
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc',
                        'inventory_detail.aging_date',
                        DB::raw('SUM(inventory_detail.qty) as qty'),
                        DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total')
                    ])
                    ->groupBy(
                        'purchase_order.purc_doc',
                        'inventory_detail.aging_date',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc'
                    )
                    ->paginate(10)
                    ->appends([
                        'type' => $request->query('type')
                    ]);

                break;
            case 3:
                $text = '181 - 365 Day';
                $start = Carbon::now()->subDays(365)->startOfDay();
                $end = Carbon::now()->subDays(181)->endOfDay();

                $inventoryDetail = DB::table('inventory_detail')
                    ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
                    ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
                    ->whereBetween('inventory_detail.aging_date', [$start, $end])
                    ->where('inventory_detail.qty', '!=', 0)
                    ->select([
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc',
                        'inventory_detail.aging_date',
                        DB::raw('SUM(inventory_detail.qty) as qty'),
                        DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total')
                    ])
                    ->groupBy(
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'inventory_detail.aging_date',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc'
                    )
                    ->paginate(10)
                    ->appends([
                        'type' => $request->query('type')
                    ]);
                break;
            case 4:
                $text = '> 365 Day';
                $start = Carbon::now()->subDays(365)->startOfDay();

                $inventoryDetail = DB::table('inventory_detail')
                    ->leftJoin('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
                    ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
                    ->where('inventory_detail.aging_date', '<', $start)
                    ->where('inventory_detail.qty', '!=', 0)
                    ->select([
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc',
                        'inventory_detail.aging_date',
                        DB::raw('SUM(inventory_detail.qty) as qty'),
                        DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total')
                    ])
                    ->groupBy(
                        'purchase_order.purc_doc',
                        'inventory_detail.sales_doc',
                        'inventory_detail.aging_date',
                        'purchase_order_detail.material',
                        'purchase_order_detail.po_item_desc',
                        'purchase_order_detail.prod_hierarchy_desc'
                    )
                    ->paginate(10)
                    ->appends([
                        'type' => $request->query('type')
                    ]);
                break;
        }

        $title = 'Dashboard Aging';
        return view('dashboard.aging.detail', compact('title', 'text', 'inventoryDetail'));
    }

    public function dashboardOutbound(): View
    {
        $outbound = Outbound::with('customer', 'user')
            ->where('type', 'outbound')
            ->latest()
            ->paginate(10);

        foreach ($outbound as $item) {
            $item->price = DB::table('outbound_detail')
                ->leftJoin('inventory_package_item', 'inventory_package_item.id', '=', 'outbound_detail.inventory_package_item_id')
                ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_package_item.purchase_order_detail_id')
                ->where('outbound_detail.outbound_id', $item->id)
                ->select([
                    DB::raw('SUM(outbound_detail.qty * purchase_order_detail.net_order_price) as price'),
                ])
                ->value('price');
        }

        $title = 'Dashboard Outbound';
        return view('dashboard.outbound.index', compact('title', 'outbound'));
    }

    // Mobile APP
    public function dashboardMobile(): View
    {
        $title = 'Dashboard';
        return view('mobile.dashboard.index', compact('title'));
    }
}
