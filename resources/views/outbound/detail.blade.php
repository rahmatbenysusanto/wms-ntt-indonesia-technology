@extends('layout.index')
@section('title', 'Detail Outbound')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Order Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Outbound</a></li>
                        <li class="breadcrumb-item">Order List</li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Detail Outbound</h4>
                </div>
                <div class="card-body">
                    <table>
                        <tr>
                            <td class="fw-bold">Number</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $outbound->number }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Client</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $outbound->customer->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Created Date</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ \Carbon\Carbon::parse($outbound->created_at)->translatedFormat('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Created By</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $outbound->user->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Product</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Sales Doc</th>
                                <th>Material</th>
                                <th>PO Item Desc</th>
                                <th class="text-center">QTY</th>
                                <th>Serial Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($outboundDetail as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail->inventoryPackageItem->purchaseOrderDetail->item }}</td>
                                    <td>{{ $detail->inventoryPackageItem->purchaseOrderDetail->sales_doc }}</td>
                                    <td>{{ $detail->inventoryPackageItem->purchaseOrderDetail->material }}</td>
                                    <td>{{ $detail->inventoryPackageItem->purchaseOrderDetail->po_item_desc }}</td>
                                    <td class="text-center fw-bold">{{ number_format($detail->qty) }}</td>
                                    <td>
                                        @foreach($detail->outboundDetailSn as $serialNumber)
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
