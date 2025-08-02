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
use App\Models\InventoryItem;
use App\Models\InventoryItemSN;
use App\Models\InventoryPackage;
use App\Models\InventoryPackageItem;
use App\Models\InventoryPackageItemSN;
use App\Models\InventoryParent;
use App\Models\InventoryParentDetail;
use App\Models\Outbound;
use App\Models\OutboundDetail;
use App\Models\OutboundDetailSN;
use App\Models\OutboundSerialNumber;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\SerialNumber;
use App\Models\Storage;
use App\Services\GrRoomService;
use App\Services\PmRoomService;
use App\Services\SpareRoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OutboundController extends Controller
{
    public function __construct(
        protected GrRoomService $grRoomService,
        protected PmRoomService $pmRoomService,
        protected SpareRoomService $spareRoomService,
    ) {}

    public function index(Request $request): View
    {
        $outbound = Outbound::with('user', 'customer')->where('type', 'outbound')->latest()->paginate(10);

        $title = 'Outbound';
        return view('outbound.index', compact('title', 'outbound'));
    }

    public function create(): View
    {
        $salesDoc = InventoryPackage::with('purchaseOrder', 'storage')
            ->where('qty', '!=', 0)
            ->whereNotIn('storage_id', [2,3,4])
            ->get();

        $customer = Customer::all();

        $title = 'Outbound';
        return view('outbound.create', compact('title', 'customer', 'salesDoc'));
    }

    public function getItemBySalesDoc(Request $request): \Illuminate\Http\JsonResponse
    {
        $products = InventoryPackage::with('storage', 'inventoryPackageItem', 'inventoryPackageItem.inventoryPackageItemSn', 'inventoryPackageItem.purchaseOrderDetail', 'purchaseOrder')->where('id', $request->get('id'))->first();

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
            $customer = Customer::find($request->post('customerId'));
            $qty_item = 0;
            $qty = 0;
            $salesDocs = [];

            $outbound = Outbound::create([
                'customer_id'   => $customer->id,
                'purc_doc'      => $products[0]['purcDoc'],
                'sales_docs'    => json_encode([]),
                'outbound_date' => $request->post('outboundDate'),
                'number'        => 'INV-' . date('ymdHis') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
                'qty_item'      => 0,
                'qty'           => 0,
                'type'          => 'outbound',
                'status'        => 'close',
                'deliv_loc'     => $request->post('delivLocation'),
                'deliv_dest'    => $request->post('deliveryDest'),
                'created_by'    => Auth::id()
            ]);

            foreach ($products as $product) {
                if ($product['disable'] == 0) {
                    // Insert Outbound Detail
                    $outboundDetail = OutboundDetail::create([
                        'outbound_id'               => $outbound->id,
                        'inventory_package_item_id' => $product['inventoryPackageItemId'],
                        'qty'                       => $product['qtySelect'],
                    ]);

                    foreach ($product['serialNumber'] ?? [] as $serialNumber) {
                        OutboundDetailSN::create([
                            'outbound_detail_id'        => $outboundDetail->id,
                            'inventory_package_item_id' => $product['inventoryPackageItemId'],
                            'serial_number'             => $serialNumber['serialNumber'],
                        ]);

                        InventoryPackageItemSN::where('inventory_package_item_id', $product['inventoryPackageItemId'])
                            ->where('serial_number', $serialNumber['serialNumber'])
                            ->update([
                                'qty' => 0
                            ]);
                    }

                    // Decrement Stock
                    InventoryPackage::where('id', $product['inventoryPackageId'])->decrement('qty', $product['qtySelect']);
                    InventoryPackageItem::where('id', $product['inventoryPackageItemId'])->decrement('qty', $product['qtySelect']);

                    // Decrement Inventory
                    $inventory = Inventory::where('purchase_order_id', $product['purchaseOrderId'])->where('type', 'inv')->first();
                    Inventory::where('id', $inventory->id)->decrement('stock', $product['qtySelect']);
                    InventoryDetail::where('inventory_package_item_id', $product['inventoryPackageItemId'])->where('inventory_id', $inventory->id)->decrement('qty', $product['qtySelect']);
                    InventoryItem::where('purc_doc', $product['purcDoc'])
                        ->where('sales_doc', $product['salesDoc'])
                        ->where('storage_id', $product['storageId'])
                        ->where('product_id', $product['productId'])
                        ->where('type', 'inv')
                        ->decrement('stock', $product['qtySelect']);

                    // Inventory History
                    InventoryHistory::create([
                        'purchase_order_id'         => $product['purchaseOrderId'],
                        'purchase_order_detail_id'  => $product['purchaseOrderDetailId'],
                        'outbound_id'               => $outbound->id,
                        'inventory_package_item_id' => $product['inventoryPackageItemId'],
                        'qty'                       => $product['qtySelect'],
                        'type'                      => 'outbound',
                        'created_by'                => Auth::id(),
                        'note'                      => 'Outbound From Inventory',
                    ]);

                    $qty_item++;
                    $qty += $product['qtySelect'];
                    $salesDocs[] = $product['salesDoc'];
                }
            }

            Outbound::where('id', $outbound->id)->update([
                'qty_item'      => $qty_item,
                'qty'           => $qty,
                'sales_docs'    => json_encode(array_unique($salesDocs)),
            ]);

            if ($request->post('deliveryDest') != 'client') {
                $type = '';
                $storage = null;
                switch ($request->post('deliveryDest')) {
                    case 'general room':
                        $type = 'gr';
                        $storage = 2;
                        break;
                    case 'pm room':
                        $type = 'pm';
                        $storage = 3;
                        break;
                    case 'spare room':
                        $type = 'spare';
                        $storage = 4;
                        break;
                }

                $checkInventory = Inventory::where('purchase_order_id', $products[0]['purchaseOrderId'])->where('type', $type)->first();
                if ($checkInventory == null) {
                    $inventory = Inventory::create([
                        'purchase_order_id' => $products[0]['purchaseOrderId'],
                        'stock'             => $qty,
                        'type'              => $type,
                    ]);
                    $inventoryId = $inventory->id;
                } else {
                    Inventory::where('id', $checkInventory->id)->increment('stock', $qty);
                    $inventoryId = $checkInventory->id;
                }

                $purchaseOrder = PurchaseOrder::find($products[0]['purchaseOrderId']);
                foreach ($products as $product) {
                    if ($product['disable'] == 0) {

                        $inventoryPackageItem = InventoryPackageItem::find($product['inventoryPackageItemId']);
                        $purchaseOrderDetail = PurchaseOrderDetail::find($inventoryPackageItem->purchase_order_detail_id);

                        InventoryDetail::create([
                            'inventory_id'              => $inventoryId,
                            'purchase_order_detail_id'  => $purchaseOrderDetail->id,
                            'storage_id'                => 2,
                            'inventory_package_item_id' => $product['inventoryPackageItemId'],
                            'sales_doc'                 => $purchaseOrderDetail->sales_doc,
                            'qty'                       => $product['qtySelect']
                        ]);

                        $checkInventoryItem = InventoryItem::where('purc_doc', $purchaseOrder->purc_doc)
                            ->where('sales_doc', $purchaseOrderDetail->sales_doc)
                            ->where('product_id', $inventoryPackageItem->product_id)
                            ->where('storage_id', 2)
                            ->first();
                        if ($checkInventoryItem == null) {
                            $inventoryItem = InventoryItem::create([
                                'purc_doc'      => $purchaseOrder->purc_doc,
                                'sales_doc'     => $purchaseOrderDetail->sales_doc,
                                'product_id'    => $inventoryPackageItem->product_id,
                                'storage_id'    => 2,
                                'stock'         => $product['qtySelect'],
                                'type'          => $type
                            ]);
                            $inventoryItemId = $inventoryItem->id;
                        } else {
                            InventoryItem::where('id', $checkInventoryItem->id)->increment('stock', $product['qtySelect']);
                            $inventoryItemId = $checkInventoryItem->id;
                        }

                        foreach ($product['serialNumber'] ?? [] as $serialNumber) {
                            InventoryItemSN::create([
                                'inventory_item_id'         => $inventoryItemId,
                                'serial_number'             => $serialNumber['serialNumber'],
                                'qty'                       => 1
                            ]);
                        }

                        // Store Inventory Package
                        $findInventoryPackage = InventoryPackage::find($product['inventoryPackageId']);
                        $inventoryPackage = InventoryPackage::create([
                            'purchase_order_id'         => $purchaseOrder->id,
                            'storage_id'                => $storage,
                            'number'                    => strtoupper($type).'-'.date('ymdHis').rand(100, 999),
                            'reff_number'               => '',
                            'qty_item'                  => 1,
                            'qty'                       => $product['qtySelect'],
                            'sales_doc'                 => $purchaseOrderDetail->sales_doc,
                            'product_package_id'        => $findInventoryPackage->product_package_id,
                            'created_by'                => Auth::id()
                        ]);

                        $findInventoryPackageItem = InventoryPackageItem::find($product['inventoryPackageItemId']);
                        $inventoryPackageItem = InventoryPackageItem::create([
                            'inventory_package_id'      => $inventoryPackage->id,
                            'product_id'                => $inventoryPackageItem->product_id,
                            'purchase_order_detail_id'  => $purchaseOrderDetail->id,
                            'is_parent'                 => $findInventoryPackageItem->is_parent,
                            'direct_outbound'           => 0,
                            'qty'                       => $product['qtySelect']
                        ]);

                        foreach ($product['serialNumber'] ?? [] as $serialNumber) {
                            InventoryPackageItemSN::create([
                                'inventory_package_item_id' => $inventoryPackageItem->id,
                                'serial_number'             => $serialNumber['serialNumber'],
                                'qty'                       => 1
                            ]);
                        }
                    }
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

    public function detail(Request $request): View
    {
        $outbound = Outbound::with('user', 'customer')->where('id', $request->query('id'))->first();
        $outboundDetail = OutboundDetail::with('inventoryPackageItem', 'inventoryPackageItem.purchaseOrderDetail', 'outboundDetailSn')->where('outbound_id', $outbound->id)->get();

        $title = 'Outbound';
        return view('outbound.detail', compact('title', 'outboundDetail', 'outbound'));
    }
}
