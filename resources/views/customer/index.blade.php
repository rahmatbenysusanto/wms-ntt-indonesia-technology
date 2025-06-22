@extends('layout.index')
@section('title', 'Customer')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Customer</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Client</a></li>
                        <li class="breadcrumb-item active">Customer</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Customer</h4>
                        <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCustomer">Add Customer</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Provinsi</th>
                                    <th>Kota</th>
                                    <th>Kode Pos</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($customer as $index => $item)
                                <tr>
                                    <td>{{ $customer->firstItem() + $index }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->provinsi }}</td>
                                    <td>{{ $item->kota }}</td>
                                    <td>{{ $item->kode_pos }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a class="btn btn-info">Edit</a>
                                            <a class="btn btn-danger">Delete</a>
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

    <!-- Create Customer Modals -->
    <div id="createCustomer" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('customer.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Customer Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Name Customer ..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Provinsi</label>
                            <input type="text" class="form-control" name="provinsi">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kab/Kota</label>
                            <input type="text" class="form-control" name="kota">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode Pos</label>
                            <input type="number" class="form-control" name="kode_pos">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')

@endsection
