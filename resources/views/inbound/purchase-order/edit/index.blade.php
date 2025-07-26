@extends('layout.index')
@section('title', 'Edit Purchase Order')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Edit Purchase Order</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item">Purchase Order</li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Request Edit PO</h4>
                        <a href="{{ route('inbound.edit-purchase-order-product') }}" class="btn btn-info">Create Request Edit PO</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Purc Doc</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Note</th>
                                    <th>Created By</th>
                                    <th>Approved By</th>
                                    <th>Create Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder as $index => $item)
                                    <tr>
                                        <td>{{ $purchaseOrder->firstItem() + $index }}</td>
                                        <td>
                                            @php $detail = json_decode($item->details) @endphp
                                            {{ $detail->sales_doc }}
                                        </td>
                                        <td>
                                            @if($item->type == 'delete')
                                                <span class="badge bg-danger-subtle text-danger">Delete</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success">Edit</span>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($item->status)
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
                                        <td>{{ $item->note }}</td>
                                        <td>{{ $item->requestBy->name }}</td>
                                        <td>
                                            @if($item->status != 'pending')
                                                <div>{{ $item->approvedBy?->name }}</div>
                                                <div>{{ \Carbon\Carbon::parse($item->approved_at)->translatedFormat('d F Y H:i') }}</div>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('inbound.edit.detail', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Detail</a>
                                                @if(Auth::user()->role == 'admin' && $item->status == 'pending')
                                                    <a class="btn btn-success btn-sm" onclick="approvedEditPO('{{ $item->id }}')">Approved Request</a>
                                                    <a class="btn btn-danger btn-sm" onclick="cancelEditPO('{{ $item->id }}')">Cancel Request</a>
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
        function approvedEditPO(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "Approved Request Edit PO",
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
                        url: '{{ route('inbound.edit.approved') }}',
                        method: 'GET',
                        data: {
                            id: id
                        },
                        success: (res) => {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Approved Request Edit Po Success',
                                    icon: 'success',
                                }).then((i) => {
                                    window.location.reload();
                                });
                            }
                        }
                    });

                }
            });
        }

        function cancelEditPO(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "Cancel Request Edit PO",
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
                        url: '{{ route('inbound.edit.cancel') }}',
                        method: 'GET',
                        data: {
                            id: id
                        },
                        success: (res) => {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Cancel Request Edit Po Success',
                                    icon: 'success',
                                }).then((i) => {
                                    window.location.reload();
                                });
                            }
                        }
                    });

                }
            });
        }
    </script>
@endsection
