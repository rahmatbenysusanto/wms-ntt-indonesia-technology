@extends('layout.index')
@section('title', 'Change Box Product')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Inventory Change Box Product</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item">Box Detail</li>
                        <li class="breadcrumb-item active">Change Box</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Box Information</h4>
                        <a class="btn btn-primary" onclick="changeBoxProcess()">Change Box Product</a>
                    </div>
                </div>
                <div class="card-body">
                    <table>
                        <tr>
                            <td class="fw-bold">Purchase Order</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $inventoryPackage->purchaseOrder->purc_doc }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">PA Number</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $inventoryPackage->number }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Reff</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $inventoryPackage->reff_number }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Storage</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $inventoryPackage->storage->raw }} - {{ $inventoryPackage->storage->area }} - {{ $inventoryPackage->storage->rak }} - {{ $inventoryPackage->storage->bin }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">New Box Location</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <select class="form-select" id="newBox">
                                <option value="">-- Select New Box --</option>
                                @foreach($listBox as $item)
                                    <option value="{{ $item->id }}">{{ $item->number }} | {{ $item->reff_number }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Product Package</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle" id="tableListProduct">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Data Product</th>
                                <th>QTY</th>
                                <th>Serial Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="listProducts">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Product Change Box</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle" id="tableListProductChangeBox">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Data Product</th>
                            <th>QTY</th>
                            <th>Serial Number</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="listProductsChangeBox">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        localStorage.clear();

        loadProducts();
        function loadProducts() {
            const dataProducts = @json($products);
            const products = [];

            dataProducts.forEach((product, index) => {
                product.select = 0;
                product.indexProduct = index;
                products.push(product);
            });

            localStorage.setItem('products', JSON.stringify(products));
            viewProducts();
        }

        function viewProducts() {
            const products = JSON.parse(localStorage.getItem('products')) ?? [];
            let html = '';
            let number = 1;

            let currentPage = 0;
            if ($.fn.DataTable.isDataTable('#tableListProduct')) {
                currentPage = $('#tableListProduct').DataTable().page();
                $('#tableListProduct').DataTable().destroy();
            }

            products.forEach((product, index) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td>
                            <div><b>Sales Doc: </b>${product.sales_doc}</div>
                            <div><b>Item: </b>${product.item}</div>
                            <div><b>Material: </b>${product.material}</div>
                            <div><b>Desc: </b>${product.po_item_desc}</div>
                            <div><b>Hie: </b>${product.prod_hierarchy_desc}</div>
                        </td>
                        <td>1</td>
                        <td>${product.serial_number}</td>
                        <td>${product.select === 0 ? `<a class="btn btn-info btn-sm" onclick="changeProduct(${index})">Pilih</a>` : ''}</td>
                    </tr>
                `;
                number++;
            });

            document.getElementById('listProducts').innerHTML = html;
            const table = new DataTable('#tableListProduct');
            table.page(currentPage).draw('page');
        }

        function changeProduct(index) {
            const products = JSON.parse(localStorage.getItem('products')) ?? [];
            const changeProducts = JSON.parse(localStorage.getItem('changeProducts')) ?? [];

            products[index].select = 1;
            changeProducts.push(products[index]);

            localStorage.setItem('products', JSON.stringify(products));
            localStorage.setItem('changeProducts', JSON.stringify(changeProducts));

            viewProducts();
            viewChangeProducts();
        }

        function viewChangeProducts() {
            const changeProducts = JSON.parse(localStorage.getItem('changeProducts')) ?? [];
            let html = '';
            let number = 1;

            let currentPage = 0;
            if ($.fn.DataTable.isDataTable('#tableListProductChangeBox')) {
                currentPage = $('#tableListProductChangeBox').DataTable().page();
                $('#tableListProductChangeBox').DataTable().destroy();
            }

            changeProducts.forEach((product, index) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td>
                            <div><b>Sales Doc: </b>${product.sales_doc}</div>
                            <div><b>Item: </b>${product.item}</div>
                            <div><b>Material: </b>${product.material}</div>
                            <div><b>Desc: </b>${product.po_item_desc}</div>
                            <div><b>Hie: </b>${product.prod_hierarchy_desc}</div>
                        </td>
                        <td>1</td>
                        <td>${product.serial_number}</td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteProducts(${index})">Delete</a></td>
                    </tr>
                `;
                number++;
            });

            document.getElementById('listProductsChangeBox').innerHTML = html;
            const table = new DataTable('#tableListProductChangeBox');
            table.page(currentPage).draw('page');
        }

        function deleteProducts(index) {
            const changeProducts = JSON.parse(localStorage.getItem('changeProducts')) ?? [];
            const products = JSON.parse(localStorage.getItem('products')) ?? [];

            const findProduct = changeProducts[index];
            products[findProduct.indexProduct].select = 0;
            changeProducts.splice(index, 1);

            localStorage.setItem('changeProducts', JSON.stringify(changeProducts));
            localStorage.setItem('products', JSON.stringify(products));
            viewChangeProducts();
            viewProducts();
        }

        function changeBoxProcess() {
            Swal.fire({
                title: "Are you sure?",
                text: "Change Box Product",
                icon: "warning",
                showCancelButton: true,
                customClass: {
                    confirmButton: "btn btn-primary w-xs me-2 mt-2",
                    cancelButton: "btn btn-danger w-xs mt-2"
                },
                confirmButtonText: "Yes, Change it!",
                buttonsStyling: false,
                showCloseButton: true
            }).then(function(t) {
                if (t.value) {

                    $.ajax({
                        url: '{{ route('inventory.change.box.post') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            changeProducts: JSON.parse(localStorage.getItem('changeProducts')) ?? [],
                            newBox: document.getElementById('newBox').value
                        },
                        success: (res) => {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Success',
                                    text: 'Change Box Location Successfully',
                                    icon: 'success'
                                }).then((i) => {
                                    window.location.href = '{{ route('inventory.box') }}';
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Change Box Location Failed',
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
