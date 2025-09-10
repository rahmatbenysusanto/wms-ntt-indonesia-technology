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
                    <input type="text" class="form-control" id="putAwaySearchInput" placeholder="Search Put Away Number ..." autofocus>
                </div>
            </div>
        </div>

        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Product</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Sales Doc</th>
                                <th>Material</th>
                                <th>Item Desc</th>
                                <th>Hierarchy Desc</th>
                                <th class="text-center">QTY</th>
                            </tr>
                        </thead>
                        <tbody id="listMaterial">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">New Location</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Area</label>
                        <select class="form-control" onchange="changeRaw(this.value)" id="raw">
                            <option value="">-- Select Area --</option>
                            @foreach($storageRaw as $raw)
                                <option value="{{ $raw->raw }}">{{ $raw->raw }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Raw</label>
                        <select class="form-control" id="area" onchange="changeArea(this.value)">

                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rak</label>
                        <select class="form-control" id="rak" onchange="changeRak(this.value)">

                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bin</label>
                        <select class="form-control" id="bin" name="bin">

                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.getElementById('putAwaySearchInput').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = this.value.trim();
                searchPutAwayData(value);
            }
        });

        function searchPutAwayData(value) {
            $.ajax({
                url: '{{ route('inventory.transfer-location-find-pa-number') }}',
                method: 'GET',
                data: {
                    paNumber: value
                },
                success: (res) => {
                    if (res.status) {
                        const products = res.data;
                        let html = '';
                        let number = 1;

                        products.forEach((product) => {
                            html += `
                                <tr>
                                    <td>${number}</td>
                                    <td>${product.purchase_order_detail.item}</td>
                                    <td>${product.purchase_order_detail.sales_doc}</td>
                                    <td>${product.purchase_order_detail.material}</td>
                                    <td>${product.purchase_order_detail.po_item_desc}</td>
                                    <td>${product.purchase_order_detail.prod_hierarchy_desc}</td>
                                    <td class="text-center">${product.qty}</td>
                                </tr>
                            `;
                            number++;
                        });

                        document.getElementById('listMaterial').innerHTML = html;
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Put Away Number tidak diketahui',
                            icon: 'error'
                        });
                    }
                }
            });
        }

        function changeRaw(raw) {
            $.ajax({
                url: '{{ route('storage.find.area') }}',
                method: 'GET',
                data: {
                    raw: raw
                },
                success: (res) => {
                    const data = res.data;
                    let html = '<option value="">-- Select Raw --</option>';

                    data.forEach((item) => {
                        html += `<option value="${item.area}">${item.area}</option>`;
                    });

                    document.getElementById('area').innerHTML = html;
                }
            });
        }

        function changeArea(area) {
            $.ajax({
                url: '{{ route('storage.find.rak') }}',
                method: 'GET',
                data: {
                    raw: document.getElementById('raw').value,
                    area: area
                },
                success: (res) => {
                    const data = res.data;
                    let html = '<option value="">-- Select Rak --</option>';

                    data.forEach((item) => {
                        html += `<option value="${item.rak}">${item.rak}</option>`;
                    });

                    document.getElementById('rak').innerHTML = html;
                }
            });
        }

        function changeRak(rak) {
            $.ajax({
                url: '{{ route('storage.find.bin') }}',
                method: 'GET',
                data: {
                    raw: document.getElementById('raw').value,
                    area: document.getElementById('area').value,
                    rak: rak
                },
                success: (res) => {
                    console.log(document.getElementById('raw').value)
                    console.log(document.getElementById('area').value)
                    console.log(document.getElementById('rak').value)
                    const data = res.data;
                    console.log(data);
                    let html = '<option value="">-- Select Bin --</option>';

                    data.forEach((item) => {
                        html += `<option value="${item.id}">${item.bin}</option>`;
                    });

                    document.getElementById('bin').innerHTML = html;
                }
            });
        }

        function processTransferLocation() {
            Swal.fire({
                title: "Are you sure?",
                text: "Process Transfer Location",
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
                        url: '{{ route('inventory.transfer-location-store') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            paNumber: document.getElementById('putAwaySearchInput').value,
                            storageId: document.getElementById('bin').value,
                        },
                        success: (res) => {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Success',
                                    text: 'Transfer Location Success',
                                    icon: 'success'
                                }).then((e) => {
                                    window.location.href = '{{ route('inventory.transfer-location') }}'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Transfer Location Failed',
                                    icon: 'error'
                                });
                            }
                        }
                    });

                }
            });
        }
    </script>
@endsection
