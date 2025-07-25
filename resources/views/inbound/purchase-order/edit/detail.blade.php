@extends('layout.index')
@section('title', 'Detail PO Edit')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Detail Edit Purchase Order</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item">Edit Purchase Order</li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Purchase Order Request Edit</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <table>
                                <tr>
                                    <td class="fw-bold">Sales Doc</td>
                                    <td class="fw-bold ps-2">:</td>
                                    <td class="ps-1">
                                        @php $detail = json_decode($purchaseOrder->details) @endphp
                                        {{ $detail->sales_doc }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Request By</td>
                                    <td class="fw-bold ps-2">:</td>
                                    <td class="ps-1">{{ $purchaseOrder->requestBy->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Request Date</td>
                                    <td class="fw-bold ps-2">:</td>
                                    <td class="ps-1">{{ \Carbon\Carbon::parse($purchaseOrder->created_at)->translatedFormat('d F Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Note</td>
                                    <td class="fw-bold ps-2">:</td>
                                    <td class="ps-1">{{ $purchaseOrder->note }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Type</td>
                                    <td class="fw-bold ps-2">:</td>
                                    <td class="ps-1">
                                        @if($purchaseOrder->type == 'delete')
                                            <span class="badge bg-danger-subtle text-danger">Delete</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success">Edit</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-6">
                            <table>
                                <tr>
                                    <td class="fw-bold">Status Request</td>
                                    <td class="fw-bold ps-2">:</td>
                                    <td class="ps-1">
                                        @switch($purchaseOrder->status)
                                            @case('pending')
                                                <span class="badge bg-info-subtle text-info">Pending</span>
                                                @break
                                            @case('approved')
                                                <span class="badge bg-success-subtle text-success">Approved</span>
                                                @break
                                            @case('cancel')
                                                <span class="badge bg-danger-subtle text-danger">Cancel</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Approved By</td>
                                    <td class="fw-bold ps-2">:</td>
                                    <td class="ps-1">
                                        @if($purchaseOrder->status == 'approved')
                                            {{ $purchaseOrder->approve_by->name }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Approved Date</td>
                                    <td class="fw-bold ps-2">:</td>
                                    <td class="ps-1">
                                        @if($purchaseOrder->status == 'approved')
                                            {{ \Carbon\Carbon::parse($purchaseOrder->approved_at)->translatedFormat('d F Y H:i') }}
                                        @endif
                                    </td>
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
                    <h4 class="card-title mb-0">Request Detail</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Sales Doc</label>
                                <input type="text" class="form-control" value="{{ $detail->sales_doc }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Item</label>
                                <input type="number" class="form-control" value="{{ $detail->item }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Material</label>
                                <input type="text" class="form-control" value="{{ $detail->material }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Vendor Name</label>
                                <input type="text" class="form-control" value="{{ $detail->vendor_name }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Stor Loc</label>
                                <input type="text" class="form-control" value="{{ $detail->stor_loc }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Valuation</label>
                                <input type="text" class="form-control" value="{{ $detail->valuation }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Net Order Price</label>
                                <input type="number" class="form-control" value="{{ $detail->net_order_price }}" readonly>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">PO Item Desc</label>
                                <input type="text" class="form-control" value="{{ $detail->po_item_desc }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Prod Hierarchy Desc</label>
                                <input type="text" class="form-control" value="{{ $detail->prod_hierarchy_desc }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Acc Ass Cat</label>
                                <input type="text" class="form-control" value="{{ $detail->acc_ass_cat }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" class="form-control" value="{{ $detail->customer_name }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sloc Desc</label>
                                <input type="text" class="form-control" value="{{ $detail->sloc_desc }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">PO Item QTY</label>
                                <input type="number" class="form-control" value="{{ $detail->po_item_qty }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Currency</label>
                                <input type="text" class="form-control" value="{{ $detail->currency }}" readonly>
                            </div>
                        </div>
                        <div class="col-12 mb-1">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" readonly>{{ $detail->note }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
