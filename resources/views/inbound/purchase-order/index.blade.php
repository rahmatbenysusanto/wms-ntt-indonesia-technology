@extends('layout.index')
@section('title', 'Purchase Order')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Purchase Order</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item active">Purchase Order</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('inbound.edit-purchase-order') }}" class="btn btn-warning">Edit Purchase Order</a>
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
                                    <a href="{{ route('inbound.purchase-order') }}" class="btn btn-danger">Clear</a>
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
                                    <th class="text-center">Download Doc</th>
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
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('inbound.purchase-order-download-excel', ['id' => $po->id]) }}" class="btn btn-success btn-sm">
                                                <i class="mdi mdi-file-excel" style="font-size: 14px;"></i>
                                            </a>
                                            <a href="{{ route('inbound.purchase-order-download-pdf', ['id' => $po->id]) }}" class="btn btn-pdf btn-sm text-white" target="_blank">
                                                <i class="mdi mdi-file-pdf-box" style="font-size: 14px;"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @if($po->status == 'new')
                                                <a class="btn btn-info btn-sm" onclick="approvedPurchaseOrder('{{ $po->id }}')">Approved PO</a>
                                                <a class="btn btn-danger btn-sm" onclick="cancelPurchaseOrder('{{ $po->id }}')">Cancel PO</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end mt-2">
                            @if ($purchaseOrder->hasPages())
                                <ul class="pagination">
                                    @if ($purchaseOrder->onFirstPage())
                                        <li class="disabled"><span>&laquo; Previous</span></li>
                                    @else
                                        <li><a href="{{ $purchaseOrder->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                    @endif

                                    @foreach ($purchaseOrder->links()->elements as $element)
                                        @if (is_string($element))
                                            <li class="disabled"><span>{{ $element }}</span></li>
                                        @endif

                                        @if (is_array($element))
                                            @foreach ($element as $page => $url)
                                                @if ($page == $purchaseOrder->currentPage())
                                                    <li class="active"><span>{{ $page }}</span></li>
                                                @else
                                                    <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach

                                    @if ($purchaseOrder->hasMorePages())
                                        <li><a href="{{ $purchaseOrder->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
                                    @else
                                        <li class="disabled"><span>Next &raquo;</span></li>
                                    @endif
                                </ul>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function approvedPurchaseOrder(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "Approved Purchase Order",
                icon: "warning",
                showCancelButton: true,
                customClass: {
                    confirmButton: "btn btn-primary w-xs me-2 mt-2",
                    cancelButton: "btn btn-danger w-xs mt-2"
                },
                confirmButtonText: "Yes, Approved it!",
                buttonsStyling: false,
                showCloseButton: true
            }).then(function(t) {
                if (t.value) {

                    $.ajax({
                        url: '{{ route('inbound.changeStatusPurchaseOrder') }}',
                        method: 'POST',
                        data:{
                            _token: '{{ csrf_token() }}',
                            type: 'approved',
                            id: id
                        },
                        success: (res) => {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Approved Purchase Order successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: "btn btn-primary w-xs mt-2"
                                },
                                buttonsStyling: false
                            }).then(() => {
                                window.location.href = '{{ route('inbound.purchase-order') }}';
                            });
                        }
                    });

                }
            });
        }

        function cancelPurchaseOrder(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "Cancel Purchase Order",
                icon: "warning",
                showCancelButton: true,
                customClass: {
                    confirmButton: "btn btn-primary w-xs me-2 mt-2",
                    cancelButton: "btn btn-danger w-xs mt-2"
                },
                confirmButtonText: "Yes, Cancel it!",
                buttonsStyling: false,
                showCloseButton: true
            }).then(function(t) {
                if (t.value) {

                    $.ajax({
                        url: '{{ route('inbound.changeStatusPurchaseOrder') }}',
                        method: 'POST',
                        data:{
                            _token: '{{ csrf_token() }}',
                            type: 'cancel',
                            id: id
                        },
                        success: (res) => {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Cancel Purchase Order successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: "btn btn-primary w-xs mt-2"
                                },
                                buttonsStyling: false
                            }).then(() => {
                                window.location.href = '{{ route('inbound.purchase-order') }}';
                            });
                        }
                    });

                }
            });
        }
    </script>
@endsection
