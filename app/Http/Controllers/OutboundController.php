<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GeneralRoom;
use App\Models\GeneralRoomDetail;
use App\Models\Inventory;
use App\Models\InventoryChild;
use App\Models\InventoryChildDetail;
use App\Models\InventoryDetail;
use App\Models\InventoryHistory;
use App\Models\InventoryParent;
use App\Models\InventoryParentDetail;
use App\Models\Outbound;
use App\Models\OutboundDetail;
use App\Models\OutboundSerialNumber;
use App\Models\SerialNumber;
use App\Models\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OutboundController extends Controller
{
    public function index(Request $request): View
    {
        $outbound = Outbound::with('user')->latest()->paginate(10);

        $title = 'Outbound';
        return view('outbound.index', compact('title', 'outbound'));
    }

    public function create(): View
    {
        $inventory = InventoryParent::with('product', 'storage', 'purchaseOrder')->where('storage_id', '!=', 1)->where('stock', '!=', 0)->latest()->get();

        $salesDoc = Inventory::where('stock', '!=', 0)
            ->groupBy('sales_doc')
            ->select([
                'sales_doc',
                DB::raw('SUM(stock) as stock')
            ])
            ->get();

        $customer = Customer::all();

        $title = 'Outbound';
        return view('outbound.create', compact('title', 'inventory', 'customer', 'salesDoc'));
    }

    public function getItemBySalesDoc(Request $request): \Illuminate\Http\JsonResponse
    {
        $products = InventoryParent::with('product', 'storage', 'purchaseOrder')
            ->where('stock', '!=', 0)
            ->where('sales_docs', 'LIKE', '%'.$request->get('salesDoc').'%')
            ->latest()
            ->get();

        return response()->json([
            'data' => $products
        ]);
    }

    public function getItemByInventoryDetail(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->get('type') == 'parent') {
            $product = InventoryDetail::with('purchaseOrderDetail', 'inventory.storage')->where('id', $request->get('id'))->where('type', 'parent')->first();
            $product->child = InventoryDetail::with('purchaseOrderDetail', 'inventory.storage')
                ->where('parent_id', $product->id)
                ->where('type', 'child')
                ->get();
        } else {
            $product = InventoryDetail::with('purchaseOrderDetail', 'inventory.storage')
                ->where('id', $request->get('idChild'))
                ->where('type', 'child')
                ->first();
        }

        return response()->json([
            'data' => $product
        ]);
    }

    public function getItemByProduct(Request $request): \Illuminate\Http\JsonResponse
    {
        $products = [];
        $inventoryParent = InventoryParent::where('id', $request->get('id'))->first();
        $storage = Storage::where('id', $inventoryParent->storage_id)->first();

        $parent = DB::table('inventory_parent_detail')
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_parent_detail.purchase_order_detail_id')
            ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
            ->where('inventory_parent_detail.inventory_parent_id', $request->get('id'))
            ->select([
                'inventory_parent_detail.id',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.material',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc',
                'inventory_parent_detail.product_id',
                'inventory_parent_detail.qty',
                'purchase_order_detail.id AS purchase_order_detail_id',
                'purchase_order_detail.item',
                'purchase_order.purc_doc'
            ])
            ->get();

        foreach ($parent as $item) {
            $serialNumber = SerialNumber::where('inventory_parent_detail_id', $item->id)
                ->where('qty', '!=', 0)
                ->get();

            $products[] = [
                'id'            => $item->id,
                'sales_doc'     => $item->sales_doc,
                'material'      => $item->material,
                'po_item_desc'  => $item->po_item_desc,
                'prod_hierarchy'=> $item->prod_hierarchy_desc,
                'qty'           => $item->qty,
                'qty_select'    => $item->qty,
                'type'          => 'parent',
                'purchase_order_detail_id' => $item->purchase_order_detail_id,
                'pa_number'     => $inventoryParent->pa_reff_number ?? $inventoryParent->pa_number,
                'item'          => $item->item,
                'purc_doc'      => $item->purc_doc,
                'storage'       => $storage->id == 1 ? '-' : ($storage->raw.' - '.$storage->area.' - '.$storage->rak.' - '.$storage->bin),
                'storage_id'    => $storage->id,
                'serial_number' => $serialNumber,
                'sn_select'     => [],
                'inventory_parent_id'   => $inventoryParent->id,
                'inventory_parent_detail_id' => $item->id,
                'product_id'    => $item->product_id
            ];
        }

        $child = DB::table('inventory_child')
            ->leftJoin('inventory_child_detail', 'inventory_child.id', '=', 'inventory_child_detail.inventory_child_id')
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_child_detail.purchase_order_detail_id')
            ->leftJoin('purchase_order', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
            ->where('inventory_child.inventory_parent_id', $request->get('id'))
            ->select([
                'inventory_child.id AS child_id',
                'inventory_child_detail.id AS child_detail_id',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.material',
                'purchase_order_detail.po_item_desc',
                'purchase_order_detail.prod_hierarchy_desc',
                'inventory_child_detail.qty',
                'purchase_order_detail.id AS purchase_order_detail_id',
                'purchase_order_detail.item',
                'purchase_order.purc_doc',
                'inventory_child_detail.product_id'
            ])
            ->get();
        foreach ($child as $item) {
            $serialNumber = SerialNumber::where('inventory_child_detail_id', $item->child_detail_id)
                ->where('qty', '!=', 0)
                ->get();

            $products[] = [
                'id'            => $item->child_detail_id,
                'sales_doc'     => $item->sales_doc,
                'material'      => $item->material,
                'po_item_desc'  => $item->po_item_desc,
                'prod_hierarchy'=> $item->prod_hierarchy_desc,
                'qty'           => $item->qty,
                'qty_select'    => $item->qty,
                'type'          => 'child',
                'purchase_order_detail_id' => $item->purchase_order_detail_id,
                'pa_number'     => $inventoryParent->pa_reff_number ?? $inventoryParent->pa_number,
                'item'          => $item->item,
                'purc_doc'      => $item->purc_doc,
                'storage'       => $storage->id == 1 ? '-' : ($storage->raw.' - '.$storage->area.' - '.$storage->rak.' - '.$storage->bin),
                'storage_id'    => $storage->id,
                'serial_number' => $serialNumber,
                'sn_select'     => [],
                'inventory_parent_id'   => $inventoryParent->id,
                'child_id'          => $item->child_id,
                'child_detail_id'   => $item->child_detail_id,
                'product_id'    => $item->product_id
            ];
        }

        return response()->json([
            'data' => $products
        ]);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $products = $request->post('products');
            $inventoryParent = InventoryParent::where('id', $products[0]['inventory_parent_id'])->first();
            $customer = Customer::find($request->post('customerId'));
            $qty_item = 0;

            $outbound = Outbound::create([
                'number'        => 'INV-' . date('ymd') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
                'purc_doc'      => $products[0]['purc_doc'],
                'sales_doc'     => json_encode($inventoryParent->sales_docs),
                'client'        => $customer->name,
                'customer_id'   => $customer->id,
                'deliv_loc'     => $request->post('delivLocation'),
                'deliv_dest'    => $request->post('deliveryDest'),
                'qty_item'      => 0,
                'created_by'    => Auth::id()
            ]);

            foreach ($products as $product) {
                if ($product['type'] == 'parent') {
                    $outboundDetail = OutboundDetail::create([
                        'outbound_id'                   => $outbound->id,
                        'product_id'                    => $product['product_id'],
                        'inventory_parent_id'           => $product['inventory_parent_id'],
                        'inventory_parent_detail_id'    => $product['inventory_parent_detail_id'],
                        'inventory_child_id'            => null,
                        'inventory_child_detail_id'     => null,
                        'qty'                           => $product['qty_select'],
                        'serial_number'                 => json_encode($product['sn_select'] ?? []),
                    ]);

                    InventoryParent::where('id', $product['inventory_parent_id'])->decrement('stock', $product['qty_select']);
                    InventoryParentDetail::where('id', $product['inventory_parent_detail_id'])->decrement('qty', $product['qty_select']);

                    Inventory::where('purc_doc', $product['purc_doc'])->where('sales_doc', $product['sales_doc'])->decrement('stock', $product['qty_select']);
                    InventoryDetail::where('id', $product['purchase_order_detail_id'])->where('storage_id', $product['storage_id'])->decrement('stock', $product['qty_select']);
                    InventoryHistory::create([
                        'purc_doc'                      => $product['purc_doc'],
                        'sales_doc'                     => $product['sales_doc'],
                        'inventory_parent_id'           => $product['inventory_parent_id'],
                        'inventory_parent_detail_id'    => $product['inventory_parent_detail_id'],
                        'outbound_id'                   => $outbound->id,
                        'outbound_detail_id'            => $outboundDetail->id,
                        'type'                          => 'outbound',
                        'qty'                           => $product['qty_select'],
                    ]);

                    foreach ($product['sn_select'] ?? [] as $sn) {
                        SerialNumber::where('inventory_parent_id', $product['inventory_parent_id'])
                            ->where('inventory_parent_detail_id', $product['inventory_parent_detail_id'])
                            ->where('serial_number', $sn)
                            ->decrement('qty', 1);

                        OutboundSerialNumber::create([
                            'outbound_id'       => $outbound->id,
                            'serial_number'     => $sn,
                        ]);
                    }
                } else {
                    $outboundDetail = OutboundDetail::create([
                        'outbound_id'                   => $outbound->id,
                        'product_id'                    => $product['product_id'],
                        'inventory_parent_id'           => null,
                        'inventory_parent_detail_id'    => null,
                        'inventory_child_id'            => $product['child_id'],
                        'inventory_child_detail_id'     => $product['child_detail_id'],
                        'qty'                           => $product['qty_select'],
                        'serial_number'                 => json_encode($product['sn_select'] ?? []),
                    ]);

                    InventoryChild::where('id', $product['child_id'])->decrement('stock', $product['qty_select']);
                    InventoryChildDetail::where('id', $product['child_detail_id'])->decrement('qty', $product['qty_select']);

                    Inventory::where('purc_doc', $product['purc_doc'])->where('sales_doc', $product['sales_doc'])->decrement('stock', $product['qty_select']);
                    InventoryDetail::where('id', $product['purchase_order_detail_id'])->where('storage_id', $product['storage_id'])->decrement('stock', $product['qty_select']);
                    InventoryHistory::create([
                        'purc_doc'                      => $product['purc_doc'],
                        'sales_doc'                     => $product['sales_doc'],
                        'inventory_child_id'            => $product['child_id'],
                        'inventory_child_detail_id'     => $product['child_detail_id'],
                        'outbound_id'                   => $outbound->id,
                        'outbound_detail_id'            => $outboundDetail->id,
                        'type'                          => 'outbound',
                        'qty'                           => $product['qty_select'],
                    ]);

                    foreach ($product['sn_select'] ?? [] as $sn) {
                        SerialNumber::where('inventory_child_id', $product['child_id'])
                            ->where('inventory_child_detail_id', $product['child_detail_id'])
                            ->where('serial_number', $sn)
                            ->decrement('qty', 1);

                        OutboundSerialNumber::create([
                            'outbound_id'       => $outbound->id,
                            'serial_number'     => $sn,
                        ]);
                    }
                }

                $qty_item += $product['qty_select'];
            }

            // Jika Outbound ke General Room
            if ($request->post('deliveryDest') == 'general room') {
                $generalRoom = GeneralRoom::create([
                    'outbound_id'   => $outbound->id,
                    'number'        => 'GR-'.date('ymdHis').rand(111, 999),
                    'qty'           => 0,
                    'qty_item'      => $qty_item,
                    'status'        => 'open'
                ]);

                foreach ($products as $product) {
                    GeneralRoomDetail::create([
                        'general_room_id'               => $generalRoom->id,
                        'product_id'                    => $product['product_id'],
                        'inventory_parent_id'           => $product['type'] == 'parent' ? $product['inventory_parent_id'] : null,
                        'inventory_parent_detail_id'    => $product['type'] == 'parent' ? $product['inventory_parent_detail_id'] : null,
                        'inventory_child_id'            => $product['type'] == 'child' ? $product['child_id'] : null,
                        'inventory_child_detail_id'     => $product['type'] == 'child' ? $product['child_detail_id'] : null,
                        'qty'                           => $product['qty_select'],
                        'serial_number'                 => json_encode($product['sn_select'] ?? []),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            Log::error($err->getLine());
            return response()->json([
                'status' => false,
            ]);
        }
    }
}
