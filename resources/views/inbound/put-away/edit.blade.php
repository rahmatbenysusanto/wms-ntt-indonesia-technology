@extends('layout.index')
@section('title', 'Edit Put Away')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Edit Put Away</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item">Put Away</li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Put Away</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Sales Doc</th>
                                    <th>Item</th>
                                    <th>Material</th>
                                    <th>PO Item Desc</th>
                                    <th>Prod Hierarchy Desc</th>
                                    <th class="text-center">QTY</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="listDataProducts">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="detailSNModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Detail Serial Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Serial Number</th>
                        </tr>
                        </thead>
                        <tbody id="listSerialNumber"></tbody>
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

        loadProducts();
        function loadProducts() {
            const products = @json($products);

            localStorage.setItem('products', JSON.stringify(products));
            viewProducts();
        }

        function viewProducts() {
            const products = JSON.parse(localStorage.getItem('products')) ?? [];
            let html = '';
            let number = 1;

            products.forEach((product, index) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td>${product.is_parent === 1 ? '<span class="badge bg-danger-subtle text-danger">Parent</span>' : '<span class="badge bg-secondary-subtle text-secondary">Child</span>'}</td>
                        <td>${product.purchase_order_detail.sales_doc}</td>
                        <td>${product.purchase_order_detail.item}</td>
                        <td>${product.purchase_order_detail.material}</td>
                        <td>${product.purchase_order_detail.po_item_desc}</td>
                        <td>${product.purchase_order_detail.prod_hierarchy_desc}</td>
                        <td class="text-center fw-bold">${product.qty}</td>
                        <td><a class="btn btn-info btn-sm">Edit Product</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listDataProducts').innerHTML = html;
        }

        function detailSerialNumber(id) {
            $.ajax({
                url: '{{ route('inbound.put-away.find-serial-number-inventory') }}',
                method: 'GET',
                data: {
                    id: id
                },
                success: (res) => {
                    console.log(id);
                    console.info(res);

                    const serialNumber = res.data ?? [];
                    let html = '';

                    serialNumber.forEach((sn) => {
                        html += `
                            <tr><td>${sn.serial_number}</td></tr>
                        `;
                    });

                    document.getElementById('listSerialNumber').innerHTML = html;
                    $('#detailSNModal').modal('show');
                }
            });
        }
    </script>
@endsection

































