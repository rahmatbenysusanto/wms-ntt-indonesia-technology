<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseApiController extends Controller
{
    public function inbound(Request $request): JsonResponse
    {
        $query = DB::table('purchase_order as po')
            ->leftJoin('customer as c', 'c.id', '=', 'po.customer_id')
            ->leftJoin('vendor as v', 'v.id', '=', 'po.vendor_id')
            ->when($request->query('purc_doc'), fn ($q, $v) => $q->where('po.purc_doc', 'like', "%{$v}%"))
            ->when($request->query('customer_id'), fn ($q, $v) => $q->where('po.customer_id', $v))
            ->when($request->query('vendor_id'), fn ($q, $v) => $q->where('po.vendor_id', $v))
            ->when($request->query('date_from'), fn ($q, $v) => $q->whereDate('po.created_at', '>=', $v))
            ->when($request->query('date_to'), fn ($q, $v) => $q->whereDate('po.created_at', '<=', $v))
            ->select([
                'po.id',
                'po.purc_doc',
                'po.status',
                'po.sales_doc_qty',
                'po.material_qty',
                'po.item_qty',
                'po.koli',
                'po.created_at',
                'c.name as customer',
                'v.name as vendor',
                DB::raw('(select coalesce(sum(qty_qc), 0) from purchase_order_detail where purchase_order_id = po.id) as total_qc'),
                DB::raw("(select coalesce(sum(qty), 0) from inventory_history where purchase_order_id = po.id and type = 'inbound') as total_inbound"),
            ])
            ->orderByDesc('po.created_at');

        return $this->paginate($query, $request);
    }

    public function inboundDetail(int $id): JsonResponse
    {
        $po = DB::table('purchase_order as po')
            ->leftJoin('customer as c', 'c.id', '=', 'po.customer_id')
            ->leftJoin('vendor as v', 'v.id', '=', 'po.vendor_id')
            ->where('po.id', $id)
            ->select(['po.*', 'c.name as customer', 'v.name as vendor'])
            ->first();

        if (!$po) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $items = DB::table('purchase_order_detail')
            ->where('purchase_order_id', $id)
            ->get();

        return response()->json(['data' => ['purchase_order' => $po, 'items' => $items]]);
    }

    public function outbound(Request $request): JsonResponse
    {
        $query = DB::table('outbound as o')
            ->leftJoin('customer as c', 'c.id', '=', 'o.customer_id')
            ->where('o.type', 'outbound')
            ->when($request->query('purc_doc'), fn ($q, $v) => $q->where('o.purc_doc', 'like', "%{$v}%"))
            ->when($request->query('number'), fn ($q, $v) => $q->where('o.number', 'like', "%{$v}%"))
            ->when($request->query('customer_id'), fn ($q, $v) => $q->where('o.customer_id', $v))
            ->when($request->query('status'), fn ($q, $v) => $q->where('o.status', $v))
            ->when($request->query('date_from'), fn ($q, $v) => $q->whereDate('o.outbound_date', '>=', $v))
            ->when($request->query('date_to'), fn ($q, $v) => $q->whereDate('o.outbound_date', '<=', $v))
            ->when($request->query('serial_number'), fn ($q, $v) => $q->whereExists(function ($sub) use ($v) {
                $sub->select(DB::raw(1))
                    ->from('outbound_detail as od')
                    ->join('outbound_detail_sn as sn', 'sn.outbound_detail_id', '=', 'od.id')
                    ->whereColumn('od.outbound_id', 'o.id')
                    ->where('sn.serial_number', 'like', "%{$v}%");
            }))
            ->select([
                'o.id',
                'o.number',
                'o.purc_doc',
                'o.type',
                'o.status',
                'o.outbound_date',
                'o.delivery_date',
                'o.delivery_note_number',
                'o.ntt_dn',
                'o.qty_item',
                'o.qty',
                'o.koli',
                'o.deliv_loc',
                'o.deliv_dest',
                'o.note',
                'o.created_at',
                'c.name as customer',
            ])
            ->orderByDesc('o.outbound_date');

        return $this->paginate($query, $request);
    }

    public function outboundDetail(int $id): JsonResponse
    {
        $outbound = DB::table('outbound as o')
            ->leftJoin('customer as c', 'c.id', '=', 'o.customer_id')
            ->where('o.id', $id)
            ->select(['o.*', 'c.name as customer'])
            ->first();

        if (!$outbound) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $items = DB::table('outbound_detail as od')
            ->leftJoin('inventory_package_item as ipi', 'ipi.id', '=', 'od.inventory_package_item_id')
            ->leftJoin('purchase_order_detail as pod', 'pod.id', '=', 'ipi.purchase_order_detail_id')
            ->where('od.outbound_id', $id)
            ->select([
                'od.id',
                'od.qty',
                'ipi.is_parent',
                'pod.id as purchase_order_detail_id',
                'pod.sales_doc',
                'pod.item',
                'pod.material',
                'pod.po_item_desc',
                'pod.prod_hierarchy_desc',
                'pod.net_order_price',
                'pod.currency',
            ])
            ->get();

        $serials = DB::table('outbound_detail_sn')
            ->whereIn('outbound_detail_id', $items->pluck('id'))
            ->get()
            ->groupBy('outbound_detail_id');

        $items->each(fn ($item) => $item->serial_numbers = $serials->get($item->id, collect())->pluck('serial_number')->values());

        return response()->json(['data' => ['outbound' => $outbound, 'items' => $items]]);
    }

    public function inventory(Request $request): JsonResponse
    {
        $query = DB::table('inventory_detail as idt')
            ->join('inventory as inv', 'inv.id', '=', 'idt.inventory_id')
            ->join('purchase_order_detail as pod', 'pod.id', '=', 'idt.purchase_order_detail_id')
            ->join('purchase_order as po', 'po.id', '=', 'pod.purchase_order_id')
            ->leftJoin('customer as c', 'c.id', '=', 'po.customer_id')
            ->leftJoin('inventory_package_item as ipi', 'ipi.id', '=', 'idt.inventory_package_item_id')
            ->where('inv.type', 'inv')
            ->where('idt.qty', '!=', 0)
            ->whereNotIn('idt.storage_id', [1, 2, 3, 4])
            ->when($request->query('purc_doc'), fn ($q, $v) => $q->where('po.purc_doc', 'like', "%{$v}%"))
            ->when($request->query('sales_doc'), fn ($q, $v) => $q->where('pod.sales_doc', 'like', "%{$v}%"))
            ->when($request->query('material'), fn ($q, $v) => $q->where('pod.material', 'like', "%{$v}%"))
            ->when($request->query('customer_id'), fn ($q, $v) => $q->where('po.customer_id', $v))
            ->when($request->query('category') === 'parent', fn ($q) => $q->where('ipi.is_parent', 1))
            ->when($request->query('category') === 'child', fn ($q) => $q->where('ipi.is_parent', 0))
            ->when($request->query('serial_number'), fn ($q, $v) => $q->whereExists(function ($sub) use ($v) {
                $sub->select(DB::raw(1))
                    ->from('inventory_package_item_sn as sn')
                    ->whereColumn('sn.inventory_package_item_id', 'idt.inventory_package_item_id')
                    ->where('sn.serial_number', 'like', "%{$v}%")
                    ->where('sn.qty', '!=', 0);
            }))
            ->select([
                'pod.id as purchase_order_detail_id',
                'po.purc_doc',
                'pod.sales_doc',
                'pod.item',
                'pod.material',
                'pod.po_item_desc',
                'pod.prod_hierarchy_desc',
                'pod.net_order_price',
                'pod.price_idr',
                'pod.currency',
                'c.name as customer',
                DB::raw('sum(idt.qty) as qty'),
                DB::raw('sum(idt.qty * pod.net_order_price) as nominal'),
                DB::raw('sum(idt.qty * pod.price_idr) as nominal_idr'),
                DB::raw('min(idt.aging_date) as aging_date'),
            ])
            ->groupBy([
                'pod.id',
                'po.purc_doc',
                'pod.sales_doc',
                'pod.item',
                'pod.material',
                'pod.po_item_desc',
                'pod.prod_hierarchy_desc',
                'pod.net_order_price',
                'pod.price_idr',
                'pod.currency',
                'c.name',
            ])
            ->orderBy('po.purc_doc');

        return $this->paginate($query, $request);
    }

    public function inventoryDetail(int $purchaseOrderDetailId): JsonResponse
    {
        $location = DB::table('inventory_detail as idt')
            ->join('inventory_package_item as ipi', 'ipi.id', '=', 'idt.inventory_package_item_id')
            ->join('inventory_package as ip', 'ip.id', '=', 'ipi.inventory_package_id')
            ->leftJoin('storage as s', 's.id', '=', 'ip.storage_id')
            ->where('idt.purchase_order_detail_id', $purchaseOrderDetailId)
            ->where('idt.qty', '!=', 0)
            ->whereNotIn('idt.storage_id', [1, 2, 3, 4])
            ->select([
                'idt.id',
                'idt.qty',
                'idt.aging_date',
                'ipi.id as inventory_package_item_id',
                'ipi.is_parent',
                'ip.number',
                'ip.reff_number',
                'ip.return',
                'ip.return_from',
                's.id as storage_id',
                's.raw',
                's.area',
                's.rak',
                's.bin',
            ])
            ->get();

        $serials = DB::table('inventory_package_item_sn')
            ->whereIn('inventory_package_item_id', $location->pluck('inventory_package_item_id'))
            ->where('qty', '!=', 0)
            ->get()
            ->groupBy('inventory_package_item_id');

        $location->each(fn ($item) => $item->serial_numbers = $serials->get($item->inventory_package_item_id, collect())->pluck('serial_number')->values());

        return response()->json(['data' => $location]);
    }

    private function paginate($query, Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 25), 100);
        $page = max((int) $request->query('page', 1), 1);
        $total = (clone $query)->count();

        $data = $query->forPage($page, $perPage)->get();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage),
            ],
        ]);
    }
}
