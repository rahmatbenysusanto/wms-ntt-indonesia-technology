@extends('layout.index')
@section('title', 'Create Box')
@section('sizeBarSize', 'sm')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Create Box</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">PM Room</a></li>
                        <li class="breadcrumb-item">List</li>
                        <li class="breadcrumb-item active">Create Box</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Data Box</h4>
                        <a class="btn btn-primary" onclick="createBoxProcess()">Create Box</a>
                    </div>
                </div>
                <div class="card-body">
                    <label class="form-label">Box Name</label>
                    <input type="text" class="form-control" placeholder="Box Name" id="boxName">
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Item</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Box</th>
                                    <th>Data PO</th>
                                    <th class="text-center">QTY Item</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="listItem">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Product In Box</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Box</th>
                                    <th>Purchase Order</th>
                                    <th>Material</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">QTY</th>
                                    <th width="10%">QTY Select</th>
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
        </div>
    </div>

    <!-- Serial Number Modals -->
    <div id="serialNumberModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Serial Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <h5 class="mb-1">List Data Serial Number</h5>
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Serial Number</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="masterSerialNumber">

                                </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <h5 class="mb-1">List Serial Number Product</h5>
                            <table class="table table-striped align-middle">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Serial Number</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody id="productSerialNumber">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        localStorage.clear();

        loadListItem();
        function loadListItem() {
            const listItem = @json($listItem);
            const result = [];

            listItem.forEach((item) => {
                item.selectStatus = 0;
                result.push(item);
            });

            localStorage.setItem('listItem', JSON.stringify(listItem));
            viewListItem();
        }

        function viewListItem() {
            const listItem = JSON.parse(localStorage.getItem('listItem')) ?? [];
            let html = '';
            let number = 1;

            listItem.forEach((item, index) => {
                let salesDoc = '';
                const dataSalesDoc = JSON.parse(item.sales_docs) ?? [];
                dataSalesDoc.forEach((sales) => {
                    salesDoc += `<div>${sales}</div>`;
                });

                html += `
                    <tr>
                        <td>${number}</td>
                        <td>
                            <div><b>${item.number}</b></div>
                            <div><b>Box: </b>${item.reff_number}</div>
                        </td>
                        <td>
                            <div><b>Purc Doc: </b>${item.purchase_order.purc_doc}</div>
                            <div><b>Sales Doc: </b></div>
                            ${salesDoc}
                        </td>
                        <td class="text-center fw-bold">${item.qty_item}</td>
                        <td><a class="btn btn-info btn-sm" onclick="pilihItem(${index})">Pilih</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listItem').innerHTML = html;
        }

        function pilihItem(index) {
            const listItem = JSON.parse(localStorage.getItem('listItem')) ?? [];
            const products = JSON.parse(localStorage.getItem('products')) ?? [];

            (listItem[index].inventory_package_item).forEach((product) => {
                products.push({
                    inventoryPackageId: listItem[index].id,
                    purcDoc: listItem[index].purchase_order.purc_doc,
                    salesDoc: product.purchase_order_detail.sales_doc,
                    number: listItem[index].number,
                    reffNumber: listItem[index].reff_number,
                    inventoryPackageItemId: product.id,
                    productId: product.product_id,
                    purchaseOrderId: listItem[index].purchase_order_id,
                    purchaseOrderDetailId: product.purchase_order_detail.id,
                    is_parent: product.is_parent,
                    qty: product.qty,
                    qtySelect: 0,
                    listSerialNumber: product.inventory_package_item_sn,
                    serialNumber: [],
                    item: product.purchase_order_detail.item,
                    material: product.purchase_order_detail.material,
                    poItemDesc: product.purchase_order_detail.po_item_desc,
                    hierarchy: product.purchase_order_detail.prod_hierarchy_desc
                });
            });

            listItem[index].selectStatus = 1;
            localStorage.setItem('listItem', JSON.stringify(listItem));
            localStorage.setItem('products', JSON.stringify(products));

            viewListProduct();
        }

        function viewListProduct() {
            const products = JSON.parse(localStorage.getItem('products')) ?? [];
            let html = '';
            let number = 1;

            products.forEach((product, index) => {
                html += `
                     <tr>
                        <td>${number}</td>
                        <td>
                            <div>${product.number}</div>
                            <div><b>Box: </b>${product.reffNumber}</div>
                        </td>
                        <td>
                            <div><b>PO: </b>${product.purcDoc}</div>
                            <div><b>SO: </b>${product.salesDoc}</div>
                        </td>
                        <td>
                            <div><b>${product.material}</b></div>
                            <div>${product.poItemDesc}</div>
                            <div>${product.hierarchy}</div>
                        </td>
                        <td class="text-center">${product.isParent === 1 ? '<span class="badge bg-danger-subtle text-danger"> Parent </span>' : '<span class="badge bg-secondary-subtle text-secondary"> Child </span>'}</td>
                        <td class="text-center fw-bold">${product.qty}</td>
                        <td><input type="number" class="form-control" value="${product.qtySelect}" onchange="changeQtySelect(${index}, this.value)"></td>
                        <td><a class="btn btn-info btn-sm" onclick="serialNumber(${index})">Serial Number</a></td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteProducts(${index})">Delete</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listProducts').innerHTML = html;
        }

        function changeQtySelect(index, value) {
            const products = JSON.parse(localStorage.getItem('products')) ?? [];

            products[index].qtySelect = parseInt(value);

            localStorage.setItem('products', JSON.stringify(products));
            viewListProduct();
        }

        function deleteProducts(index) {
            const products = JSON.parse(localStorage.getItem('products')) ?? [];

            products.splice(index, 1);

            localStorage.setItem('products', JSON.stringify(products));
            viewListProduct();
        }

        function serialNumber(index) {
            viewSerialNumber(index);
            $('#serialNumberModal').modal('show');
        }

        function viewSerialNumber(index) {
            const products = JSON.parse(localStorage.getItem('products')) ?? [];

            // Serial Number Master
            let dataSN = '';
            let numberDataSN = 1;
            (products[index].listSerialNumber).forEach((sn, indexSN) => {
                dataSN += `
                    <tr>
                        <td>${numberDataSN}</td>
                        <td>${sn.serial_number}</td>
                        <td><a class="btn btn-info btn-sm" onclick="pilihSN(${index}, ${indexSN})">Pilih</a></td>
                    </tr>
                `;
                numberDataSN++;
            });
            document.getElementById('masterSerialNumber').innerHTML = dataSN;

            // Serial Number Product
            let dataProductSN = '';
            let numberDataProductSN = 1;
            (products[index].serialNumber).forEach((sn, indexSN) => {
                dataProductSN += `
                    <tr>
                        <td>${numberDataProductSN}</td>
                        <td>${sn.serial_number}</td>
                        <td><a class="btn btn-info btn-sm" onclick="deleteSN(${index}, ${indexSN})">Delete</a></td>
                    </tr>
                `;
                numberDataProductSN++;
            });
            document.getElementById('productSerialNumber').innerHTML = dataProductSN;
        }

        function pilihSN(index, indexSN) {
            const products = JSON.parse(localStorage.getItem('products')) ?? [];
            const serialNumber = products[index].listSerialNumber[indexSN];
            products[index].serialNumber.push(serialNumber);

            localStorage.setItem('products', JSON.stringify(products));
            viewSerialNumber(index);
        }

        function deleteSN(index, indexSN) {
            const products = JSON.parse(localStorage.getItem('products')) ?? [];
            products[index].serialNumber.splice(indexSN, 1);
            localStorage.setItem('products', JSON.stringify(products));
            viewSerialNumber(index);
        }

        function createBoxProcess() {
            Swal.fire({
                title: "Are you sure?",
                text: "Create Box Product",
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

                    const products = JSON.parse(localStorage.getItem('products')) ?? [];
                    const purchaseOrder = [];
                    const salesDoc = [];
                    let qty = 0;

                    products.forEach((product) => {
                        purchaseOrder.push(product.purchaseOrderId);
                        salesDoc.push(product.salesDoc);
                        qty += product.qtySelect;
                    });

                    $.ajax({
                        url: '{{ route('pm-room.create-box-store') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            products: JSON.parse(localStorage.getItem('products')) ?? [],
                            boxName: document.getElementById('boxName').value,
                            purchaseOrder: purchaseOrder,
                            salesDoc: salesDoc ?? [],
                            qty: qty
                        },
                        success: (res) => {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Create Box Success',
                                    icon: 'success'
                                }).then((i) => {
                                    window.location.href = '{{ route('pm-room.index') }}';
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Create Box Failed',
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
