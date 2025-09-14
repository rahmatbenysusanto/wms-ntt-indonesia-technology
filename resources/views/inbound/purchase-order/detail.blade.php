@extends('layout.index')
@section('title', 'Detail PO')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Detail Purchase Order</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item">Purchase Order</li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Purchase Order Information</h4>
                </div>
                <div class="card-body">
                    <table>
                        <tr>
                            <td class="fw-bold">Purc Doc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $purchaseOrder->purc_doc }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">PO Created Date</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ \Carbon\Carbon::parse($purchaseOrder->created_at)->translatedFormat('d F Y H:i') }} WIB</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">PO Created By</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $purchaseOrder->user->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Products</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sales Doc</th>
                                    <th>Item</th>
                                    <th>Material</th>
                                    <th>PO Item Desc</th>
                                    <th>Prod Hierarchy Desc</th>
                                    <th>Acc Ass Cat</th>
                                    <th>Vendor name</th>
                                    <th>Customer name</th>
                                    <th>Stor Loc</th>
                                    <th>SLoc Desc</th>
                                    <th>Valuation</th>
                                    <th class="text-center">PO Itm Qty</th>
                                    <th>Price (USD)</th>
                                    <th>Price (IDR)</th>
                                    <th class="text-center">QTY QC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $product->sales_doc }}</td>
                                        <td>{{ $product->item }}</td>
                                        <td>{{ $product->material }}</td>
                                        <td>{{ $product->po_item_desc }}</td>
                                        <td>{{ $product->prod_hierarchy_desc }}</td>
                                        <td>{{ $product->acc_ass_cat }}</td>
                                        <td>{{ $product->vendor_name }}</td>
                                        <td>{{ $product->customer_name }}</td>
                                        <td>{{ $product->stor_loc }}</td>
                                        <td>{{ $product->sloc_desc }}</td>
                                        <td>{{ $product->valuation }}</td>
                                        <td class="text-center fw-bold">{{ number_format($product->po_item_qty) }}</td>
                                        <td>${{ number_format($product->net_order_price) }}</td>
                                        <td>Rp{{ number_format($product->price_idr) }}</td>
                                        <td class="text-center fw-bold">{{ number_format($product->qty_qc) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
