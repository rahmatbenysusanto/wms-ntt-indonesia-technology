<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PurchaseOrder;
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
}
