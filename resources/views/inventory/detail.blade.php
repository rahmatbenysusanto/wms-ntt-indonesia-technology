@extends('layout.index')
@section('title', 'Detail Inventory')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Inventory Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item">Product List</li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Inventory Detail</h4>
                </div>
                <div class="card-body">
                    <table>
                        <tr>
                            <td class="fw-bold">Purc Doc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $inventory->purc_doc }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Sales Doc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $inventory->sales_doc }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Storage Location</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $inventory->storage->raw }} - {{ $inventory->storage->area }} - {{ $inventory->storage->rak }} - {{ $inventory->storage->bin }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Item List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Item</th>
                                    <th>Material</th>
                                    <th>PO Item Desc</th>
                                    <th>Prod Hierarchy Desc</th>
                                    <th class="text-center">Acc Ass Cat</th>
                                    <th>Vendor name</th>
                                    <th>Customer name</th>
                                    <th>Stor Loc</th>
                                    <th>SLoc Desc</th>
                                    <th>Valuation</th>
                                    <th class="text-center">QTY</th>
                                    <th>Net Order Price</th>
                                    <th>Currency</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($inventoryDetail as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="badge bg-success-subtle text-success">Parent</span></td>
                                    <td>{{ $detail->parent->purchaseOrderDetail->item }}</td>
                                    <td>{{ $detail->parent->purchaseOrderDetail->material }}</td>
                                    <td>{{ $detail->parent->purchaseOrderDetail->po_item_desc }}</td>
                                    <td>{{ $detail->parent->purchaseOrderDetail->prod_hierarchy_desc }}</td>
                                    <td class="text-center">{{ $detail->parent->purchaseOrderDetail->acc_ass_cat }}</td>
                                    <td>{{ $detail->parent->purchaseOrderDetail->vendor_name }}</td>
                                    <td>{{ $detail->parent->purchaseOrderDetail->customer_name }}</td>
                                    <td>{{ $detail->parent->purchaseOrderDetail->stor_loc }}</td>
                                    <td>{{ $detail->parent->purchaseOrderDetail->sloc_desc }}</td>
                                    <td>{{ $detail->parent->purchaseOrderDetail->valuation }}</td>
                                    <td class="text-center fw-bold">{{ $detail->parent->qty }}</td>
                                    <td>{{ $detail->parent->purchaseOrderDetail->net_order_price }}</td>
                                    <td>{{ $detail->parent->purchaseOrderDetail->currency }}</td>
                                </tr>
                                @foreach($detail->child as $child)
                                    <tr>
                                        <td></td>
                                        <td><span class="badge bg-info-subtle text-info">Child</span></td>
                                        <td>{{ $child->purchaseOrderDetail->item }}</td>
                                        <td>{{ $child->purchaseOrderDetail->material }}</td>
                                        <td>{{ $child->purchaseOrderDetail->po_item_desc }}</td>
                                        <td>{{ $child->purchaseOrderDetail->prod_hierarchy_desc }}</td>
                                        <td class="text-center">{{ $child->purchaseOrderDetail->acc_ass_cat }}</td>
                                        <td>{{ $child->purchaseOrderDetail->vendor_name }}</td>
                                        <td>{{ $child->purchaseOrderDetail->customer_name }}</td>
                                        <td>{{ $child->purchaseOrderDetail->stor_loc }}</td>
                                        <td>{{ $child->purchaseOrderDetail->sloc_desc }}</td>
                                        <td>{{ $child->purchaseOrderDetail->valuation }}</td>
                                        <td class="text-center fw-bold">{{ $child->qty }}</td>
                                        <td>{{ $child->purchaseOrderDetail->net_order_price }}</td>
                                        <td>{{ $child->purchaseOrderDetail->currency }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
