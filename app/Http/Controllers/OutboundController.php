<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\InventoryDetail;
use App\Models\InventoryHistory;
use App\Models\Outbound;
use App\Models\OutboundDetail;
use App\Models\OutboundSerialNumber;
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
        $inventory = Inventory::whereNot('qty_item', 0)
            ->select([
                'sales_doc',
                DB::raw('MAX(purc_doc) AS purc_doc'),
            ])
            ->groupBy('sales_doc')
            ->get();

        $customer = Customer::all();

        $title = 'Outbound';
        return view('outbound.create', compact('title', 'inventory', 'customer'));
    }

    public function getItemBySalesDoc(Request $request): \Illuminate\Http\JsonResponse
    {
        $products = Inventory::with('storage')
            ->where('sales_doc', $request->get('salesDoc'))
            ->where('qty_item', '!=', 0)
            ->get();

        foreach ($products as $product) {
            $listParent = InventoryDetail::with('purchaseOrderDetail')->where('inventory_id', $product->id)->where('type', 'parent')->get();
            foreach ($listParent as $parent) {
                $parent->child = InventoryDetail::with('purchaseOrderDetail')
                    ->where('parent_id', $parent->id)
                    ->where('type', 'child')
                    ->get();
            }

            $product->products = $listParent;
        }

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

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $listItem = $request->post('listItem');

            $customer = Customer::find($request->post('customerId'));

            $outbound = Outbound::create([
                'number'        => 'INV-' . date('ymd') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
                'purc_doc'      => $listItem[0]['purcDoc'],
                'sales_doc'     => $listItem[0]['salesDoc'],
                'client'        => $customer->name,
                'customer_id'   => $customer->id,
                'deliv_loc'     => $request->post('delivLocation'),
                'qty_item'      => 0,
                'created_by'    => Auth::id()
            ]);

            $qty_item = 0;
            foreach ($listItem as $item) {
                $outboundDetail = OutboundDetail::create([
                    'outbound_id'           => $outbound->id,
                    'product_id'            => $item['productId'],
                    'inventory_id'          => $item['inventoryId'],
                    'inventory_detail_id'   => $item['id'],
                    'qty'                   => $item['qty']
                ]);

                foreach ($item['sn'] ?? [] as $sn) {
                    OutboundSerialNumber::create([
                        'outbound_detail_id'    => $outboundDetail->id,
                        'serial_number'         => $sn['serialNumber'],
                    ]);
                }

                Inventory::where('id', $item['inventoryId'])->decrement('qty_item', $item['qty']);
                InventoryDetail::where('id', $item['id'])->decrement('qty', $item['qty']);

                InventoryHistory::create([
                    'inventory_id'              => $item['inventoryId'],
                    'inventory_detail_id'       => $item['id'],
                    'quality_control_detail_id' => $item['qualityControlDetailId'],
                    'outbound_id'               => $outbound->id,
                    'outbound_detail_id'        => $outboundDetail->id,
                    'type'                      => 'outbound',
                    'qty'                       => $item['qty']
                ]);

                $qty_item += $item['qty'];
            }

            Outbound::where('id', $outbound->id)->update(['qty_item' => $qty_item]);

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
