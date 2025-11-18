<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardCustomerController extends Controller
{
    public function index(): View
    {
        $customer = Customer::all();

        $title = 'Dashboard Customer';
        return view('dashboard-customer.index', compact('title', 'customer'));
    }

    public function cardJson(Request $request): JsonResponse
    {
        $totalPO = PurchaseOrder::whereIn('status', ['new', 'open'])
            ->when($request->get('customer'), function ($query) use ($request) {
                if ($request->get('customer') != '') {
                    $query->where('customer_id', $request->get('customer'));
                }
            })
            ->count();

        $totalSO = DB::table('purchase_order')
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.purchase_order_id', '=', 'purchase_order.id')
            ->whereIn('purchase_order.status', ['new', 'open'])
            ->where('purchase_order_detail.status', 'new')
            ->when($request->get('customer'), function ($query) use ($request) {
                if ($request->get('customer') != '') {
                    $query->where('purchase_order.customer_id', $request->get('customer'));
                }
            })
            ->count();

        $totalStock = DB::table('inventory')
            ->leftJoin('purchase_order', 'purchase_order.id', '=', 'inventory.purchase_order_id')
            ->where('inventory.type', 'inv')
            ->when($request->get('customer'), function ($query) use ($request) {
                if ($request->get('customer') != '') {
                    $query->where('purchase_order.customer_id', $request->get('customer'));
                }
            })
            ->sum('stock');

        $totalPrice = DB::table('inventory_detail as idt')
            ->leftJoin('purchase_order_detail as pod', 'pod.id', '=', 'idt.purchase_order_detail_id')
            ->leftJoin('purchase_order as po', 'po.id', '=', 'pod.purchase_order_id')
            ->whereNotIn('idt.storage_id', [1,2,3,4])
            ->where('idt.qty', '!=', 0)
            ->when($request->get('customer'), function ($q) use ($request) {
                $q->where('po.customer_id', $request->get('customer'));
            })
            ->selectRaw('COALESCE(SUM(idt.qty * COALESCE(pod.net_order_price,0)),0) as total')
            ->value('total');


        return response()->json([
            'status'    => true,
            'data'      => [
                'totalPO'       => $totalPO,
                'totalSO'       => $totalSO,
                'totalStock'    => $totalStock,
                'totalPrice'    => $totalPrice,
            ]
        ]);
    }

    public function inbound(Request $request): View
    {
        $customer = Customer::all();

        $purchaseOrder = PurchaseOrder::with('customer:id,name', 'vendor:id,name', 'user:id,name')->paginate(10);

        $title = 'Dashboard Customer';
        return view('dashboard-customer.inbound', compact('title', 'customer', 'purchaseOrder'));
    }

    public function inboundDetail(Request $request): View
    {
        $purchaseOrder = PurchaseOrder::with('customer:id,name', 'vendor:id,name', 'user:id,name')->where('id', $request->query('id'))->first();
        $purchaseOrderDetail = PurchaseOrderDetail::where('purchase_order_id', $request->query('id'))->get();

        $title = 'Dashboard Customer';
        return view('dashboard-customer.inbound-detail', compact('title', 'purchaseOrder', 'purchaseOrderDetail'));
    }

    public function aging(): View
    {
        $customer = Customer::all();

        $title = 'Dashboard Customer';
        return view('dashboard-customer.aging', compact('title', 'customer'));
    }

    public function agingChartQty(): JsonResponse
    {
        $aging = DB::table('inventory_detail')
            ->selectRaw("
                SUM(CASE WHEN DATEDIFF(CURDATE(), aging_date) BETWEEN 0 AND 90 THEN qty ELSE 0 END) AS day_1_90,
                SUM(CASE WHEN DATEDIFF(CURDATE(), aging_date) BETWEEN 91 AND 180 THEN qty ELSE 0 END) AS day_91_180,
                SUM(CASE WHEN DATEDIFF(CURDATE(), aging_date) BETWEEN 181 AND 365 THEN qty ELSE 0 END) AS day_181_365,
                SUM(CASE WHEN DATEDIFF(CURDATE(), aging_date) > 365 THEN qty ELSE 0 END) AS day_gt_365
            ")
            ->first();

        $series = [
            (int) $aging->day_1_90,
            (int) $aging->day_91_180,
            (int) $aging->day_181_365,
            (int) $aging->day_gt_365,
        ];

        return response()->json($series);
    }

    public function outbound(): View
    {
        $customer = Customer::all();

        $title = 'Dashboard Customer';
        return view('dashboard-customer.outbound', compact('title', 'customer'));
    }
}
