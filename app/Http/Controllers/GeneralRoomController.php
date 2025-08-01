<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GeneralRoom;
use App\Models\GeneralRoomDetail;
use App\Models\InventoryDetail;
use App\Models\InventoryItem;
use App\Models\InventoryPackage;
use App\Models\InventoryPackageItem;
use App\Models\InventoryPackageItemSN;
use App\Models\InventoryParent;
use App\Models\Outbound;
use App\Models\OutboundDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class GeneralRoomController extends Controller
{
    public function index(): View
    {
        $generalRoom = InventoryPackage::with('purchaseOrder', 'purchaseOrder.customer', 'user')->where('storage_id', 2)
            ->where('qty', '!=', 0)
            ->paginate(10);

        $title = "General Room";
        return view('general-room.index', compact('title', 'generalRoom'));
    }

    public function detail(Request $request): View
    {
        $product = InventoryPackage::with('inventoryPackageItem', 'inventoryPackageItem.purchaseOrderDetail', 'storage', 'purchaseOrder', 'purchaseOrder.customer')
            ->where('id', $request->query('id'))
            ->first();

        $title = "General Room";
        return view('general-room.detail', compact('title', 'product'));
    }

    public function createBox(): View
    {
        $listItem = InventoryPackage::with('purchaseOrder', 'inventoryPackageItem', 'inventoryPackageItem.inventoryPackageItemSn', 'inventoryPackageItem.purchaseOrderDetail')->where('storage_id', 2)->where('qty', '!=', 0)->get();

        $title = "General Room";
        return view('general-room.create-box', compact('title', 'listItem'));
    }

    public function outboundAll(Request $request): \Illuminate\Http\JsonResponse
    {
        GeneralRoom::where('id', $request->post('id'))->update(['status' => 'outbound']);

        return response()->json([
            'status' => true
        ]);
    }

    public function outbound(): View
    {
        $generalRoom = Outbound::with('customer')->where('type', 'general room')->latest()->paginate(10);

        $title = "General Room Outbound";
        return view('general-room.outbound.index', compact('title', 'generalRoom'));
    }

    public function create(): View
    {
        $products = InventoryItem::with('product')
            ->where('type', 'gr')
            ->where('stock', '!=', 0)
            ->latest()
            ->get();

        $customer = Customer::all();

        $title = "General Room Outbound";
        return view('general-room.outbound.create', compact('title', 'customer', 'products'));
    }

    public function createBoxStore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $purchaseOrder = $request->post('purchaseOrder');
            $check = array_unique($purchaseOrder);

            if (count($check) != 1) {
                abort(400, 'Purc Doc Harus Sama!');
            }

            $inventoryPackage = InventoryPackage::create([
                'purchase_order_id'     => $purchaseOrder[0],
                'storage_id'            => 2,
                'number'                => 'GR-'.date('YmdHis').rand(111, 999),
                'reff_number'           => $request->post('boxName'),
                'qty_item'              => 0,
                'qty'                   => $request->post('qty'),
                'sales_docs'            => json_encode($request->post('salesDocs')),
                'created_by'            => Auth::id()
            ]);

            foreach ($request->post('products') as $product) {
                InventoryPackage::where('id', $product['inventoryPackageId'])->decrement('qty', $product['qtySelect']);
                InventoryPackageItem::where('id', $product['inventoryPackageItemId'])->decrement('qty', $product['qtySelect']);

                // Create Inventory Package Item
                $checkInventoryPackageItem = InventoryPackageItem::where('inventory_package_id', $inventoryPackage->id)
                    ->where('purchase_order_detail_id', $product['purchaseOrderDetailId'])
                    ->first();
                if ($checkInventoryPackageItem != null) {
                    InventoryPackageItem::where('id', $checkInventoryPackageItem->id)->increment('qty', $product['qtySelect']);
                    $inventoryPackageItemId = $checkInventoryPackageItem->id;
                } else {
                    $inventoryPackageItem = InventoryPackageItem::create([
                        'inventory_package_id'      => $inventoryPackage->id,
                        'product_id'                => $product['productId'],
                        'purchase_order_detail_id'  => $product['purchaseOrderDetailId'],
                        'is_parent'                 => $product['is_parent'],
                        'qty'                       => $product['qtySelect']
                    ]);
                    $inventoryPackageItemId = $inventoryPackageItem->id;
                }

                foreach ($product['serialNumber'] ?? [] as $serialNumber) {
                    InventoryPackageItemSN::where('serial_number', $serialNumber['serial_number'])
                        ->where('inventory_package_item_id', $product['inventoryPackageItemId'])
                        ->update([
                            'qty' => 0
                        ]);

                    InventoryPackageItemSN::create([
                        'inventory_package_item_id' => $inventoryPackageItemId,
                        'serial_number'             => $serialNumber['serial_number'],
                        'qty'                       => 1
                    ]);
                }
            }

            $qty_item = InventoryPackageItem::where('inventory_package_id', $inventoryPackage->id)->count();
            InventoryPackage::where('id', $inventoryPackage->id)->update([
                'qty_item' => $qty_item
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $err) {
            DB::rollBack();
            Log::error($err->getMessage());
            Log::error($err->getTraceAsString());
            Log::error($err->getLine());
            return response()->json([
                'status' => false,
            ]);
        }
    }
}

































