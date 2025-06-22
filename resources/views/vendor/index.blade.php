@extends('layout.index')
@section('title', 'Vendor')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Vendor</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Client</a></li>
                        <li class="breadcrumb-item active">Vendor</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createVendor">Create Vendor</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Vendor Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($vendor as $index => $item)
                                <tr>
                                    <td>{{ $vendor->firstItem() + $index }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a class="btn btn-info btn-sm" onclick="edit('{{ $item->id }}')">Edit</a>
                                            <a class="btn btn-danger btn-sm" onclick="delete('{{ $item->id }}')">Delete</a>
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

    <!-- Create Vendor Modals -->
    <div id="createVendor" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('vendor.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Vendor Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Name vendor ..." required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Vendor Modals -->
    <div id="editVendor" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('vendor.edit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="idVendor">
                        <div class="mb-3">
                            <label class="form-label">Vendor Name</label>
                            <input type="text" class="form-control" name="name" id="nameVendor" placeholder="Name vendor ..." required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Edit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function edit(id) {
            $.ajax({
                url: '{{ route('vendor.find') }}',
                method: 'GET',
                data: {
                    id: id
                },
                success: (res) => {
                    const data = res.data;

                    document.getElementById('idVendor').value = data.id;
                    document.getElementById('nameVendor').value = data.name;

                    $('#editVendor').modal('show');
                }
            });
        }
    </script>
@endsection






































