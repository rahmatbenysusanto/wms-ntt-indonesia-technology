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
            $po->value = DB::table('purchase_order_detail')->where('purchase_order_id', $po->id)->select(DB::raw('SUM(po_item_qty * net_order_price) as total'))->value('total');
            $po->listSO = DB::table('purchase_order_detail')
                ->where('purchase_order_id', $po->id)
                ->select([
                    'sales_doc',
                    DB::raw('SUM(po_item_qty * net_order_price) as total'),
                ])
                ->groupBy('sales_doc')
                ->get();
        }

        $title = 'Dashboard PO';
        return view('dashboard.po.index', compact('title', 'listPO'));
    }

    public function dashboardAging(): View
    {
        $agingData = DB::table('inventory_package_item')
            ->where('inventory_package_item.qty', '!=', 0)
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_package_item.purchase_order_detail_id');

        $aging1 = $agingData->whereBetween('inventory_package_item.created_at', [Carbon::now()->subDays(90)->startOfDay(), Carbon::now()->subDays(1)->endOfDay()])
            ->selectRaw('SUM(inventory_package_item.qty * purchase_order_detail.net_order_price) as total')
            ->value('total');

        $aging2 = $agingData->whereBetween('inventory_package_item.created_at', [Carbon::now()->subDays(180)->startOfDay(), Carbon::now()->subDays(91)->endOfDay()])
            ->selectRaw('SUM(inventory_package_item.qty * purchase_order_detail.net_order_price) as total')
            ->value('total');

        $aging3 = $agingData->whereBetween('inventory_package_item.created_at', [Carbon::now()->subDays(365)->startOfDay(), Carbon::now()->subDays(181)->endOfDay()])
            ->selectRaw('SUM(inventory_package_item.qty * purchase_order_detail.net_order_price) as total')
            ->value('total');

        $aging4 = $agingData->where('inventory_package_item.created_at', '<', Carbon::now()->subDays(365)->startOfDay())
            ->selectRaw('SUM(inventory_package_item.qty * purchase_order_detail.net_order_price) as total')
            ->value('total');

        $title = 'Dashboard Aging';
        return view('dashboard.aging.index', compact('title', 'aging1', 'aging2', 'aging3', 'aging4'));
    }

    public function dashboardOutbound(): View
    {
        $title = 'Dashboard Outbound';
        return view('dashboard.outbound.index', compact('title'));
    }

    // Mobile APP
    public function dashboardMobile(): View
    {
        $title = 'Dashboard';
        return view('mobile.dashboard.index', compact('title'));
    }
}
