@extends('layout.index')
@section('title', 'Quality Control')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quality Control</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item active">Quality Control</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('inbound.purchase-order-upload') }}" class="btn btn-info">Upload PO Excel</a>
                    </div>
                </div>
                <div class="card-header">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row">
                            <div class="col-2">
                                <label class="form-label">Purc Doc</label>
                                <input type="text" class="form-control" value="{{ request()->get('purcDoc', null) }}" name="purcDoc">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Vendor</label>
                                <select class="form-control" name="vendor">
                                    <option value="">-- Select Vendor --</option>
                                    @foreach($vendor as $item)
                                        <option value="{{ $item->id }}" {{ request()->get('vendor') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Customer</label>
                                <select class="form-control" name="customer">
                                    <option value="">-- Select Customer --</option>
                                    @foreach($customer as $item)
                                        <option value="{{ $item->id }}" {{ request()->get('customer') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Created Date</label>
                                <input type="date" class="form-control" value="{{ request()->get('date', null) }}" name="date">
                            </div>
                            <div class="col-2">
                                <label class="form-label text-white">-</label>
                                <div>
                                    <button type="submit" class="btn btn-info">Search</button>
                                    <a href="{{ route('inbound.quality-control') }}" class="btn btn-danger">Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Pur Doc</th>
                                <th>Vendor</th>
                                <th>Customer</th>
                                <th class="text-center">Sales Docs Qty</th>
                                <th class="text-center">Material Qty</th>
                                <th class="text-center">Item Qty</th>
                                <th class="text-center">Status</th>
                                <th>PO Created Date</th>
                                <th>Po Created By</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($purchaseOrder as $index => $po)
                                <tr>
                                    <td>{{ $purchaseOrder->firstItem() + $index }}</td>
                                    <td><a href="{{ route('inbound.purchase-order-detail', ['id' => $po->id]) }}">{{ $po->purc_doc }}</a></td>
                                    <td>{{ $po->vendor->name }}</td>
                                    <td>{{ $po->customer->name }}</td>
                                    <td class="text-center">{{ number_format($po->sales_doc_qty) }}</td>
                                    <td class="text-center">{{ number_format($po->material_qty) }}</td>
                                    <td class="text-center">{{ number_format($po->item_qty) }}</td>
                                    <td class="text-center">
                                        @switch($po->status)
                                            @case('new')
                                                <span class="badge bg-success-subtle text-success">New</span>
                                                @break
                                            @case('open')
                                                <span class="badge bg-info-subtle text-info">Open</span>
                                                @break
                                            @case('process')
                                                <span class="badge bg-primary-subtle text-primary">In Process</span>
                                                @break
                                            @case('done')
                                                <span class="badge bg-secondary-subtle text-secondary">Done</span>
                                                @break
                                            @case('cancel')
                                                <span class="badge bg-danger-subtle text-danger">Cancel</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($po->created_at)->translatedFormat('d F Y H:i') }}</td>
                                    <td>{{ $po->user->name }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('inbound.quality-control-process', ['id' => $po->id]) }}" class="btn btn-primary btn-sm">Process QC</a>
                                            <a href="{{ route('inbound.quality-control-process-ccw', ['id' => $po->id]) }}" class="btn btn-info btn-sm">Process QC CCW</a>
                                        </div>
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
