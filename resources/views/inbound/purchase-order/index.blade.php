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
                        <a href="{{ route('inbound.purchase-order-upload') }}" class="btn btn-info">Upload PO Excel</a>
                    </div>
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
                                    <td class="text-center">{{ number_format($po->sales_docs_qty) }}</td>
                                    <td class="text-center">{{ number_format($po->material_qty) }}</td>
                                    <td class="text-center">{{ number_format($po->items_qty) }}</td>
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
{{--                                            <a href="{{ route('inbound.upload.serial-number', ['id' => $po->id]) }}" class="btn btn-primary btn-sm">Serial Number</a>--}}
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
