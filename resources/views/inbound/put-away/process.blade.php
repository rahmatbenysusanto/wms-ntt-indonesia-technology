@extends('layout.index')
@section('title', 'Put Away')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Put Away process</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item active">Put Away process</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Item</h4>
                        <a class="btn btn-primary" onclick="processPutAway()">Process Put Away</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center">Item</th>
                                    <th>Sales Doc</th>
                                    <th class="text-center">Type</th>
                                    <th>Material</th>
                                    <th>Po Item Desc</th>
                                    <th>Prod Hierarchy Desc</th>
                                    <th class="text-center">QTY</th>
                                    <th>Serial Number</th>
                                </tr>
                            </thead>
                            <tbody id="listProductMaster">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Storage Location</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <label class="form-label">Raw</label>
                            <select class="form-control" onchange="changeRaw(this.value)" id="raw">
                                <option value="">-- Select Raw --</option>
                                @foreach($storageRaw as $raw)
                                    <option value="{{ $raw->raw }}">{{ $raw->raw }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label class="form-label">Area</label>
                            <select class="form-control" id="area" onchange="changeArea(this.value)">

                            </select>
                        </div>
                        <div class="col-3">
                            <label class="form-label">Rak</label>
                            <select class="form-control" id="rak" onchange="changeRak(this.value)">

                            </select>
                        </div>
                        <div class="col-3">
                            <label class="form-label">Bin</label>
                            <select class="form-control" id="bin" name="bin">

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
                        <h4 class="card-title mb-0">Set Box Product</h4>
                        <a class="btn btn-info btn-sm" onclick="addBox()">Add Box</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th class="text-center">Box Number</th>
                                <th>Type</th>
                                <th>Item</th>
                                <th>Sales Doc</th>
                                <th>Material</th>
                                <th class="text-center">QTY</th>
                                <th>Serial Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="listBox">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Box Modals -->
    <div id="addBoxModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Add Box Put Away</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Item</th>
                                <th>Sales Doc</th>
                                <th>Material</th>
                                <th>QTY</th>
                                <th>QTY Item In Box</th>
                            </tr>
                        </thead>
                        <tbody id="listMaterialAddBox">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addBoxProcess()">Add Box Process</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail SN Master Modals -->
    <div id="detailSerialNumberModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Detail Serial Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <table>
                        <tr>
                            <td class="fw-bold">Item</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1" id="detailSNMaster_item"></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Sales Doc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1" id="detailSNMaster_salesDoc"></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Material</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1" id="detailSNMaster_material"></td>
                        </tr>
                    </table>

                    <table class="table table-striped mt-3">
                        <thead>
                            <tr>
                                <th>Serial Number</th>
                            </tr>
                        </thead>
                        <tbody id="listSerialNumberMaster">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Box Serial Number Modals -->
    <div id="boxSerialNumberModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Detail Serial Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="boxSerialNumber_type">
                    <input type="hidden" id="boxSerialNumber_index">
                    <input type="hidden" id="boxSerialNumber_indexDetail">
                    <input type="hidden" id="boxSerialNumber_indexMaster">

                    <table>
                        <tr>
                            <td class="fw-bold">Item</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1" id="boxSerialNumber_item"></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Sales Doc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1" id="boxSerialNumber_salesDoc"></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Material</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1" id="boxSerialNumber_material"></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">QTY</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1" id="boxSerialNumber_qty"></td>
                        </tr>
                    </table>

                    <div class="row mt-3">
                        <div class="col-10">
                            <div class="row">
                                <div class="col-8">
                                    <select class="form-control" id="selectSerialNumber"></select>
                                </div>
                                <div class="col-4">
                                    <a class="btn btn-info w-100" onclick="pilihSerialNumber()">Pilih SN</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-2">
                            <a class="btn btn-secondary w-100" onclick="tambahManualSerialNumber()">Tambah Manual</a>
                        </div>
                    </div>

                    <table class="table table-striped align-middle mt-3">
                        <thead>
                            <tr>
                                <th>Serial Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="listSnBox">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Set Location Modals -->
    <div id="setLocationModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Set Location Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('inbound.put-away.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="setLocationId">
                        <input type="hidden" name="number" value="{{ request()->get('number') }}">
                        <div class="mb-3">
                            <label class="form-label">Raw</label>
                            <select class="form-control" onchange="changeRaw(this.value)" id="raw">
                                <option value="">-- Select Raw --</option>
                                @foreach($storageRaw as $raw)
                                    <option value="{{ $raw->raw }}">{{ $raw->raw }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Area</label>
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
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Set Location</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        localStorage.clear();

        loadDataProducts();
        function loadDataProducts() {
            const products = @json($products);
            console.log(products);
            const listProduct = [];

            products.forEach((product, index) => {
                const serialNumber = [];
                (product.product_package_item_sn).forEach((sn) => {
                    serialNumber.push({
                        serialNumber: sn.serial_number,
                        select: 0
                    });
                });

                listProduct.push({
                    productPackageId: product.product_poackage_id,
                    productPackageItemId: product.id,
                    purchaseOrderDetailId: product.purchase_order_detail_id,
                    salesDoc: product.purchase_order_detail.sales_doc,
                    isParent: product.is_parent,
                    qty: product.qty,
                    qtyPa: 0,
                    productId: product.product_id,
                    item: product.purchase_order_detail.item,
                    material: product.purchase_order_detail.material,
                    poItemDesc: product.purchase_order_detail.po_item_desc,
                    prodHierarchyDesc: product.purchase_order_detail.prod_hierarchy_desc,
                    serialNumber: serialNumber,
                    indexMaster: index
                });
            });

            localStorage.setItem('master', JSON.stringify(listProduct));
            viewProductMaster();
        }

        function getSerialNumber(type, id, detail) {
            return $.ajax({
                url: '{{ route('inbound.put-away.find-serial-number') }}',
                method: 'GET',
                data: {
                    type: type,
                    id: id,
                    detail: detail
                }
            });
        }

        function viewProductMaster() {
            const products = JSON.parse(localStorage.getItem('master')) ?? [];
            let html = '';

            products.forEach((product, index) => {
                html += `
                    <tr>
                        <td class="text-center">${product.item}</td>
                        <td>${product.salesDoc}</td>
                        <td class="text-center">${product.isParent === 1 ? '<span class="badge bg-danger-subtle text-danger">Parent</span>' : '<span class="badge bg-secondary-subtle text-secondary">Child</span>'}</td>
                        <td>${product.material}</td>
                        <td>${product.poItemDesc}</td>
                        <td>${product.prodHierarchyDesc}</td>
                        <td class="text-center fw-bold">${product.qty}</td>
                        <td><a class="btn btn-primary btn-sm" onclick="detailSerialNumberMaster(${index})">Detail Serial Number</a></td>
                    </td>
                `;
            });

            document.getElementById('listProductMaster').innerHTML = html;
        }

        function detailSerialNumberMaster(index) {
            const products = JSON.parse(localStorage.getItem('master')) ?? [];
            let html = '';

            (products[index].serialNumber).forEach((sn) => {
                html += `
                    <tr>
                        <td>${sn.serialNumber}</td>
                    </tr>
                `;
            });

            document.getElementById('detailSNMaster_item').innerText = products[index].item;
            document.getElementById('detailSNMaster_salesDoc').innerText = products[index].salesDoc;
            document.getElementById('detailSNMaster_material').innerText = products[index].material;

            document.getElementById('listSerialNumberMaster').innerHTML = html;
            $('#detailSerialNumberModal').modal('show');
        }

        function addBox() {
            const master = JSON.parse(localStorage.getItem('master')) ?? [];

            const addBox = [];
            master.forEach((item, index) => {
                if (parseInt(item.qty) !== parseInt(item.qtyPa)) {
                    item.index = index;
                    item.qtySelect = 0;
                    addBox.push(item);
                }
            });

            localStorage.setItem('addBox', JSON.stringify(addBox));

            viewMasterProductModal();
            $('#addBoxModal').modal('show');
        }

        function viewMasterProductModal() {
            const addBox = JSON.parse(localStorage.getItem('addBox')) ?? [];
            let html = '';
            let number = 1;

            addBox.forEach((item, index) => {
                html += `
                        <tr>
                            <td>${number}</td>
                            <td>${item.isParent === 1 ? '<span class="badge bg-danger-subtle text-danger">Parent</span>' : '<span class="badge bg-secondary-subtle text-secondary">Child</span>'}</td>
                            <td>${item.item}</td>
                            <td>${item.salesDoc}</td>
                            <td>${item.material}</td>
                            <td>${item.qty - item.qtyPa}</td>
                            <td><input type="number" class="form-control" value="${item.qtySelect}" onchange="changeQtyPa(${index}, this.value)"></td>
                        </tr>
                    `;
                number++;
            });

            document.getElementById('listMaterialAddBox').innerHTML = html;
        }

        function changeQtyPa(index, value) {
            const addBox = JSON.parse(localStorage.getItem('addBox')) ?? [];

            addBox[index].qtySelect = value;

            localStorage.setItem('addBox', JSON.stringify(addBox));
            viewMasterProductModal();
        }

        function addBoxProcess() {
            const box = JSON.parse(localStorage.getItem('box')) ?? [];
            const addBox = JSON.parse(localStorage.getItem('addBox'));
            const master = JSON.parse(localStorage.getItem('master')) ?? [];
            const parent = [];
            const child = [];

            addBox.forEach((item) => {
                if (parseInt(item.isParent) === 1) {
                    if (parseInt(item.qtySelect) !== 0) {
                        // Update QTY Master
                        master[item.index].qtyPa = parseInt(master[item.index].qtyPa) + parseInt(item.qtySelect);
                        item.serialNumber = [];

                        parent.push(item);
                    }
                } else {
                    if (parseInt(item.qtySelect) !== 0) {
                        // Update QTY Master
                        master[item.index].qtyPa = parseInt(master[item.index].qtyPa) + parseInt(item.qtySelect);
                        item.serialNumber = [];

                        child.push(item);
                    }
                }
            });

            box.push({
                boxNumber: box.length + 1,
                parent: parent,
                child: child
            });

            localStorage.setItem('addBox', JSON.stringify([]));
            localStorage.setItem('box', JSON.stringify(box));
            localStorage.setItem('master', JSON.stringify(master));
            viewListBox();
            $('#addBoxModal').modal('hide');
        }

        function viewListBox() {
            const box = JSON.parse(localStorage.getItem('box')) ?? [];
            let html = '';

            box.forEach((item, index) => {
                (item.parent).forEach((parent, indexParent) => {
                    html += `
                        <tr>
                            <td class="text-center fw-bold">${item.boxNumber}</td>
                            <td><span class="badge bg-danger-subtle text-danger">Parent</span></td>
                            <td>${parent.item}</td>
                            <td>${parent.salesDoc}</td>
                            <td>${parent.material}</td>
                            <td class="text-center fw-bold">${parent.qtySelect}</td>
                            <td><a class="btn btn-info btn-sm" onclick="serialNumber('parent', '${index}', '${indexParent}', '${parent.indexMaster}')">Serial Number</a></td>
                            <td><a class="btn btn-danger btn-sm" onclick="deleteBox(${index})">Delete</a></td>
                        </tr>
                    `;
                });

                (item.child).forEach((child, indexChild) => {
                    html += `
                        <tr>
                            <td class="text-center fw-bold"></td>
                            <td><span class="badge bg-secondary-subtle text-secondary">Child</span></td>
                            <td>${child.item}</td>
                            <td>${child.salesDoc}</td>
                            <td>${child.material}</td>
                            <td class="text-center fw-bold">${child.qtySelect}</td>
                            <td><a class="btn btn-info btn-sm" onclick="serialNumber('child', '${index}', '${indexChild}', '${child.indexMaster}')">Serial Number</a></td>
                            <td></td>
                        </tr>
                    `;
                });
            });

            document.getElementById('listBox').innerHTML = html;
        }

        function serialNumber(type, index, indexDetail, indexMaster) {
            const box = JSON.parse(localStorage.getItem('box')) ?? [];
            const master = JSON.parse(localStorage.getItem('master')) ?? [];

            // Form Select Serial Number
            viewSelectSnAvailable(indexMaster);

            // Load Jika sudah punya SN
            if (type === 'parent') {
                document.getElementById('boxSerialNumber_item').innerText = box[index].parent[indexDetail].item;
                document.getElementById('boxSerialNumber_salesDoc').innerText = box[index].parent[indexDetail].salesDoc;
                document.getElementById('boxSerialNumber_material').innerText = box[index].parent[indexDetail].material;
                document.getElementById('boxSerialNumber_qty').innerText = box[index].parent[indexDetail].qtySelect;

                const serialNumber = box[index].parent[indexDetail].serialNumber;
                localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            } else {
                document.getElementById('boxSerialNumber_item').innerText = box[index].child[indexDetail].item;
                document.getElementById('boxSerialNumber_salesDoc').innerText = box[index].child[indexDetail].salesDoc;
                document.getElementById('boxSerialNumber_material').innerText = box[index].child[indexDetail].material;
                document.getElementById('boxSerialNumber_qty').innerText = box[index].child[indexDetail].qtySelect;

                const serialNumber = box[index].child[indexDetail].serialNumber;
                localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            }

            document.getElementById('boxSerialNumber_type').value = type;
            document.getElementById('boxSerialNumber_index').value = index;
            document.getElementById('boxSerialNumber_indexDetail').value = indexDetail;
            document.getElementById('boxSerialNumber_indexMaster').value = indexMaster;

            viewListSerialNumber();
            $('#boxSerialNumberModal').modal('show');
        }

        function viewListSerialNumber() {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];
            let html = '';

            serialNumber.forEach((sn, index) => {
                html += `
                    <tr>
                        <td><input type="text" class="form-control" value="${sn}" onchange="changeSN(${index}, this.value)"></td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteSN(${index})">Delete</a></td>
                    </tr>
                `;
            });

            document.getElementById('listSnBox').innerHTML = html;
        }

        function changeSN(indexSN, value) {
            const box = JSON.parse(localStorage.getItem('box')) ?? [];
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            const type = document.getElementById('boxSerialNumber_type').value;
            const index = document.getElementById('boxSerialNumber_index').value;
            const indexDetail = document.getElementById('boxSerialNumber_indexDetail').value;

            serialNumber[indexSN] = value;

            if (type === 'parent') {
                box[index].parent[indexDetail].serialNumber = serialNumber;
            } else {
                box[index].child[indexDetail].serialNumber = serialNumber;
            }

            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            viewListSerialNumber();
        }

        function deleteSN(indexDelete) {
            const box = JSON.parse(localStorage.getItem('box')) ?? [];
            const master = JSON.parse(localStorage.getItem('master')) ?? [];
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            const type = document.getElementById('boxSerialNumber_type').value;
            const index = document.getElementById('boxSerialNumber_index').value;
            const indexDetail = document.getElementById('boxSerialNumber_indexDetail').value;
            const indexMaster = document.getElementById('boxSerialNumber_indexMaster').value;

            const masterSN = master[indexMaster].serialNumber;
            const masterSNFind = masterSN.find(i => i.serialNumber === serialNumber[indexDelete]);
            masterSNFind.select = 0;

            serialNumber.splice(indexDelete, 1);

            if (type === 'parent') {
                box[index].parent[indexDetail].serialNumber = serialNumber;
            } else {
                box[index].child[indexDetail].serialNumber = serialNumber;
            }

            localStorage.setItem('box', JSON.stringify(box));
            localStorage.setItem('master', JSON.stringify(master));
            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));

            viewSelectSnAvailable(indexMaster);
            viewListSerialNumber();
        }

        function viewSelectSnAvailable(indexMaster) {
            const master = JSON.parse(localStorage.getItem('master')) ?? [];

            const serialNumberMaster = master[indexMaster].serialNumber;
            const serialNumberAvailable = serialNumberMaster.filter(i => parseInt(i.select) === 0);
            let htmlSelect = '<option value="">-- Pilih Serial Number --</option>';
            serialNumberAvailable.forEach((sn) => {
                htmlSelect += `<option>${sn.serialNumber}</option>`;
            });
            document.getElementById('selectSerialNumber').innerHTML = htmlSelect;
        }

        function pilihSerialNumber() {
            const box = JSON.parse(localStorage.getItem('box')) ?? [];
            const master = JSON.parse(localStorage.getItem('master')) ?? [];
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            const type = document.getElementById('boxSerialNumber_type').value;
            const index = document.getElementById('boxSerialNumber_index').value;
            const indexDetail = document.getElementById('boxSerialNumber_indexDetail').value;
            const indexMaster = document.getElementById('boxSerialNumber_indexMaster').value;

            if (type === 'parent') {
                if (parseInt(box[index].parent[indexDetail].qtySelect + 1) > serialNumber.count) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Jumlah serial number melebihi qty',
                        icon: 'warning'
                    });

                    return true;
                }
            } else {
                if (parseInt(box[index].child[indexDetail].qtySelect + 1) > serialNumber.count) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Jumlah serial number melebihi qty',
                        icon: 'warning'
                    });

                    return true;
                }
            }

            const valueSN = document.getElementById('selectSerialNumber').value;
            serialNumber.push(valueSN);
            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));

            const masterSN = master[indexMaster].serialNumber;
            const masterSNFilter = masterSN.find(i => i.serialNumber === valueSN);
            masterSNFilter.select = 1;
            localStorage.setItem('master', JSON.stringify(master));

            // Save SN to BOX
            if (type === 'parent') {
                box[index].parent[indexDetail].serialNumber = serialNumber;
            } else {
                box[index].child[indexDetail].serialNumber = serialNumber;
            }
            localStorage.setItem('box', JSON.stringify(box));

            document.getElementById('selectSerialNumber').value = "";
            viewListSerialNumber();
            viewSelectSnAvailable(indexMaster);
        }

        function tambahManualSerialNumber() {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];
            const box = JSON.parse(localStorage.getItem('box')) ?? [];

            const type = document.getElementById('boxSerialNumber_type').value;
            const index = document.getElementById('boxSerialNumber_index').value;
            const indexDetail = document.getElementById('boxSerialNumber_indexDetail').value;
            const indexMaster = document.getElementById('boxSerialNumber_indexMaster').value;

            serialNumber.push("");

            if (type === 'parent') {
                box[index].parent[indexDetail].serialNumber = serialNumber;
            } else {
                box[index].child[indexDetail].serialNumber = serialNumber;
            }

            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            viewListSerialNumber();
        }

        function deleteBox(index) {
            const box = JSON.parse(localStorage.getItem('box')) ?? [];
            const master = JSON.parse(localStorage.getItem('master')) ?? [];

            // Parent
            (box[index].parent).forEach((parent) => {
                master[parent.index].qtyPa = parseInt(master[parent.index].qtyPa) - parseInt(parent.qtySelect);

                (parent.serialNumber).forEach((sn) => {
                    const findSnMaster = master[parent.index].serialNumber.find(i => i.serial_number === sn);
                    if (findSnMaster) {
                        findSnMaster.select = 0;
                    }
                });
            });

            // Child
            (box[index].child).forEach((child) => {
                master[child.index].qtyPa = parseInt(master[child.index].qtyPa) - parseInt(child.qtySelect);

                (child.serialNumber).forEach((sn) => {
                    const findSnMaster = master[child.index].serialNumber.find(i => i.serial_number === sn);
                    if (findSnMaster) {
                        findSnMaster.select = 0;
                    }
                });
            });

            box.splice(index, 1);

            const dataBox = [];
            box.forEach((item, index) => {
                dataBox.push({
                    boxNumber: index + 1,
                    parent: item.parent,
                    child: item.child
                });
            });

            localStorage.setItem('box', JSON.stringify(dataBox));
            localStorage.setItem('master', JSON.stringify(master));
            viewListBox();
        }

        function processPutAway() {
            Swal.fire({
                title: "Are you sure?",
                text: "Process Put Away",
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

                    // Validation Products
                    const master = JSON.parse(localStorage.getItem('master')) ?? [];
                    master.forEach((item) => {
                        if (parseInt(item.qty) !== parseInt(item.qtyPa)) {
                            Swal.fire({
                                title: 'Warning!',
                                text: 'Belum Semua QTY dimasukan ke box',
                                icon: 'warning'
                            });

                            return true;
                        }
                    });

                    // Validation Storage Location

                    // Insert ke Database
                    $.ajax({
                        url: '{{ route('inbound.put-away.store') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            box: JSON.parse(localStorage.getItem('box')) ?? [],
                            bin: document.getElementById('bin').value,
                            productPackageId: '{{ request()->get('id') }}'
                        },
                        success: (res) => {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Success',
                                    text: 'Put Away Product Success',
                                    icon: 'success'
                                }).then((e) => {
                                    window.location.href = '/inbound/put-away/detail?pa-number='+res.data;
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Put Away Product Failed',
                                    icon: 'error'
                                });
                            }
                        }
                    });
                }
            });
        }

































        function setLocation(id) {
            document.getElementById('setLocationId').value = id;
            $('#setLocationModal').modal('show');
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
                    let html = '<option value="">-- Select Area --</option>';

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
    </script>
@endsection
