<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Outbound;
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

    public function agingChartPrice(): JsonResponse
    {
        $aging = DB::table('inventory_detail')
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_detail.purchase_order_detail_id')
            ->selectRaw("
                SUM(
                    CASE
                        WHEN DATEDIFF(CURDATE(), inventory_detail.aging_date) BETWEEN 0 AND 90
                            THEN inventory_detail.qty * COALESCE(purchase_order_detail.net_order_price, 0)
                        ELSE 0
                    END
                ) AS day_1_90,

                SUM(
                    CASE
                        WHEN DATEDIFF(CURDATE(), inventory_detail.aging_date) BETWEEN 91 AND 180
                            THEN inventory_detail.qty * COALESCE(purchase_order_detail.net_order_price, 0)
                        ELSE 0
                    END
                ) AS day_91_180,

                SUM(
                    CASE
                        WHEN DATEDIFF(CURDATE(), inventory_detail.aging_date) BETWEEN 181 AND 365
                            THEN inventory_detail.qty * COALESCE(purchase_order_detail.net_order_price, 0)
                        ELSE 0
                    END
                ) AS day_181_365,

                SUM(
                    CASE
                        WHEN DATEDIFF(CURDATE(), inventory_detail.aging_date) > 365
                            THEN inventory_detail.qty * COALESCE(purchase_order_detail.net_order_price, 0)
                        ELSE 0
                    END
                ) AS day_gt_365
            ")
            ->first();

        $series = [
            (float) $aging->day_1_90,
            (float) $aging->day_91_180,
            (float) $aging->day_181_365,
            (float) $aging->day_gt_365,
        ];


        return response()->json($series);
    }

    public function outbound(Request $request): View
    {
        $customer = Customer::all();

        $outbound = Outbound::with('customer', 'user')
            ->where('type', 'outbound')
            ->when($request->query('purcDoc'), function ($q) use ($request) {
                $q->where('purc_doc', $request->query('purcDoc'));
            })
            ->when($request->query('salesDoc'), function ($q) use ($request) {
                $q->where('sales_doc', 'LIKE', '%'.$request->query('salesDoc').'%');
            })
            ->when($request->query('customer'), function ($q) use ($request) {
                $q->where('customer_id', $request->query('customer'));
            })
            ->when($request->query('number'), function ($q) use ($request) {
                $q->where('delivery_note_number', $request->query('number'));
            })
            ->latest()
            ->paginate(10)
            ->appends([
                'purcDoc'   => $request->query('purcDoc'),
                'salesDoc'  => $request->query('salesDoc'),
                'customer'  => $request->query('customer'),
                'number'    => $request->query('number'),
            ]);

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

        $title = 'Dashboard Customer';
        return view('dashboard-customer.outbound', compact('title', 'customer', 'outbound'));
    }
}
