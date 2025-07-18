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
}
