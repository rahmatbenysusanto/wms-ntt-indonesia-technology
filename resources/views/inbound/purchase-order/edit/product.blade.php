@extends('layout.index')
@section('title', 'Edit PO Product')
@section('sizeBarSize', 'sm')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Create Edit Purchase Order</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item">Purchase Order</li>
                        <li class="breadcrumb-item active">Create Edit</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Edit Product PO</h4>
                        <a class="btn btn-primary" onclick="createRequestEditPO()">Create Request Edit PO</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Purchase Order</h4>
                </div>
                <div class="card-body">
                    <table id="purchaseOrderTable" class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Purchase Order</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="purchaseOrder">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Product Edit</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="purchaseOrderDetailTable" class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Sales Doc</th>
                                    <th>Material</th>
                                    <th>PO Item Desc</th>
                                    <th>Prod Hierarchy Desc</th>
                                    <th>QTY</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="purchaseOrderDetail">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Edit Product PO</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="text-center">Type</th>
                                <th>Sales Doc</th>
                                <th>Item</th>
                                <th>Material</th>
                                <th>Note</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="listEditProduct">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Default Modals -->
    <div id="editProductsModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Edit Product PO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <div class="row" id="dataEditProduct">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addEditProduct()">Edit Product</button>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        localStorage.clear();

        loadPurchaseOrder();
        function loadPurchaseOrder() {
            const purchaseOrder = @json($purchaseOrder);
            localStorage.setItem('purchaseOrder', JSON.stringify(purchaseOrder));
            viewPurchaseOrder();
        }

        function viewPurchaseOrder() {
            const purchaseOrder = JSON.parse(localStorage.getItem('purchaseOrder'));
            let html = '';
            let number = 1;

            purchaseOrder.forEach(po => {
                let status = '';
                switch (po.status) {
                    case 'new':
                        status = '<span class="badge bg-success-subtle text-success">New</span>';
                        break;
                    case 'process':
                        status = '<span class="badge bg-primary-subtle text-primary">In Process</span>';
                        break;
                    case 'open':
                        status = '<span class="badge bg-info-subtle text-info">Open</span>';
                        break;
                }

                html += `
                    <tr>
                        <td>${number}</td>
                        <td>${po.purc_doc}</td>
                        <td>${status}</td>
                        <td><a class="btn btn-info btn-sm" onclick="pilihPO(${po.id})">Pilih</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('purchaseOrder').innerHTML = html;

            $(document).ready(function () {
                $('#purchaseOrderTable').DataTable();
            });
        }

        function pilihPO(id) {
            $.ajax({
                url: '{{ route('listMaterialEditPO') }}',
                method: 'GET',
                data: {
                    id: id
                },
                success: (res => {
                    const products = res.data;
                    localStorage.setItem('products', JSON.stringify(products));

                    let number = 1;
                    let html = '';

                    products.forEach((product, index) => {
                        html += `
                            <tr>
                                <td>${number}</td>
                                <td>${product.item}</td>
                                <td>${product.sales_doc}</td>
                                <td>${product.material}</td>
                                <td>${product.po_item_desc}</td>
                                <td>${product.prod_hierarchy_desc}</td>
                                <td>${product.po_item_qty}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-info btn-sm" onclick="pilihProduct(${product.id})">Edit Product</a>
                                        <a class="btn btn-danger btn-sm" onclick="deleteProduct(${index})">Delete Product</a>
                                    </div>
                                </td>
                            </tr>
                        `;

                        number++;
                    });

                    document.getElementById('purchaseOrderDetail').innerHTML = html;

                    $(document).ready(function () {
                        $('#purchaseOrderDetailTable').DataTable();
                    });
                })
            });
        }

        function deleteProduct(index) {
            const products = JSON.parse(localStorage.getItem('products'));
            const editProducts = JSON.parse(localStorage.getItem('editProducts')) ?? [];
            const product = products[index];
            product.note = "";

            editProducts.push({
                'ket': 'delete',
                'data': product
            });

            localStorage.setItem('editProducts', JSON.stringify(editProducts));
            localStorage.setItem('product', JSON.stringify([]));

            viewListEditProduct();
            $('#editProductsModal').modal('hide');

            Swal.fire({
                title: 'Success!',
                text: 'Product dimasukan ke dalam list',
                icon: 'success',
            });
        }

        function pilihProduct(id) {
            const products = JSON.parse(localStorage.getItem('products'));
            const product = products.find(i => parseInt(i.id) === parseInt(id));
            product.note = "";
            localStorage.setItem('product', JSON.stringify(product));

            viewProductModal();
            $('#editProductsModal').modal('show');
        }

        function viewProductModal() {
            const product = JSON.parse(localStorage.getItem('product')) ?? [];
            document.getElementById('dataEditProduct').innerHTML = `
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label">Sales Doc</label>
                        <input type="text" class="form-control" value="${product.sales_doc}" onchange="changeValueProduct('sales_doc', this.value)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Item</label>
                        <input type="number" class="form-control" value="${product.item}" onchange="changeValueProduct('item', this.value)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Material</label>
                        <input type="text" class="form-control" value="${product.material}" onchange="changeValueProduct('material', this.value)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vendor Name</label>
                        <input type="text" class="form-control" value="${product.vendor_name}" onchange="changeValueProduct('vendor_name', this.value)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stor Loc</label>
                        <input type="text" class="form-control" value="${product.stor_loc ?? ''}" onchange="changeValueProduct('stor_loc', this.value)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valuation</label>
                        <input type="text" class="form-control" value="${product.valuation ?? ''}" onchange="changeValueProduct('valuation', this.value)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Net Order Price</label>
                        <input type="number" class="form-control" value="${product.net_order_price ?? 0}" onchange="changeValueProduct('net_order_price', this.value)">
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <label class="form-label">PO Item Desc</label>
                        <input type="text" class="form-control" value="${product.po_item_desc ?? ''}" onchange="changeValueProduct('po_item_desc', this.value)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prod Hierarchy Desc</label>
                        <input type="text" class="form-control" value="${product.prod_hierarchy_desc ?? ''}" onchange="changeValueProduct('prod_hierarchy_desc', this.value)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Acc Ass Cat</label>
                        <input type="text" class="form-control" value="${product.acc_ass_cat ?? ''}" onchange="changeValueProduct('acc_ass_cat', this.value)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Customer Name</label>
                        <input type="text" class="form-control" value="${product.customer_name}" onchange="changeValueProduct('customer_name', this.value)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sloc Desc</label>
                        <input type="text" class="form-control" value="${product.sloc_desc ?? ''}" onchange="changeValueProduct('sloc_desc', this.value)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PO Item QTY</label>
                        <input type="number" class="form-control" value="${product.po_item_qty ?? 0}" onchange="changeValueProduct('po_item_qty', this.value)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Currency</label>
                        <input type="text" class="form-control" value="${product.currency ?? ''}" onchange="changeValueProduct('currency', this.value)">
                    </div>
                </div>
                <div class="col-12 mb-1">
                    <label class="form-label">Note</label>
                    <textarea class="form-control" onchange="changeValueProduct('note', this.value)">${product.note}</textarea>
                </div>
            `;
        }

        function changeValueProduct(type, value) {
            const product = JSON.parse(localStorage.getItem('product')) ?? [];

            product[type] = value;
            localStorage.setItem('product', JSON.stringify(product));
        }

        function addEditProduct() {
            const editProducts = JSON.parse(localStorage.getItem('editProducts')) ?? [];
            const product = JSON.parse(localStorage.getItem('product')) ?? [];

            editProducts.push({
                'ket': 'edit',
                'data': product
            });

            localStorage.setItem('editProducts', JSON.stringify(editProducts));
            localStorage.setItem('product', JSON.stringify([]));

            viewListEditProduct();
            $('#editProductsModal').modal('hide');
        }

        function viewListEditProduct() {
            const editProducts = JSON.parse(localStorage.getItem('editProducts')) ?? [];
            let html = '';
            let number = 1;

            editProducts.forEach((item, index) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td class="text-center">${item.ket === 'edit' ? '<span class="badge bg-success-subtle text-success">Edit</span>' : '<span class="badge bg-danger-subtle text-danger">Delete</span>'}</td>
                        <td>${item.data.sales_doc}</td>
                        <td>${item.data.item}</td>
                        <td>${item.data.material}</td>
                        <td>${item.ket === 'delete' ? `<input type="text" class="form-control" value="${item.data.note}" onchange="changeNoteDelete(${index}, this.value)">` : `${item.data.note}`}</td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteEditProduct(${index})">Delete</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listEditProduct').innerHTML = html;
        }

        function deleteEditProduct(id) {
            const editProducts = JSON.parse(localStorage.getItem('editProducts')) ?? [];

            editProducts.splice(id, 1);

            localStorage.setItem('editProducts', JSON.stringify(editProducts));
            viewListEditProduct();
        }

        function changeNoteDelete(index, value) {
            const editProducts = JSON.parse(localStorage.getItem('editProducts')) ?? [];

            editProducts[index].data.note = value;

            localStorage.setItem('editProducts', JSON.stringify(editProducts));
            viewListEditProduct();
        }

        function createRequestEditPO() {
            Swal.fire({
                title: "Are you sure?",
                text: "Create Request Edit PO",
                icon: "warning",
                showCancelButton: true,
                customClass: {
                    confirmButton: "btn btn-primary w-xs me-2 mt-2",
                    cancelButton: "btn btn-danger w-xs mt-2"
                },
                confirmButtonText: "Yes, Request it!",
                buttonsStyling: false,
                showCloseButton: true
            }).then(function(t) {
                if (t.value) {

                    $.ajax({
                        url: '{{ route('inbound.edit-purchase-order-request-edit') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            editProducts: JSON.parse(localStorage.getItem('editProducts')),
                        },
                        success: (res) => {

                        }
                    });

                }
            });
        }
    </script>
@endsection
