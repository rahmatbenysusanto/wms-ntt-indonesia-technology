@extends('layout.index')
@section('title', 'Serial Number')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Upload Serial Number</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item">Purchase Order</li>
                        <li class="breadcrumb-item active">Upload Serial Number</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Purchase Order Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <table>
                                <tr>
                                    <td class="fw-bold">Purc Doc</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $purchaseOrder->purc_doc }}</td>
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Upload Excel Serial Number</h4>
                        <a class="btn btn-info" onclick="uploadSerialNumber()">Upload Serial Number</a>
                    </div>
                </div>
                <div class="card-body">
                    <input type="file" class="form-control" id="excelFile">
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Item Serial Number</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sales Doc</th>
                                    <th>Item</th>
                                    <th>Material</th>
                                    <th>PO Item Desc</th>
                                    <th>Prod Hierarchy Desc</th>
                                    <th>PO Itm Qty</th>
                                    <th>Serial Number</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        function uploadSerialNumber() {
            const fileInput = document.getElementById('excelFile');
            const file = fileInput.files[0];

            if (!file) {
                alert("Silakan pilih file Excel terlebih dahulu.");
                return;
            }

            const reader = new FileReader();

        }
    </script>
@endsection
