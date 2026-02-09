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
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\SerialNumber;
use App\Models\Storage;
use App\Services\GrRoomService;
use App\Services\PmRoomService;
use App\Services\SpareRoomService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OutboundController extends Controller
{
    public function __construct(
        protected GrRoomService $grRoomService,
        protected PmRoomService $pmRoomService,
        protected SpareRoomService $spareRoomService,
    ) {}

    public function index(Request $request): View
    {
        $outbound = Outbound::with('user', 'customer')
            ->where('type', 'outbound')
            ->when($request->query('purcDoc'), function ($query) use ($request) {
                $query->where('purc_doc', 'like', '%' . $request->query('purcDoc') . '%');
            })
            ->when($request->query('salesDoc'), function ($query) use ($request) {
                $query->where('sales_doc', 'like', '%' . $request->query('salesDoc') . '%');
            })
            ->when($request->query('client'), function ($query) use ($request) {
                $query->where('customer_id', $request->query('client'));
            })
            ->when($request->query('start'), function ($query) use ($request) {
                $query->whereBetween('outbound_date', [$request->query('start'), $request->query('end')]);
            })
            ->latest()
            ->paginate(10)
            ->appends([
                'start'     => $request->query('start'),
                'end'       => $request->query('end'),
                'purcDoc'   => $request->query('purcDoc'),
                'salesDoc'  => $request->query('salesDoc'),
                'client'    => $request->query('client'),
            ]);

        $customer = Customer::all();

        $title = 'Outbound';
        return view('outbound.index', compact('title', 'outbound', 'customer'));
    }

    public function create(): View
    {
        $salesDoc = InventoryPackage::with('purchaseOrder', 'storage', 'purchaseOrder.customer')
            ->where('qty', '!=', 0)
            ->whereNotIn('storage_id', [2, 3, 4])
            ->get();

        $customer = Customer::all();

        $title = 'Outbound';
        return view('outbound.create', compact('title', 'customer', 'salesDoc'));
    }

    public function getItemBySalesDoc(Request $request): \Illuminate\Http\JsonResponse
    {
        $products = InventoryPackage::with('storage', 'inventoryPackageItem', 'inventoryPackageItem.purchaseOrderDetail', 'inventoryPackageItem.inventoryPackageItemSn', 'inventoryPackageItem.purchaseOrderDetail', 'purchaseOrder')
            ->where('id', $request->get('id'))
            ->first();

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
                'prod_hierarchy' => $item->prod_hierarchy_desc,
                'qty'           => $item->qty,
                'qty_select'    => $item->qty,
                'type'          => 'parent',
                'purchase_order_detail_id' => $item->purchase_order_detail_id,
                'pa_number'     => $inventoryParent->pa_reff_number ?? $inventoryParent->pa_number,
                'item'          => $item->item,
                'purc_doc'      => $item->purc_doc,
                'storage'       => $storage->id == 1 ? '-' : ($storage->raw . ' - ' . $storage->area . ' - ' . $storage->rak . ' - ' . $storage->bin),
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
                'prod_hierarchy' => $item->prod_hierarchy_desc,
                'qty'           => $item->qty,
                'qty_select'    => $item->qty,
                'type'          => 'child',
                'purchase_order_detail_id' => $item->purchase_order_detail_id,
                'pa_number'     => $inventoryParent->pa_reff_number ?? $inventoryParent->pa_number,
                'item'          => $item->item,
                'purc_doc'      => $item->purc_doc,
                'storage'       => $storage->id == 1 ? '-' : ($storage->raw . ' - ' . $storage->area . ' - ' . $storage->rak . ' - ' . $storage->bin),
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
            Log::channel('outbound')->info('Outbound Store Process Started', ['user_id' => Auth::id()]);

            $products = $request->post('products');
            $customer = Customer::find($request->post('customerId'));
            $qty_item = 0;
            $qty = 0;
            $salesDocs = [];

            $deliveryNoteNumber = $request->post('deliveryNoteNumber');
            if (empty($deliveryNoteNumber)) {
                $deliveryNoteNumber = $this->generateDeliveryNoteNumber();
            }

            $outbound = Outbound::create([
                'customer_id'   => $customer->id,
                'purc_doc'      => $products[0]['purcDoc'],
                'sales_docs'    => json_encode([]),
                'outbound_date' => $request->post('deliveryDate'),
                'number'        => 'INV-' . date('ymdHis') . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
                'qty_item'      => 0,
                'qty'           => 0,
                'type'          => 'outbound',
                'status'        => 'outbound',
                'deliv_loc'     => $request->post('delivLocation'),
                'deliv_dest'    => $request->post('deliveryDest'),
                'delivery_date' => $request->post('deliveryDate'),
                'delivery_note_number' => $deliveryNoteNumber,
                'created_by'    => Auth::id()
            ]);

            foreach ($products as $product) {
                if ($product['disable'] == 0 && $product['qtySelect'] != 0 && $product['qtySelect'] <= $product['qty']) {
                    // Insert Outbound Detail
                    $outboundDetail = OutboundDetail::create([
                        'outbound_id'               => $outbound->id,
                        'inventory_package_item_id' => $product['inventoryPackageItemId'],
                        'qty'                       => $product['qtySelect'],
                    ]);

                    $serialNumber = [];
                    foreach ($product['serialNumber'] ?? [] as $sn) {
                        OutboundDetailSN::create([
                            'outbound_detail_id'        => $outboundDetail->id,
                            'inventory_package_item_id' => $product['inventoryPackageItemId'],
                            'serial_number'             => $sn['serialNumber'],
                        ]);

                        InventoryPackageItemSN::where('inventory_package_item_id', $product['inventoryPackageItemId'])
                            ->where('serial_number', $sn['serialNumber'])
                            ->where('id', $sn['id'])
                            ->update([
                                'qty' => 0
                            ]);

                        $serialNumber[] = $sn['serialNumber'];
                    }

                    // Decrement Stock
                    InventoryPackage::where('id', $product['inventoryPackageId'])->decrement('qty', $product['qtySelect']);
                    InventoryPackageItem::where('id', $product['inventoryPackageItemId'])->decrement('qty', $product['qtySelect']);

                    // Decrement Inventory
                    $inventory = Inventory::where('purchase_order_id', $product['purchaseOrderId'])->where('type', 'inv')->first();
                    Inventory::where('id', $inventory->id)->decrement('stock', $product['qtySelect']);
                    InventoryDetail::where('inventory_package_item_id', $product['inventoryPackageItemId'])->where('inventory_id', $inventory->id)->decrement('qty', $product['qtySelect']);

                    // Inventory History
                    InventoryHistory::create([
                        'purchase_order_id'         => $product['purchaseOrderId'],
                        'purchase_order_detail_id'  => $product['purchaseOrderDetailId'],
                        'outbound_id'               => $outbound->id,
                        'inventory_package_item_id' => $product['inventoryPackageItemId'],
                        'qty'                       => $product['qtySelect'],
                        'type'                      => 'outbound',
                        'serial_number'             => json_encode($serialNumber),
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
                    Inventory::create([
                        'purchase_order_id' => $products[0]['purchaseOrderId'],
                        'stock'             => $qty,
                        'type'              => $type,
                    ]);
                } else {
                    Inventory::where('id', $checkInventory->id)->increment('stock', $qty);
                }

                $purchaseOrder = PurchaseOrder::find($products[0]['purchaseOrderId']);
                // Store Inventory Package
                $findInventoryPackage = InventoryPackage::find($products[0]['inventoryPackageId']);
                $inventoryPackage = InventoryPackage::create([
                    'purchase_order_id'         => $purchaseOrder->id,
                    'storage_id'                => $storage,
                    'number'                    => strtoupper($type) . '-' . date('ymdHis') . rand(100, 999),
                    'reff_number'               => '',
                    'qty_item'                  => $qty_item,
                    'qty'                       => $qty,
                    'sales_docs'                => json_encode(array_unique($salesDocs)),
                    'product_package_id'        => $findInventoryPackage->product_package_id,
                    'created_by'                => Auth::id()
                ]);

                foreach ($products as $product) {
                    if ($product['disable'] == 0 && $product['qtySelect'] != 0 && $product['qtySelect'] <= $product['qty']) {
                        $inventoryPackageItem = InventoryPackageItem::find($product['inventoryPackageItemId']);
                        $purchaseOrderDetail = PurchaseOrderDetail::find($inventoryPackageItem->purchase_order_detail_id);

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

                        // Inventory Detail
                        $productAging = InventoryDetail::where('inventory_package_item_id', $product['inventoryPackageItemId'])
                            ->where('purchase_order_detail_id', $product['purchaseOrderDetailId'])
                            ->first();
                        $checkInventoryDetail = InventoryDetail::where('storage_id', $storage)
                            ->where('purchase_order_detail_id', $product['purchaseOrderDetailId'])
                            ->where('inventory_package_item_id', $inventoryPackageItem->id)
                            ->whereDate('aging_date', Carbon::parse($productAging->aging_date)->format('Y-m-d'))
                            ->first();
                        if ($checkInventoryDetail == null) {
                            $inventory = Inventory::where('purchase_order_id', $product['purchaseOrderId'])->where('type', $type)->first();
                            InventoryDetail::create([
                                'inventory_id'              => $inventory->id,
                                'purchase_order_detail_id'  => $product['purchaseOrderDetailId'],
                                'storage_id'                => $storage,
                                'inventory_package_item_id' => $inventoryPackageItem->id,
                                'sales_doc'                 => $product['salesDoc'],
                                'qty'                       => 1,
                                'aging_date'                => $productAging->aging_date,
                            ]);
                        } else {
                            InventoryDetail::where('id', $checkInventoryDetail->id)->increment('qty', $product['qtySelect']);
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
            Log::channel('outbound')->error('Outbound Store Process Failed: ' . $err->getMessage());
            Log::error($err->getMessage());
            Log::error($err->getLine());
            return response()->json([
                'status' => false,
            ]);
        }
    }
    public function cancel(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            Log::channel('outbound')->info('Outbound Cancel Process Started', ['id' => $request->query('id'), 'user_id' => Auth::id()]);

            $id = $request->query('id');
            $outbound = Outbound::find($id);

            if (!$outbound) {
                return back()->with('error', 'Outbound not found');
            }

            // Get Outbound Details
            $outboundDetails = OutboundDetail::where('outbound_id', $id)->get();

            foreach ($outboundDetails as $detail) {
                // Restore Stock
                // 1. InventoryPackageItem
                InventoryPackageItem::where('id', $detail->inventory_package_item_id)->increment('qty', $detail->qty);

                // 2. InventoryPackage
                $invPackageItem = InventoryPackageItem::find($detail->inventory_package_item_id);
                if ($invPackageItem) {
                    InventoryPackage::where('id', $invPackageItem->inventory_package_id)->increment('qty', $detail->qty);

                    $invPackage = InventoryPackage::find($invPackageItem->inventory_package_id);
                    $inventory = Inventory::where('purchase_order_id', $invPackage->purchase_order_id)->where('type', 'inv')->first();

                    if ($inventory) {
                        // 3. Inventory
                        Inventory::where('id', $inventory->id)->increment('stock', $detail->qty);

                        // 4. InventoryDetail
                        InventoryDetail::where('inventory_package_item_id', $detail->inventory_package_item_id)
                            ->where('inventory_id', $inventory->id)
                            ->increment('qty', $detail->qty);
                    }

                    // Restore Serial Numbers
                    $sns = OutboundDetailSN::where('outbound_detail_id', $detail->id)->get();
                    $snList = [];
                    foreach ($sns as $sn) {
                        InventoryPackageItemSN::where('inventory_package_item_id', $detail->inventory_package_item_id)
                            ->where('serial_number', $sn->serial_number)
                            ->update(['qty' => 1]);
                        $snList[] = $sn->serial_number;
                    }

                    // Log History
                    InventoryHistory::create([
                        'purchase_order_id'         => $invPackage->purchase_order_id,
                        'purchase_order_detail_id'  => $invPackageItem->purchase_order_detail_id,
                        'outbound_id'               => $outbound->id,
                        'inventory_package_item_id' => $detail->inventory_package_item_id,
                        'qty'                       => $detail->qty,
                        'type'                      => 'inbound', // Back to inventory
                        'serial_number'             => json_encode($snList),
                        'created_by'                => Auth::id(),
                        'note'                      => 'Outbound ' . $outbound->number . ' Cancelled',
                    ]);
                }

                // Delete SNs from Outbound
                OutboundDetailSN::where('outbound_detail_id', $detail->id)->delete();

                // Delete Detail
                $detail->delete();
            }

            // Delete Outbound
            $outbound->delete();

            DB::commit();
            return back()->with('success', 'Outbound Cancelled Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('outbound')->error('Outbound Cancel Process Failed: ' . $e->getMessage());
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to cancel Outbound');
        }
    }
    public function detail(Request $request): View
    {
        $outbound = Outbound::with('user', 'customer')->where('id', $request->query('id'))->first();
        $outboundDetail = OutboundDetail::with('inventoryPackageItem', 'inventoryPackageItem.purchaseOrderDetail', 'outboundDetailSn', 'inventoryPackageItem.inventoryPackage', 'inventoryPackageItem.inventoryPackage.storage')->where('outbound_id', $outbound->id)->get();

        $title = 'Outbound';
        return view('outbound.detail', compact('title', 'outboundDetail', 'outbound'));
    }

    public function return(): View
    {
        $outbound = Outbound::where('type', 'outbound')->where('status', 'outbound')->get();

        $storageRaw = Storage::whereNotIn('id', [1, 2, 3, 4])
            ->where('raw', '!=', '-')
            ->whereNull('area')
            ->whereNull('rak')
            ->whereNull('bin')
            ->get();

        $dataMasterBox = InventoryPackage::whereNotIn('storage_id', [1, 2, 3, 4])
            ->where('qty', '!=', 0)
            ->get();

        $masterBox = [];
        foreach ($dataMasterBox as $box) {
            $explode = explode("-", $box->number);
            $masterBox[] = $explode[0] . '-' . $explode[1];
        }
        $masterBox = array_unique($masterBox);

        $title = 'Outbound';
        return view('outbound.return', compact('title', 'storageRaw', 'masterBox', 'outbound'));
    }

    public function returnGetProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        $outboundDetail = OutboundDetail::with('inventoryPackageItem', 'inventoryPackageItem.purchaseOrderDetail', 'outboundDetailSN')->where('outbound_id', $request->get('id'))->get();

        return response()->json([
            'data' => $outboundDetail,
        ]);
    }

    public function returnStore(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            Log::channel('outbound')->info('Outbound Return Process Started', ['user_id' => Auth::id()]);

            $products = $request->post('products');

            $returnOutbound = Outbound::find($products[0]['outboundId']);
            $purchaseOrder = PurchaseOrder::where('purc_doc', $returnOutbound->purc_doc)->first();

            $deliveryNoteNumber = $request->post('reffNumber');
            if (empty($deliveryNoteNumber)) {
                $deliveryNoteNumber = $this->generateDeliveryNoteNumber();
            }

            $outbound = Outbound::create([
                'customer_id'   => $returnOutbound->customer_id,
                'purc_doc'      => $returnOutbound->purc_doc,
                'sales_docs'    => json_encode([]),
                'outbound_date' => $request->post('outboundDate') ?? date('Y-m-d H:i:s'),
                'number'        => 'inv-' . date('YmdHis') . rand(111, 999),
                'qty_item'      => 0,
                'qty'           => 0,
                'type'          => 'outbound',
                'status'        => 'return',
                'deliv_loc'     => '-',
                'deliv_dest'    => '-',
                'note'          => $request->post('note'),
                'delivery_date' => $request->post('outboundDate') ?? date('Y-m-d H:i:s'),
                'delivery_note_number' => $deliveryNoteNumber,
                'created_by'    => Auth::id(),
            ]);

            $inventoryPackage = InventoryPackage::create([
                'purchase_order_id' => $purchaseOrder->id,
                'storage_id'        => $request->post('bin'),
                'number'            => $request->post('masterBox') != null ? $request->post('masterBox') : 'PA-' . date('YmdHis') . rand(111, 999),
                'reff_number'       => $request->post('boxName'),
                'qty'               => 0,
                'qty_item'          => 0,
                'sales_docs'        => json_encode([]),
                'note'              => $request->post('note'),
                'return'            => 1,
                'return_from'       => 'outbound',
                'created_by'        => Auth::id(),
            ]);

            $qty = 0;
            $salesDocs = [];
            $qtyItem = [];
            foreach ($products as $product) {
                $outboundDetail = OutboundDetail::create([
                    'outbound_id'               => $outbound->id,
                    'inventory_package_item_id' => $product['inventoryPackageItemId'],
                    'qty'                       => $product['qtySelect'],
                ]);

                // Insert to Inventory
                Inventory::where('purchase_order_id', $purchaseOrder->id)->where('type', 'inv')->increment('stock', $product['qtySelect']);
                // InventoryDetail
                $inventory = Inventory::where('purchase_order_id', $purchaseOrder->id)->where('type', 'inv')->first();

                $inventoryPackageItem = InventoryPackageItem::create([
                    'inventory_package_id'      => $inventoryPackage->id,
                    'product_id'                => $product['productId'],
                    'purchase_order_detail_id'  => $product['purchaseOrderDetailId'],
                    'is_parent'                 => $product['isParent'],
                    'qty'                       => $product['qtySelect'],
                ]);

                InventoryDetail::create([
                    'inventory_id'              => $inventory->id,
                    'purchase_order_detail_id'  => $product['purchaseOrderDetailId'],
                    'storage_id'                => $request->post('bin'),
                    'inventory_package_item_id' => $inventoryPackageItem->id,
                    'sales_doc'                 => $product['salesDoc'],
                    'qty'                       => $product['qtySelect'],
                    'aging_date'                => date('Y-m-d'),
                ]);

                foreach ($product['serialNumber'] ?? [] as $serialNumber) {
                    InventoryPackageItemSN::create([
                        'inventory_package_item_id' => $inventoryPackageItem->id,
                        'serial_number'             => $serialNumber['serialNumber'],
                        'qty'                       => 1,
                    ]);

                    OutboundDetailSN::create([
                        'outbound_detail_id'        => $outboundDetail->id,
                        'inventory_package_item_id' => $product['inventoryPackageItemId'],
                        'serial_number'             => $serialNumber['serialNumber'],
                    ]);
                }

                $qty += $product['qtySelect'];
                $salesDocs[] = $product['salesDoc'];
                $qtyItem[] = $product['productId'];
            }

            Outbound::where('id', $outbound->id)->update([
                'qty_item'      => count(array_unique($qtyItem)),
                'qty'           => $qty,
                'sales_docs'    => json_encode($salesDocs),
            ]);

            InventoryPackage::where('id', $inventoryPackage->id)->update([
                'qty_item'      => count(array_unique($qtyItem)),
                'qty'           => $qty,
                'sales_docs'    => json_encode($salesDocs),
            ]);

            DB::commit();
            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $err) {
            DB::rollBack();
            Log::channel('outbound')->error('Outbound Return Process Failed: ' . $err->getMessage());
            Log::error($err->getMessage());
            Log::error($err->getLine());
            return response()->json([
                'status' => false,
            ]);
        }
    }

    public function downloadPdf(Request $request): \Illuminate\Http\Response
    {
        $outbound = Outbound::with('customer', 'inventoryPackageItem', 'inventoryPackageItem.inventoryPackage', 'inventoryPackageItem.inventoryPackage.storage')->where('id', $request->query('id'))->first();
        $outboundDetail = OutboundDetail::with('inventoryPackageItem', 'inventoryPackageItem.purchaseOrderDetail', 'outboundDetailSN')->where('outbound_id', $request->query('id'))->get();

        $data = [
            'outbound'          => $outbound,
            'outboundDetail'    => $outboundDetail,
        ];

        $pdf = Pdf::loadView('pdf.outbound', $data)->setPaper('A4', 'landscape');
        return $pdf->stream('outbound ' . $outbound->delivery_note_number . '.pdf');
    }

    public function downloadExcel(Request $request): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $outbound = Outbound::with('customer')
            ->where('id', $request->query('id'))
            ->firstOrFail();

        $sheet->setCellValue('A1', 'From');
        $sheet->setCellValue('A2', 'NTT Global Technology');

        $sheet->setCellValue('C1', 'To');
        $sheet->setCellValue('C2', $outbound->customer->name);

        $sheet->setCellValue('E1', 'No : ' . $outbound->delivery_note_number);
        $sheet->setCellValue('E2', $outbound->created_at);

        $outboundDetail = OutboundDetail::with([
            'inventoryPackageItem',
            'inventoryPackageItem.purchaseOrderDetail',
            'outboundDetailSN'
        ])
            ->where('outbound_id', $request->query('id'))
            ->get();

        $sheet->setCellValue('A7', 'No');
        $sheet->setCellValue('B7', 'Product');
        $sheet->setCellValue('C7', 'Description');
        $sheet->setCellValue('D7', 'QTY');
        $sheet->setCellValue('E7', 'Serial Number');

        $row = 8;
        $no  = 1;

        foreach ($outboundDetail as $detail) {
            $pod = optional(optional($detail->inventoryPackageItem)->purchaseOrderDetail);

            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, (string) $pod->sales_doc);

            $descLines = [
                'Material: ' . (string) $pod->material,
                'PO Item Desc: ' . (string) $pod->po_item_desc,
                'Hierarchy Desc: ' . (string) $pod->prod_hierarchy_desc,
            ];
            $sheet->setCellValue('C' . $row, implode("\n", $descLines));
            $sheet->getStyle('C' . $row)->getAlignment()->setWrapText(true);
            $sheet->setCellValue('D' . $row, (float) $detail->qty);
            $snList = [];
            foreach ($detail->outboundDetailSN as $sn) {
                $snList[] = (string) $sn->serial_number;
            }
            $sheet->setCellValue('E' . $row, implode("\n", $snList));
            $sheet->getStyle('E' . $row)->getAlignment()->setWrapText(true);

            $row++;
            $no++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            if (ob_get_length()) {
                @ob_end_clean();
            }
            $writer->save('php://output');
        });

        $fileName = 'Report Outbound' . date('Y-m-d_H-i-s') . '.xlsx';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    // Mobile APP
    public function indexMobile(Request $request): View
    {
        $outbound = DB::table('outbound')
            ->leftJoin('outbound_detail', 'outbound_detail.outbound_id', '=', 'outbound.id')
            ->leftJoin('inventory_package_item', 'inventory_package_item.id', '=', 'outbound_detail.inventory_package_item_id')
            ->leftJoin('purchase_order_detail', 'purchase_order_detail.id', '=', 'inventory_package_item.purchase_order_detail_id')
            ->leftJoin('customer', 'customer.id', '=', 'outbound.customer_id')
            ->when($request->query('purcDoc'), function ($query) use ($request) {
                $query->where('outbound.purc_doc', 'LIKE', '%' . $request->query('purcDoc') . '%');
            })
            ->when($request->query('salesDoc'), function ($query) use ($request) {
                $query->where('outbound.sales_docs', 'LIKE', '%' . $request->query('salesDoc') . '%');
            })
            ->when($request->query('material'), function ($query) use ($request) {
                $query->where('purchase_order_detail.material', 'LIKE', '%' . $request->query('material') . '%');
            })
            ->when($request->query('customer'), function ($query) use ($request) {
                $query->where('customer.name', 'LIKE', '%' . $request->query('customer') . '%');
            })
            ->select([
                'outbound.id',
                'outbound.number',
                'outbound.purc_doc',
                'outbound.sales_docs',
                'outbound.delivery_date',
                'outbound.qty',
                'outbound.status',
                'outbound.deliv_loc',
                'outbound.deliv_dest',
                'outbound.delivery_note_number',
                DB::raw('SUM(outbound_detail.qty * purchase_order_detail.net_order_price) as nominal')
            ])
            ->groupBy([
                'outbound.id',
                'outbound.number',
                'outbound.purc_doc',
                'outbound.sales_docs',
                'outbound.delivery_date',
                'outbound.qty',
                'outbound.status',
                'outbound.deliv_loc',
                'outbound.deliv_dest',
                'outbound.delivery_note_number',
            ])
            ->latest('outbound.delivery_date')
            ->paginate(5)
            ->appends([
                'purcDoc'   => $request->query('purcDoc'),
                'salesDoc'  => $request->query('salesDoc'),
                'material'  => $request->query('material'),
                'customer'  => $request->query('customer'),
                'search'    =>  $request->query('search'),
            ]);

        $customer = Customer::all();
        $products = Product::all();

        return view('mobile.outbound.index', compact('outbound', 'customer', 'products'));
    }

    public function indexDetailMobile(Request $request): View
    {
        $outbound = Outbound::with('user', 'customer')->where('id', $request->query('id'))->first();
        $outboundDetail = OutboundDetail::with('inventoryPackageItem', 'inventoryPackageItem.purchaseOrderDetail', 'inventoryPackageItem.purchaseOrderDetail.purchaseOrder')->where('outbound_id', $request->query('id'))->get();

        return view('mobile.outbound.detail', compact('request', 'outbound', 'outboundDetail'));
    }

    public function indexDetailSnMobile(Request $request): View
    {
        $outboundId = $request->query('outbound');
        $serialNumber = OutboundDetailSN::where('outbound_detail_id', $request->query('id'))->get();

        return view('mobile.outbound.sn', compact('request', 'serialNumber', 'outboundId'));
    }

    public function reportDownloadPdf(Request $request): \Illuminate\Http\Response
    {
        $outbound = Outbound::with('outboundDetail', 'outboundDetail.outboundDetailSN', 'outboundDetail.inventoryPackageItem', 'outboundDetail.inventoryPackageItem.purchaseOrderDetail', 'customer')
            ->when($request->query('purcDoc'), function ($query) use ($request) {
                $query->where('purc_doc', 'LIKE', '%' . $request->query('purcDoc') . '%');
            })->when($request->query('salesDoc'), function ($query) use ($request) {
                $query->where('sales_docs', 'LIKE', '%' . $request->query('salesDoc') . '%');
            })->when($request->query('client'), function ($query) use ($request) {
                $query->where('customer_id', $request->query('client'));
            })
            ->whereBetween('delivery_date', [$request->query('start'), $request->query('end')])
            ->where('type', 'outbound')
            ->get();

        $data = [
            'outbound'          => $outbound,
        ];

        $pdf = Pdf::loadView('pdf.outbound-list', $data)->setPaper('a4', 'landscape');
        return $pdf->stream('Report Outbound.pdf');
    }

    public function reportDownloadExcel(Request $request) {}

    private function generateDeliveryNoteNumber()
    {
        $now = Carbon::now();
        $monthShort = strtoupper($now->format('M'));
        $romanMonth = $this->getRomanMonth($now->month);
        $year = $now->year;

        // Find the count of outbounds in the current month with this pattern
        $pattern = "%-TKS-WMS-{$monthShort}-{$romanMonth}-{$year}";
        $count = Outbound::where('delivery_note_number', 'like', $pattern)->count();
        $nextNumber = $count + 1;

        return str_pad($nextNumber, 3, '0', STR_PAD_LEFT) . "-TKS-WMS-{$monthShort}-{$romanMonth}-{$year}";
    }

    private function getRomanMonth($month)
    {
        $romans = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        return $romans[$month] ?? $month;
    }
}
