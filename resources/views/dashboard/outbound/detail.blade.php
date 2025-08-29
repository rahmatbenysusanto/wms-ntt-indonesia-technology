@extends('layout.index')
@section('title', 'Outbound Detail')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Dashboard Outbound Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a>Dashboard Outbound Detail</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Outbound Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <table>
                                <tr>
                                    <td class="fw-bold">Delivery Note Number</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $outbound->delivery_note_number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Delivery Loc</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $outbound->deliv_loc }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Delivery Dest</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $outbound->deliv_dest }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-6">
                            <table>
                                <tr>
                                    <td class="fw-bold">Delivery Date</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $outbound->delivery_date }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created By</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $outbound->user->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $outbound->status }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Outbound Products</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sales Doc</th>
                                <th>Item</th>
                                <th>Material</th>
                                <th>PO Item Desc</th>
                                <th>Prod Hierarchy Desc</th>
                                <th class="text-center">QTY</th>
                                <th>Nominal</th>
                                <th>Serial Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($outboundDetail as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail->inventoryPackageItem->purchaseOrderDetail->sales_doc }}</td>
                                    <td>{{ $detail->inventoryPackageItem->purchaseOrderDetail->item }}</td>
                                    <td>{{ $detail->inventoryPackageItem->purchaseOrderDetail->material }}</td>
                                    <td>{{ $detail->inventoryPackageItem->purchaseOrderDetail->po_item_desc }}</td>
                                    <td>{{ $detail->inventoryPackageItem->purchaseOrderDetail->prod_hierarchy_desc }}</td>
                                    <td class="text-center fw-bold">{{ $detail->qty }}</td>
                                    <td>$ {{ number_format($detail->qty * $detail->inventoryPackageItem->purchaseOrderDetail->net_order_price) }}</td>
                                    <td>
                                        @foreach($detail->outboundDetailSN as $serialNumber)
                                            <div>{{ $serialNumber->serial_number }}</div>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
