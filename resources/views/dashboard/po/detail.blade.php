@extends('layout.index')
@section('title','PO Detail')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Dashboard PO Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a>Dashboard PO Detail</a></li>
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
                    <div class="row">
                        <div class="col-6">
                            <table>
                                <tr>
                                    <td class="fw-bold">Purc Doc</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $purchaseOrder->purc_doc }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Customer</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $purchaseOrder->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created Date</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ \Carbon\Carbon::parse($purchaseOrder->created_at)->translatedFormat('d F Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-6">
                            <table>
                                <tr>
                                    <td class="fw-bold">Qty SO</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ number_format($purchaseOrder->sales_doc_qty) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Total Item</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $purchaseOrder->material_qty }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">QTY Item</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ number_format($purchaseOrder->item_qty) }}</td>
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
                    <h4 class="card-title mb-0">Sales Docs</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sales Doc</th>
                                    <th class="text-center">Total QTY PO</th>
                                    <th class="text-center">QTY Quality Control</th>
                                    <th class="text-center">Stock In Warehouse</th>
                                    <th class="text-center">QTY Outbound</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrderDetail as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $detail->sales_doc }}</td>
                                        <td class="text-center fw-bold">{{ number_format($detail->qty) }}</td>
                                        <td class="text-center fw-bold">{{ number_format($detail->qty_qc) }}</td>
                                        <td class="text-center fw-bold">{{ number_format($detail->stock) }}</td>
                                        <td class="text-center fw-bold">{{ number_format($detail->qty_outbound) }}</td>
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
