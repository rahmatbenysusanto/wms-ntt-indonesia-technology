@extends('layout.index')
@section('title', 'Transfer Location')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Create Transfer Location</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item">Transfer Location</li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Create Transfer Location</h4>
                        <a class="btn btn-primary" onclick="processTransferLocation()">Process Transfer Location</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Sales Doc</label>
                                <select class="form-control">
                                    <option value="">-- Select Sales Doc --</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Old Location</label>
                                <input type="text" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Product Parent</label>
                                <select class="form-control">
                                    <option value="">-- Select Product --</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">QTY</label>
                                <input type="number" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Product</h4>
                </div>
                <div class="card-body">

                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">New Location</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Raw</label>
                        <select class="form-control">
                            <option value="">-- Select Raw --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Area</label>
                        <select class="form-control">
                            <option value="">-- Select Area --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rak</label>
                        <select class="form-control">
                            <option value="">-- Select Rak --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bin</label>
                        <select class="form-control">
                            <option value="">-- Select Bin --</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function processTransferLocation() {

        }
    </script>
@endsection
