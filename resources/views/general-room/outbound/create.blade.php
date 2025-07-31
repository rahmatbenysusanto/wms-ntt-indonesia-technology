@extends('layout.index')
@section('title', 'Outbound General Room')
@section('sizeBarSize', 'sm')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Create General Room Outbound</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">General Room</a></li>
                        <li class="breadcrumb-item">Outbound</li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Data Outbound General Room</h4>
                        <a class="btn btn-primary">Create Outbound</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Customer</label>
                                <select class="form-control">
                                    <option value="">-- Select Customer --</option>
                                    @foreach($customer as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Destination</label>
                                <select class="form-control">
                                    <option>Client</option>
                                    <option>Client Warehouse</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label">Delivery Location</label>
                                <input type="text" class="form-control" placeholder="Delivery Location">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Outbound Date</label>
                                <input type="datetime-local" class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Products</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>PO</th>
                                <th>Product</th>
                                <th class="text-center">QTY</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="listProducts">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-7">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0"></h4>
                </div>
                <div class="card-body">

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
            const products = @json($products);
            localStorage.setItem('products', JSON.stringify(products));

            viewListProducts();
        }

        function viewListProducts() {
            const products = JSON.parse(localStorage.getItem('products')) ?? [];
            let html = '';
            let number = 1;

            products.forEach((product) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td>
                            <div><b>Purc Doc: </b>${product.purc_doc}</div>
                            <div><b>Sales Doc: </b>${product.sales_doc}</div>
                        </td>
                        <td>
                            <div><b>Material: </b>${product.product.material}</div>
                            <div><b>Desc: </b>${product.product.po_item_desc}</div>
                            <div><b>Hie: </b>${product.product.prod_hierarchy_desc}</div>
                        </td>
                        <td class="text-center fw-bold">${product.stock}</td>
                        <td><a class="btn btn-info btn-sm">Pilih</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listProducts').innerHTML = html;
        }
    </script>
@endsection
