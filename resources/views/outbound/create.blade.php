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
                        <div class="col-6">
                            <label class="form-label">Customer</label>
                            <select class="form-control" id="customerId">
                                <option value="">-- Select Customer --</option>
                                @foreach($customer as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Delivery Location</label>
                            <input type="text" class="form-control" id="delivLocation">
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
                                <th>Type</th>
                                <th>Purc Doc</th>
                                <th>Sales Doc</th>
                                <th>Item</th>
                                <th>Material</th>
                                <th>Desc</th>
                                <th>Hierarchy Desc</th>
                                <th>QTY</th>
                                <th>Loc</th>
                                <th>Serial Number</th>
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
                                    <th>Type</th>
                                    <th>Purc Doc</th>
                                    <th>Item</th>
                                    <th>Material</th>
                                    <th>Desc</th>
                                    <th>Prod Hierarchy Desc</th>
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

        function openModalProduct() {
            const salesDoc = @json($inventory);
            let html = '<option value="">-- Select Sales Doc --</option>';

            salesDoc.forEach((item) => {
                html += `<option>${item.sales_doc}</option>`;
            });

            document.getElementById('listSalesDoc').innerHTML = html;
            document.getElementById('listItemSalesDoc').innerHTML = '';

            $('#listProductModal').modal('show');
        }

        function changeSalesDoc(value) {
            $.ajax({
                url: '{{ route('outbound.sales-doc') }}',
                method: 'GET',
                data: {
                    salesDoc: value
                },
                success: (res) => {
                    const data = res.data;
                    let html = '';
                    let number = 1;

                    data.forEach((item) => {
                        (item.products).forEach((product) => {
                            html += `
                                <tr>
                                    <td>${number}</td>
                                    <td><span class="badge bg-success-subtle text-success">Parent</span></td>
                                    <td>${product.sales_doc}</td>
                                    <td>${product.purchase_order_detail.item}</td>
                                    <td>${product.purchase_order_detail.material}</td>
                                    <td>${product.purchase_order_detail.po_item_desc}</td>
                                    <td>${product.purchase_order_detail.prod_hierarchy_desc}</td>
                                    <td class="text-center fw-bold">${product.qty}</td>
                                    <td>${item.storage.raw} - ${item.storage.area} - ${item.storage.rak} - ${item.storage.bin}</td>
                                    <td><a class="btn btn-success btn-sm" id="btn_parent_${product.id}" onclick="pilihProduct('parent', ${product.id})">Pilih Parent</td>
                                </tr>
                            `;

                            (product.child).forEach((child) => {
                                html += `
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>${child.sales_doc}</td>
                                        <td>${child.purchase_order_detail.item}</td>
                                        <td>${child.purchase_order_detail.material}</td>
                                        <td>${child.purchase_order_detail.po_item_desc}</td>
                                        <td>${child.purchase_order_detail.prod_hierarchy_desc}</td>
                                        <td class="text-center fw-bold">${child.qty}</td>
                                        <td>${item.storage.raw} - ${item.storage.area} - ${item.storage.rak} - ${item.storage.bin}</td>
                                        <td><a class="btn btn-info btn-sm" id="btn_child_${child.id}" onclick="pilihProduct('child', null, ${child.id})">Pilih</td>
                                    </tr>
                                `;
                            });

                            number++;
                        });
                    });

                    document.getElementById('listItemSalesDoc').innerHTML = html;
                }
            });
        }

        function pilihProduct(type, id, idChild) {
            $.ajax({
                url: '{{ route('outbound.inventory-detail') }}',
                method: 'GET',
                data: {
                    id: id,
                    idChild: idChild,
                    type: type
                },
                success: (res) => {
                    const data = res.data;
                    const listItem = JSON.parse(localStorage.getItem('listItem')) ?? [];
                    const check = listItem.find(item => item.id === id);

                    if (check) {
                        Swal.fire({
                            title: 'Warning',
                            text: 'Produk Sudah Ada didalam List Order',
                            icon: 'warning'
                        });

                        return true;
                    }

                    if (type === 'parent') {
                        listItem.push({
                            id: data.id,
                            inventoryId: data.inventory_id,
                            purchaseOrderDetailId: data.purchase_order_detil_id,
                            qualityControlDetailId: data.quality_control_detail_id,
                            type: 'parent',
                            purcDoc: data.purc_doc,
                            salesDoc: data.sales_doc,
                            qty: data.qty,
                            max: data.qty,
                            productId: data.purchase_order_detail.product_id,
                            item: data.purchase_order_detail.item,
                            material: data.purchase_order_detail.material,
                            desc: data.purchase_order_detail.po_item_desc,
                            hie: data.purchase_order_detail.prod_hierarchy_desc,
                            storageId: data.inventory.storage.id,
                            storage: data.inventory.storage.raw + ' - ' + data.inventory.storage.area + ' - ' + data.inventory.storage.rak + ' - ' + data.inventory.storage.bin,
                            sn: null
                        });

                        document.getElementById(`btn_parent_${id}`).style.display = 'none';

                        (data.child).forEach((child) => {
                            listItem.push({
                                id: child.id,
                                inventoryId: child.inventory_id,
                                purchaseOrderDetailId: child.purchase_order_detil_id,
                                qualityControlDetailId: child.quality_control_detail_id,
                                type: 'child',
                                purcDoc: child.purc_doc,
                                salesDoc: child.sales_doc,
                                qty: child.qty,
                                max: child.qty,
                                productId: child.purchase_order_detail.product_id,
                                item: child.purchase_order_detail.item,
                                material: child.purchase_order_detail.material,
                                desc: child.purchase_order_detail.po_item_desc,
                                hie: child.purchase_order_detail.prod_hierarchy_desc,
                                storageId: child.inventory.storage.id,
                                storage: child.inventory.storage.raw + ' - ' + child.inventory.storage.area + ' - ' + child.inventory.storage.rak + ' - ' + child.inventory.storage.bin,
                                sn: null
                            });

                            document.getElementById(`btn_child_${child.id}`).style.display = 'none';
                        });
                    } else {
                        listItem.push({
                            id: data.id,
                            inventoryId: data.inventory_id,
                            purchaseOrderDetailId: data.purchase_order_detil_id,
                            qualityControlDetailId: data.quality_control_detail_id,
                            type: 'child',
                            purcDoc: data.purc_doc,
                            salesDoc: data.sales_doc,
                            qty: data.qty,
                            max: data.qty,
                            productId: data.purchase_order_detail.product_id,
                            item: data.purchase_order_detail.item,
                            material: data.purchase_order_detail.material,
                            desc: data.purchase_order_detail.po_item_desc,
                            hie: data.purchase_order_detail.prod_hierarchy_desc,
                            storageId: data.inventory.storage.id,
                            storage: data.inventory.storage.raw + ' - ' + data.inventory.storage.area + ' - ' + data.inventory.storage.rak + ' - ' + data.inventory.storage.bin,
                            sn: null
                        });

                        document.getElementById(`btn_child_${data.id}`).style.display = 'none';
                    }

                    localStorage.setItem('listItem', JSON.stringify(listItem));
                    viewListItem();
                }
            });
        }

        function viewListItem() {
            const listItem = JSON.parse(localStorage.getItem('listItem')) ?? [];
            let html = '';
            let number = 1;

            listItem.forEach((item, index) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td>${item.type === 'parent' ? '<span class="badge bg-success-subtle text-success">Parent</span>' : ""}</td>
                        <td>${item.purcDoc}</td>
                        <td>${item.salesDoc}</td>
                        <td>${item.item}</td>
                        <td>${item.material}</td>
                        <td>${item.desc}</td>
                        <td>${item.hie}</td>
                        <td><input type="number" class="form-control" value="${item.qty}" onchange="changeQTY(${index}, this.value)"></td>
                        <td>${item.storage}</td>
                        <td>${item.sn === null ? `<a class="btn btn-info btn-sm" onclick="uploadSN(${index})">Upload SN</a>` : `<a class="btn btn-success btn-sm" onclick="detailSN(${index})">Detail SN</a>`}</td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteListItem(${index})">Delete</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listItem').innerHTML = html;
        }

        function deleteListItem(index) {
            const listItem = JSON.parse(localStorage.getItem('listItem')) ?? [];

            listItem.splice(index, 1);

            localStorage.setItem('listItem', JSON.stringify(listItem));
            viewListItem();
        }

        function uploadSN(index) {
            const listItem = JSON.parse(localStorage.getItem('listItem')) ?? [];
            localStorage.setItem('serialNumber', JSON.stringify([]));
            viewSerialNumber();

            document.getElementById('SN_index').value = index;
            document.getElementById('SN_item').innerText = listItem[index].item;
            document.getElementById('SN_material').innerText = listItem[index].material;
            document.getElementById('SN_desc').innerText = listItem[index].desc;
            document.getElementById('SN_hie').innerText = listItem[index].hie;

            $('#uploadSerialNumberModal').modal('show');
        }

        function processDateUploadSN() {
            const fileInput = document.getElementById('uploadFileSN');
            const file = fileInput.files[0];

            if (!file) {
                alert("Silakan pilih file Excel terlebih dahulu.");
                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                localStorage.setItem('serialNumber', JSON.stringify([]));

                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });

                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];

                const jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: "" });

                const filteredData = jsonData.map((row) => ({
                    serialNumber: row["Serial Number"],
                }));

                localStorage.setItem('serialNumber', JSON.stringify(filteredData));
                viewSerialNumber();
            };

            reader.readAsArrayBuffer(file);
        }

        function viewSerialNumber() {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];
            let html = '';
            let number = 1;

            serialNumber.forEach((sn, index) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td><input type="text" class="form-control" value="${sn.serialNumber}" onchange="changeSN(${index}, this.value)"></td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteSN(${index})">Delete</a></td>
                    </tr>
                `;
                number++;
            });

            document.getElementById('listSerialNumberUpload').innerHTML = html;
        }

        function deleteSN(index) {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            serialNumber.splice(index, 1);

            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            viewSerialNumber();
        }

        function changeSN(index, value) {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            serialNumber[index].serialNumber = value;

            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            viewSerialNumber();
        }

        function uploadSerialNumberProcess() {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];
            const listItem = JSON.parse(localStorage.getItem('listItem')) ?? [];

            const index = document.getElementById('SN_index').value;

            if (parseInt(listItem[index].qty) !== serialNumber.length) {
                Swal.fire({
                    title: 'Warning',
                    text: 'Jumlah serial number harus sama dengan qty product',
                    icon: 'warning'
                });

                return true;
            }

            listItem[index].sn = serialNumber;

            localStorage.setItem('listItem', JSON.stringify(listItem));
            localStorage.setItem('serialNumber', JSON.stringify([]));

            $('#uploadSerialNumberModal').modal('hide');
            viewListItem();
        }

        function changeQTY(index, value) {
            const listItem = JSON.parse(localStorage.getItem('listItem')) ?? [];

            listItem[index].qty = parseInt(value);

            localStorage.setItem('listItem', JSON.stringify(listItem));
            viewListItem();
        }

        function detailSN(index) {
            const listItem = JSON.parse(localStorage.getItem('listItem')) ?? [];

            document.getElementById('detail_SN_item').innerText = listItem[index].item;
            document.getElementById('detail_SN_material').innerText = listItem[index].material;
            document.getElementById('detail_SN_desc').innerText = listItem[index].desc;
            document.getElementById('detail_SN_hie').innerText = listItem[index].hie;

            let html = '';
            let number = 1;

            (listItem[index].sn).forEach((sn) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td>${sn.serialNumber}</td>
                    </tr>
                `;
                number++;
            });

            document.getElementById('listDetailSerialNumberUpload').innerHTML = html;

            $('#detailSerialNumberModal').modal('show');
        }

        function tambahSerialNumberManual() {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            serialNumber.push({
                serialNumber: ''
            });

            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            viewSerialNumber();
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
                            listItem: JSON.parse(localStorage.getItem('listItem')),
                            delivLocation: document.getElementById('delivLocation').value,
                            customerId: document.getElementById('customerId').value
                        },
                        success: (res) => {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Success',
                                    text: 'Create Order Successfully',
                                    icon: 'success'
                                }).then((e) => {
                                    window.location.href = '{{ route('outbound.index') }}';
                                });
                            }
                        }
                    });

                }
            });
        }
     </script>
@endsection
