<?php

namespace App\Http\Controllers;

use App\Models\GeneralRoom;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\Outbound;
use App\Models\OutboundDetail;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPurcDoc = Inventory::groupBy('purc_doc')->count();
        $totalSalesDoc = Inventory::groupBy('sales_doc')->count();
        $totalStock = InventoryDetail::sum('stock');
        $stockGR = GeneralRoom::where('status', 'open')->sum('qty_item');

        $listPO = PurchaseOrder::with('customer', 'vendor')
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->limit(7)
            ->get();

        $totalPO = PurchaseOrder::where('status', '!=', 'cancel')->count();
        $totalQtyPO = DB::table('purchase_order')->where('purchase_order.status', '!=', 'cancel')
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.purchase_order_id', '=', 'purchase_order.id')
            ->sum('purchase_order_detail.po_item_qty');
        $totalOutbound = Outbound::count();
        $totalQtyOutbound = OutboundDetail::sum('qty');

        $title = 'Dashboard';
        return view('dashboard.index', compact('title', 'totalPurcDoc', 'totalSalesDoc', 'listPO', 'totalStock', 'stockGR', 'totalPO', 'totalQtyPO', 'totalOutbound', 'totalQtyOutbound'));
    }
}
