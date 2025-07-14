<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPurcDoc = Inventory::groupBy('purc_doc')->count();
        $totalSalesDoc = Inventory::groupBy('sales_doc')->count();
        $totalStock = InventoryDetail::sum('stock');

        $listPO = PurchaseOrder::with('customer', 'vendor')
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->limit(7)
            ->get();

        $title = 'Dashboard';
        return view('dashboard.index', compact('title', 'totalPurcDoc', 'totalSalesDoc', 'listPO', 'totalStock'));
    }
}
