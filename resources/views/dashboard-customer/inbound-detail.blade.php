@extends('layout.index')
@section('title', 'Inbound Detail')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card p-2">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex gap-2">
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-dark">Main Dashboard</a>
                            <a href="{{ route('customer.inbound') }}" class="btn btn-info">Inbound</a>
                            <a href="{{ route('customer.aging') }}" class="btn btn-dark">Aging</a>
                            <a href="{{ route('customer.outbound') }}" class="btn btn-dark">Outbound</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <table>
                                <tr>
                                    <td class="fw-bold">Purchase Doc</td>
                                    <td class="fw-bold ms-2">:</td>
                                    <td class="ms-1">{{ $purchaseOrder->purc_doc }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Customer</td>
                                    <td class="fw-bold ms-2">:</td>
                                    <td class="ms-1">{{ $purchaseOrder->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Vendor</td>
                                    <td class="fw-bold ms-2">:</td>
                                    <td class="ms-1">{{ $purchaseOrder->vendor->name }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4">
                            <table>
                                <tr>
                                    <td class="fw-bold">QTY Material</td>
                                    <td class="fw-bold ms-2">:</td>
                                    <td class="ms-1">{{ number_format($purchaseOrder->qty_material) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">QTY Item</td>
                                    <td class="fw-bold ms-2">:</td>
                                    <td class="ms-1">{{ number_format($purchaseOrder->qty_item) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4">
                            <table>
                                <tr>
                                    <td class="fw-bold">Created By</td>
                                    <td class="fw-bold ms-2">:</td>
                                    <td class="ms-1">{{ $purchaseOrder->user->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created Date</td>
                                    <td class="fw-bold ms-2">:</td>
                                    <td class="ms-1">{{ \Carbon\Carbon::parse($purchaseOrder->created_at)->translatedFormat('d F Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Purchase Order Detail</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sales Doc</th>
                                    <th>Item</th>
                                    <th>Material</th>
                                    <th class="text-center">QTY</th>
                                    <th>Price</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($purchaseOrderDetail as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->sales_doc }}</td>
                                    <td>{{ $item->item }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $item->material }}</div>
                                        <div>{{ $item->po_item_desc }}</div>
                                        <div>{{ $item->prod_hierarchy_desc }}</div>
                                    </td>
                                    <td class="text-center">{{ number_format($item->po_item_qty) }}</td>
                                    <td>${{ number_format($item->po_item_qty * $item->net_order_price, 2) }}</td>
                                    <td class="text-center">
                                        @if($item->status == 'qc')
                                            <span class="badge bg-success">QC</span>
                                        @else
                                            <span class="badge bg-warning">Waiting QC</span>
                                        @endif
                                    </td>
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
