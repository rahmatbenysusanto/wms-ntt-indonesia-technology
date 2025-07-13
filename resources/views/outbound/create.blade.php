@extends('layout.index')
@section('title', 'Create Order')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Order List</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Outbound</a></li>
                        <li class="breadcrumb-item active">Order List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mn-0">Order Detail</h4>
                        <a class="btn btn-primary" onclick="processCreateOrder()">Create Order</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Customer</label>
                            <select class="form-control" id="customerId">
                                <option value="">-- Select Customer --</option>
                                @foreach($customer as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Location</label>
                            <input type="text" class="form-control" id="delivLocation">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Destination</label>
                            <select class="form-control" id="deliveryDest">
                                <option value="client">Client</option>
                                <option value="general room">General Room</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Item</h4>
                        <a class="btn btn-info" onclick="openModalProduct()">Add Product</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>PA Number</th>
                                <th>Type</th>
                                <th>Purc Doc</th>
                                <th>Sales Doc</th>
                                <th>Item</th>
                                <th>Material</th>
                                <th>Desc</th>
                                <th>QTY</th>
                                <th>QTY Out</th>
                                <th>Loc</th>
                                <th>Serial Number</th>
                            </tr>
                        </thead>
                        <tbody id="listItem">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Default Modals -->
    <div id="listProductModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">List Products</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Sales Doc</label>
                        <select class="form-control" id="listSalesDoc" onchange="changeSalesDoc(this.value)">

                        </select>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>PA Number</th>
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th>Material</th>
                                    <th>Desc</th>
                                    <th class="text-center">QTY</th>
                                    <th>Loc</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="listItemSalesDoc">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="uploadSerialNumberModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Upload Serial Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <table>
                            <tr>
                                <td class="fw-bold">Item</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="SN_item"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Material</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="SN_material"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Desc</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="SN_desc"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Hierarchy</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="SN_hie"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-8">
                                <label class="form-label">Upload Excel Serial Number</label>
                                <input type="file" class="form-control" id="uploadFileSN">
                            </div>
                            <div class="col-2">
                                <label class="form-label text-white">-</label>
                                <div>
                                    <a class="btn btn-info w-100" onclick="processDateUploadSN()">Proses Data</a>
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label text-white">-</label>
                                <div>
                                    <a class="btn btn-secondary w-100" onclick="tambahSerialNumberManual()">Add Manual</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Serial Number</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="listSerialNumberUpload">

                        </tbody>
                    </table>

                    <input type="hidden" id="SN_index">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="uploadSerialNumberProcess()">Upload</button>
                </div>

            </div>
        </div>
    </div>

    <div id="detailSerialNumberModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Detail Serial Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <table>
                            <tr>
                                <td class="fw-bold">Item</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="detail_SN_item"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Material</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="detail_SN_material"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Desc</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="detail_SN_desc"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Hierarchy</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="detail_SN_hie"></td>
                            </tr>
                        </table>
                    </div>

                    <table class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Serial Number</th>
                        </tr>
                        </thead>
                        <tbody id="listDetailSerialNumberUpload">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        localStorage.clear();

        function viewSalesDoc() {
            const salesDoc = @json($salesDoc);
            let html = '<option value="">-- Pilih Sales Doc --</option>';

            salesDoc.forEach((item) => {
                html += `<option>${item.sales_doc}</option>`
            });

            document.getElementById('listSalesDoc').innerHTML = html;
        }

        function changeSalesDoc(value) {
            $.ajax({
                url: '{{ route('outbound.sales-doc') }}',
                method: 'GET',
                data: {
                    salesDoc: value
                },
                success: (res) => {
                    console.log(res.data);
                    localStorage.setItem('master', JSON.stringify(res.data));
                    viewListMaster();
                }
            });
        }

        function viewListMaster() {
            const products = JSON.parse(localStorage.getItem('master')) ?? [];
            let html = '';
            let number = 1;

            products.forEach((product, index) => {
                let salesDoc = '';
                (JSON.parse(product.sales_docs)).forEach((item) => {
                    salesDoc += `<div>${item}</div>`;
                });

                let storage = '-';
                if (product.storage.id !== 1) {
                    storage = `${product.storage.raw} - ${product.storage.area} - ${product.storage.rak} - ${product.storage.bin}`;
                }

                html += `
                    <tr>
                        <td>${number}</td>
                        <td>${product.pa_reff_number ?? product.pa_number}</td>
                        <td>${product.purchase_order.purc_doc}</td>
                        <td>${salesDoc}</td>
                        <td>${product.product.material}</td>
                        <td>${product.product.po_item_desc}</td>
                        <td class="text-center fw-bold">${product.stock}</td>
                        <td>${storage}</td>
                        <td><a class="btn btn-info btn-sm" onclick="pilihProduct(${index})">Pilih</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listItemSalesDoc').innerHTML = html;
        }

        function openModalProduct() {
            viewSalesDoc();
            $('#listProductModal').modal('show');
        }

        function pilihProduct(index) {
            const products = JSON.parse(localStorage.getItem('master')) ?? [];

            const product = products[index];

            $.ajax({
                url: '{{ route('outbound.inventory-product') }}',
                method: 'GET',
                data: {
                    id: product.id,
                },
                success: (res) => {
                    localStorage.setItem('products', JSON.stringify(res.data));
                    $('#listProductModal').modal('hide');
                    viewListProduct();
                }
            });
        }

        function viewListProduct() {
            const products = JSON.parse(localStorage.getItem('products')) ?? [];
            let html = '';
            let number = 1;

            products.forEach((product, index) => {
                let serialNumber = '';
                (product.serial_number).forEach((sn) => {
                    serialNumber += `<div>${sn.serial_number}</div>`;
                });

                html += `
                    <tr>
                        <td>${number}</td>
                        <td>${product.pa_number}
                        <td>${product.type === 'parent' ? '<span class="badge bg-info-subtle text-info">Parent</span>' : ''}</td>
                        <td>${product.purc_doc}</td>
                        <td>${product.sales_doc}</td>
                        <td>${product.item}</td>
                        <td>${product.material}</td>
                        <td>${product.po_item_desc}</td>
                        <td>${product.qty}</td>
                        <td><input type="number" class="form-control" value="${product.qty_select}" onclick="changeQty(${index}, this.value)"></td>
                        <td>${product.storage}</td>
                        <td>${serialNumber}</td>
                        <td></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listItem').innerHTML = html;
        }

        function changeQty(index, value) {
            const products = JSON.parse(localStorage.getItem('products')) ?? [];

            products[index].qty_select = parseInt(value);

            localStorage.setItem('products', JSON.stringify(products));
            viewListProduct();
        }

        function processCreateOrder() {
            Swal.fire({
                title: "Are you sure?",
                text: "Create Order",
                icon: "warning",
                showCancelButton: true,
                customClass: {
                    confirmButton: "btn btn-primary w-xs me-2 mt-2",
                    cancelButton: "btn btn-danger w-xs mt-2"
                },
                confirmButtonText: "Yes, Create it!",
                buttonsStyling: false,
                showCloseButton: true
            }).then(function(t) {
                if (t.value) {

                    $.ajax({
                        url: '{{ route('outbound.store') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            products: JSON.parse(localStorage.getItem('products')),
                            delivLocation: document.getElementById('delivLocation').value,
                            customerId: document.getElementById('customerId').value,
                            deliveryDest: document.getElementById('deliveryDest').value
                        },
                        success: (res) => {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Success',
                                    text: 'Create Order Successfully',
                                    icon: 'success'
                                }).then((e) => {
                                    {{--window.location.href = '{{ route('outbound.index') }}';--}}
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Create Order Failed',
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
