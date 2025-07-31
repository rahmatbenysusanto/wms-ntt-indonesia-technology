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
                                    <th>Material</th>
                                    <th>PO Item Desc</th>
                                    <th>Prod Hierarchy Desc</th>
                                    <th class="text-center">Stock</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($generalRoom as $index => $gr)
                                    <tr>
                                        <td>{{ $generalRoom->firstItem() + $index }}</td>
                                        <td>{{ $gr->purc_doc }}</td>
                                        <td>{{ $gr->sales_doc }}</td>
                                        <td>{{ $gr->customer_name }}</td>
                                        <td>{{ $gr->material }}</td>
                                        <td>{{ $gr->po_item_desc }}</td>
                                        <td>{{ $gr->prod_hierarchy_desc }}</td>
                                        <td class="text-center fw-bold">{{ $gr->stock }}</td>
                                        <td>{{ \Carbon\Carbon::parse($gr->created_at)->translatedFormat('d F Y H:i') }}</td>
                                        <td>
                                            <a class="btn btn-info btn-sm">Detail</a>
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
