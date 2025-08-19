<?php

namespace App\Http\Controllers;

use App\Models\GeneralRoom;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\Outbound;
use App\Models\OutboundDetail;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPurcDoc = DB::table('purchase_order')->whereBetween('created_at', [date('Y-m-01'), date('Y-m-d')])->count();
        $totalSalesDoc = DB::table('purchase_order_detail')->whereBetween('created_at', [date('Y-m-01'), date('Y-m-d')])->groupBy('sales_doc')->count();
        $totalStock = DB::table('inventory')->where('type', 'inv')->sum('stock');
        $stockGR = DB::table('inventory_package')->whereNotIn('storage_id', [1,2,3,4])->where('qty', '!=', 0)->count();

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

    public function dashboardDetail(Request $request): View
    {
        $purchaseOrder = PurchaseOrder::with('customer')->where('id', $request->query('id'))->first();

        $purchaseOrderDetail = PurchaseOrderDetail::where('purchase_order_id', $request->query('id'))
            ->select([
                'sales_doc',
                DB::raw('SUM(qty_qc) as qty_qc'),
                DB::raw('SUM(po_item_qty) as qty'),
            ])
            ->orderBy('sales_doc')
            ->groupBy([
                'sales_doc'
            ])
            ->get();

        foreach ($purchaseOrderDetail as $detail) {
            $detail->stock = DB::table('inventory_detail')
                ->where('sales_doc', $detail->sales_doc)
                ->whereNotIn('storage_id', [1,2,3,4])
                ->sum('qty');

            $detail->qty_outbound = DB::table('outbound_detail')
                ->leftJoin('outbound', 'outbound_detail.outbound_id', '=', 'outbound.id')
                ->leftJoin('inventory_package_item', 'inventory_package_item.id', '=', 'outbound_detail.inventory_package_item_id')
                ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_package_item.purchase_order_detail_id')
                ->where('purchase_order_detail.sales_doc', $detail->sales_doc)
                ->where('outbound.type', 'outbound')
                ->sum('outbound_detail.qty');
        }

        $title = 'Dashboard PO';
        return view('dashboard.po.detail', compact('title', 'purchaseOrder', 'purchaseOrderDetail'));
    }

    public function dashboardSoDetail(Request $request): View
    {
        $purchaseOrderDetail = PurchaseOrderDetail::where('purchase_order_id', $request->query('po'))
            ->where('sales_doc', $request->query('so'))
            ->orderBy('sales_doc')
            ->get();

        foreach ($purchaseOrderDetail as $detail) {
            $detail->stock = DB::table('inventory_detail')->where('purchase_order_detail_id', $detail->id)->sum('qty');
            $detail->qty_outbound = DB::table('outbound_detail')
                ->leftJoin('inventory_package_item', 'inventory_package_item.id', '=', 'outbound_detail.inventory_package_item_id')
                ->where('inventory_package_item.purchase_order_detail_id', $detail->id)
                ->sum('outbound_detail.qty');
        }

        $title = 'Dashboard PO';
        return view('dashboard.po.detail-so', compact('title', 'purchaseOrderDetail'));
    }

    public function dashboardStockSN(Request $request): View
    {
        $serialNumber = DB::table('inventory_package_item')
            ->leftJoin('inventory_package', 'inventory_package_item.inventory_package_id', '=', 'inventory_package.id')
            ->leftJoin('inventory_package_item_sn', 'inventory_package_item_sn.inventory_package_item_id', '=', 'inventory_package_item.id')
            ->where('purchase_order_detail_id', $request->query('id'))
            ->whereNotIn('inventory_package.storage_id', [1,2,3,4])
            ->where('inventory_package_item.qty', '!=', 0)
            ->where('inventory_package_item_sn.qty', '!=', 0)
            ->select([
                'inventory_package_item_sn.serial_number',
            ])
            ->get();

        $title = 'Dashboard PO';
        return view('dashboard.po.sn', compact('title', 'serialNumber'));
    }

    public function dashboardOutboundSN(Request $request): View
    {
        $serialNumber = DB::table('outbound')
            ->leftJoin('outbound_detail', 'outbound_detail.outbound_id', '=', 'outbound.id')
            ->leftJoin('inventory_package_item', 'inventory_package_item.id', '=', 'outbound_detail.inventory_package_item_id')
            ->leftJoin('outbound_detail_sn', 'outbound_detail_sn.outbound_detail_id', '=', 'outbound_detail.id')
            ->where('inventory_package_item.purchase_order_detail_id', $request->query('id'))
            ->where('type', 'outbound')
            ->where('status', 'outbound')
            ->select([
                'outbound_detail_sn.serial_number',
            ])
            ->get();

        $title = 'Dashboard PO';
        return view('dashboard.po.sn', compact('title', 'serialNumber'));
    }

    public function dashboardAging(): View
    {
        $queryAging = DB::table('inventory_detail')
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_detail.purchase_order_detail_id')
            ->where('qty', '!=', 0);

        $agingType1 = (clone $queryAging)->whereBetween('inventory_detail.aging_date', [Carbon::now()->subDays(90)->startOfDay(), Carbon::now()->subDays(1)->endOfDay()])
            ->select([
                DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total'),
                DB::raw('SUM(inventory_detail.qty) as qty'),
            ])
            ->first();

        $agingType2 = (clone $queryAging)->whereBetween('inventory_detail.aging_date', [Carbon::now()->subDays(180)->startOfDay(), Carbon::now()->subDays(91)->endOfDay()])
            ->select([
                DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total'),
                DB::raw('SUM(inventory_detail.qty) as qty'),
            ])
            ->first();

        $agingType3 = (clone $queryAging)->whereBetween('inventory_detail.aging_date', [Carbon::now()->subDays(365)->startOfDay(), Carbon::now()->subDays(181)->endOfDay()])
            ->select([
                DB::raw('SUM(inventory_detail.qty * purchase_order_detail.net_order_price) as total'),
                DB::raw('SUM(inventory_detail.qty) as qty'),
            ])
            ->first();

        $agingType4 = (clone $queryAging)->where('inventory_detail.aging_date', '<', Carbon::now()->subDays(365)->startOfDay())
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

    public function dashboardOutboundDetail(Request $request): View
    {
        $outbound = Outbound::with('customer', 'user')->where('id', $request->query('id'))->first();
        $outboundDetail = OutboundDetail::with('inventoryPackageItem', 'inventoryPackageItem.purchaseOrderDetail', 'outboundDetailSn')->where('outbound_id', $request->query('id'))->get();

        $title = 'Dashboard Outbound';
        return view('dashboard.outbound.detail', compact('title', 'outbound', 'outboundDetail'));
    }

    // Mobile APP
    public function dashboardMobile(): View
    {
        $title = 'Dashboard';
        return view('mobile.dashboard.index', compact('title'));
    }
}
