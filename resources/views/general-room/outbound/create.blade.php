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
                    <h4 class="card-title mb-0">Data Order</h4>
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
                            <input type="text" class="form-control" id="delivLocation" placeholder="Delivery Location">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Destination</label>
                            <select class="form-control" id="deliveryDest">
                                <option value="client">Client</option>
                                <option value="pm">PM Room</option>
                                <option value="spare">Spare Room</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Outbound Date</label>
                            <input type="datetime-local" class="form-control" id="deliveryDate">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Note Number</label>
                            <input type="text" class="form-control" id="deliveryNoteNumber" placeholder="Delivery Note Number">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" rows="2" id="note"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Sales Doc</h4>
                </div>
                <div class="card-body">
                    <table id="tabelSalesDoc" class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Sales Doc</th>
                            <th>Data Box</th>
                            <th>QTY</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="listSalesDoc">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Product Order</h4>
                        <a class="btn btn-primary" onclick="createOrder()">Create Order General Room</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th>Material</th>
                            <th class="text-center">Type</th>
                            <th>Box</th>
                            <th>Sales Doc</th>
                            <th class="text-center">QTY</th>
                            <th>QTY Outbound</th>
                            <th>Serial Number</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="listProductOutbound">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Serial Number Modals -->
    <div id="serialNumberModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Serial Number Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="card-title mb-2">Data Serial Number</h4>
                            <table class="table table-striped align-middle">
                                <thead>
                                <tr>
                                    <th>Serial Number</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody id="listDataSN">

                                </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <h4 class="card-title mb-2">Data Outbound Serial Number</h4>
                            <table class="table table-striped align-middle">
                                <thead>
                                <tr>
                                    <th>Serial Number</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody id="listDataOutboundSN">

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

        $(document).ready(function () {
            $('#tabelSalesDoc').DataTable();
        });

        loadSalesDoc();
        function loadSalesDoc() {
            const salesDoc = @json($salesDoc);

            localStorage.setItem('salesDoc', JSON.stringify(salesDoc));
            viewSalesDoc();
        }

        function viewSalesDoc() {
            const salesDoc = JSON.parse(localStorage.getItem('salesDoc')) ?? [];
            let html = '';
            let number = 1;

            salesDoc.forEach((item) => {
                let salesDoc = '';
                (JSON.parse(item.sales_docs) ?? []).forEach((detail) => {
                    salesDoc += `<div>${detail}</div>`;
                });

                html += `
                    <tr>
                        <td>${number}</td>
                        <td>${salesDoc}</td>
                        <td>
                            ${item.storage.id === 1 ? '<div><span class="badge bg-danger"> Cross Docking </span></div>' : ''}
                            <div><b>Purc Doc: </b>${item.purchase_order.purc_doc}</div>
                            <div>${item.number}</div>
                            <div><b>Box: </b>${item.reff_number}</div>
                            <div><b>Loc: </b>General Room</div>
                        </td>
                        <td>${item.qty}</td>
                        <td><a class="btn btn-info btn-sm" onclick="pilihSalesDoc(${item.id})">Pilih</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listSalesDoc').innerHTML = html;
        }

        function pilihSalesDoc(id) {
            $.ajax({
                url: '{{ route('outbound.sales-doc') }}',
                method: 'GET',
                data: {
                    id: id
                },
                success: (res => {
                    const products = [];

                    (res.data.inventory_package_item).forEach((product) => {
                        const dataSN = [];
                        (product.inventory_package_item_sn).forEach((item) => {
                            if (item.qty !== 0) {
                                dataSN.push({
                                    id: item.id,
                                    serialNumber: item.serial_number,
                                    select: 0
                                });
                            }
                        });

                        products.push({
                            inventoryPackageId: product.inventory_package_id,
                            inventoryPackageItemId: product.id,
                            purchaseOrderId: product.purchase_order_detail.purchase_order_id,
                            purchaseOrderDetailId: product.purchase_order_detail_id,
                            isParent: product.is_parent,
                            directOutbound: product.direct_outbound,
                            qty: product.qty,
                            qtySelect: 0,
                            productId: product.product_id,
                            material: product.purchase_order_detail.material,
                            poItemDesc: product.purchase_order_detail.po_item_desc,
                            prodHierarchyDesc: product.purchase_order_detail.prod_hierarchy_desc,
                            salesDoc: product.purchase_order_detail.sales_doc,
                            purcDoc: res.data.purchase_order.purc_doc,
                            dataSN: dataSN,
                            serialNumber: [],
                            number: res.data.number,
                            reffNumber: res.data.reff_number,
                            loc: 'General Room',
                            storageId: res.data.storage.id,
                            disable: 0
                        });
                    });

                    localStorage.setItem('salesDocProduct', JSON.stringify(products));
                    viewProductOutbound();
                })
            });
        }

        function viewProductOutbound() {
            const products = JSON.parse(localStorage.getItem('salesDocProduct')) ?? [];
            let html = '';

            products.forEach((item, index) => {
                if (item.disable === 0) {
                    html += `
                        <tr>
                            <td>
                                ${ item.directOutbound === 1 ? '<div><span class="badge bg-danger"> Cross Docking </span></div>' : '' }
                                <div>${item.material}</div>
                                <div>${item.poItemDesc}</div>
                                <div>${item.prodHierarchyDesc}</div>
                            </td>
                            <td class="text-center">${item.isParent === 1 ? '<span class="badge bg-danger-subtle text-danger">Parent</span>' : '<span class="badge bg-secondary-subtle text-secondary">Child</span>'}</td>
                            <td>
                                <div><b>PA: </b>${item.number}</div>
                                <div><b>Box: </b>${item.reffNumber}</div>
                                <div><b>Loc: </b>${item.loc}</div>
                            </td>
                            <td>${item.salesDoc}</td>
                            <td class="text-center fw-bold">${item.qty}</td>
                            <td><input type="number" class="form-control" onchange="changeQtySelect(${index}, this.value)" value="${item.qtySelect}"></td>
                            <td><a class="btn btn-info btn-sm" onclick="openSerialNumberModal(${index})">Serial Number</a></td>
                            <td><a class="btn btn-danger btn-sm" onclick="deleteProduct(${index})">Delete</a></td>
                        </tr>
                    `;
                }
            });

            document.getElementById('listProductOutbound').innerHTML = html;
        }

        function deleteProduct(index) {
            const products = JSON.parse(localStorage.getItem('salesDocProduct')) ?? [];

            products[index].disable = 1;

            localStorage.setItem('salesDocProduct', JSON.stringify(products));
            viewProductOutbound();
        }

        function changeQtySelect(index, value) {
            const products = JSON.parse(localStorage.getItem('salesDocProduct')) ?? [];

            if (parseInt(value) > products[index].qty) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'QTY outbound melebihi qty diinventory',
                    icon: 'warning',
                });
                viewProductOutbound();
                return true;
            }

            products[index].qtySelect = parseInt(value);

            localStorage.setItem('salesDocProduct', JSON.stringify(products));
            viewProductOutbound();
        }

        function openSerialNumberModal(index) {
            const products = JSON.parse(localStorage.getItem('salesDocProduct')) ?? [];
            const product = products[index];

            let dataSN = '';
            (product.dataSN).forEach((item, indexSN) => {
                let button = '';
                if (item.select === 0) {
                    button = `<a class="btn btn-info btn-sm" onclick="pilihSN(${index}, ${indexSN})">Pilih SN</a>`;
                }

                dataSN += `
                    <tr>
                        <td>${item.serialNumber}</td>
                        <td>${button}</td>
                    </tr>
                `;
            });

            let serialNumber = '';
            (product.serialNumber).forEach((item, indexSN) => {
                serialNumber += `
                    <tr>
                        <td>${item.serialNumber}</td>
                        <td><a class="btn btn-info btn-sm" onclick="deleteSN(${index}, ${indexSN})">Delete</a></td>
                    </tr>
                `;
            });

            document.getElementById('listDataOutboundSN').innerHTML = serialNumber;
            document.getElementById('listDataSN').innerHTML = dataSN;
            $('#serialNumberModal').modal('show');
        }

        function pilihSN(index, indexSN) {
            const products = JSON.parse(localStorage.getItem('salesDocProduct')) ?? [];
            const product = products[index];

            product.dataSN[indexSN].select = 1;
            product.serialNumber.push(product.dataSN[indexSN]);

            localStorage.setItem('salesDocProduct', JSON.stringify(products));
            viewSerialNumberReload(index);
        }

        function deleteSN(index, indexSN) {
            const products = JSON.parse(localStorage.getItem('salesDocProduct')) ?? [];
            const product = products[index];

            const findSN = product.serialNumber[indexSN];
            const findDataSN = product.dataSN.find(item => parseInt(item.id) === parseInt(findSN.id));

            findDataSN.select = 0;
            product.serialNumber.splice(indexSN, 1);

            localStorage.setItem('salesDocProduct', JSON.stringify(products));
            viewSerialNumberReload(index);
        }

        function viewSerialNumberReload(index) {
            const products = JSON.parse(localStorage.getItem('salesDocProduct')) ?? [];
            const product = products[index];

            let dataSN = '';
            (product.dataSN).forEach((item, indexSN) => {
                let button = '';
                if (item.select === 0) {
                    button = `<a class="btn btn-info btn-sm" onclick="pilihSN(${index}, ${indexSN})">Pilih SN</a>`;
                }

                dataSN += `
                    <tr>
                        <td>${item.serialNumber}</td>
                        <td>${button}</td>
                    </tr>
                `;
            });

            let serialNumber = '';
            (product.serialNumber).forEach((item, indexSN) => {
                serialNumber += `
                    <tr>
                        <td>${item.serialNumber}</td>
                        <td><a class="btn btn-info btn-sm" onclick="deleteSN(${index}, ${indexSN})">Delete</a></td>
                    </tr>
                `;
            });

            document.getElementById('listDataOutboundSN').innerHTML = serialNumber;
            document.getElementById('listDataSN').innerHTML = dataSN;
        }

        function createOrder() {
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

                    // Validation
                    const products = JSON.parse(localStorage.getItem('salesDocProduct')) ?? [];

                    // Create Order Process
                    $.ajax({
                        url: '{{ route('general-room.create.outbound') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            products: products,
                            delivLocation: document.getElementById('delivLocation').value,
                            customerId: document.getElementById('customerId').value,
                            deliveryDest: document.getElementById('deliveryDest').value,
                            deliveryDate: document.getElementById('deliveryDate').value,
                            deliveryNoteNumber: document.getElementById('deliveryNoteNumber').value,
                            note: document.getElementById('note').value
                        },
                        success: (res) => {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Success',
                                    text: 'Create Order General Room Successfully',
                                    icon: 'success'
                                }).then((e) => {
                                    window.location.href = '{{ route('general-room.outbound') }}';
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
