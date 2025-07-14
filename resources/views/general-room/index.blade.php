@extends('layout.index')
@section('title', 'General Room')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">General Room</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">General Room</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List General Room</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th>Client</th>
                                    <th>PA Number</th>
                                    <th>Material Parent</th>
                                    <th>Material Parent Desc</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-center">Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($generalRoom as $index => $gr)
                                    <tr>
                                        <td>{{ $generalRoom->firstItem() + $index }}</td>
                                        <td>{{ $gr->purc_doc }}</td>
                                        <td>
                                            @foreach($gr->sales_doc_array as $item)
                                                <div>{{ $item }}</div>
                                            @endforeach
                                        </td>
                                        <td>{{ $gr->client }}</td>
                                        <td>{{ $gr->pa_number }}</td>
                                        <td>{{ $gr->material }}</td>
                                        <td>{{ $gr->po_item_desc }}</td>
                                        <td class="text-center fw-bold">{{ $gr->qty_item }}</td>
                                        <td class="text-center">
                                            @if($gr->status == 'open')
                                                <span class="badge bg-info-subtle text-info">New</span>
                                            @elseif($gr->status == 'return')
                                                <span class="badge bg-warning-subtle text-warning">Return WH</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success">Outbound</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($gr->created_at)->translatedFormat('d F Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('general-room.detail', ['id' => $gr->id]) }}" class="btn btn-secondary btn-sm">Detail</a>
                                                @if($gr->status == 'open')
                                                    <a class="btn btn-info btn-sm" onclick="outboundAll('{{ $gr->id }}')">Outbound All</a>
                                                    <a class="btn btn-primary btn-sm">Outbound Partial</a>
                                                    <a class="btn btn-warning btn-sm">Return to WH</a>
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
        function outboundAll(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "Outbound All Product",
                icon: "warning",
                showCancelButton: true,
                customClass: {
                    confirmButton: "btn btn-primary w-xs me-2 mt-2",
                    cancelButton: "btn btn-danger w-xs mt-2"
                },
                confirmButtonText: "Yes, Process it!",
                buttonsStyling: false,
                showCloseButton: true
            }).then(function(t) {
                if (t.value) {

                    $.ajax({
                        url: '{{ route('general-room.outbound.all') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: id
                        },
                        success: (res) => {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Success',
                                    text: 'Outbound Success',
                                    icon: 'success'
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
