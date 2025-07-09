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
                            </tr>
                        </thead>
                        <tbody id="listSnBox">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <a class="btn btn-primary" onclick="simpanSerialNumber()">Simpan Serial Number</a>
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
        async function loadDataProducts() {
            const products = @json($products);
            const listProduct = [];

            for (const parent of (products.product_parent_detail)) {
                const dataSN = await getSerialNumber('parent', parent.product_parent_id, parent.id);
                const serialNumber = [];
                (dataSN.data).forEach((sn) => {
                    sn.select = 0;
                    serialNumber.push(sn);
                })

                listProduct.push({
                    productParentDetailId: parent.id,
                    productParentId: parent.product_parent_id,
                    purchaseOrderDetailId: parent.purchase_order_detail_id,
                    salesDoc: parent.sales_doc,
                    qty: parent.qty,
                    qtyPa: 0,
                    item: parent.purchase_order_detail.item,
                    material: products.product.material,
                    poItemDesc: products.product.po_item_desc,
                    prodHierarchyDesc: products.product.prod_hierarchy_desc,
                    productId: parent.product_id,
                    type: 'parent',
                    serialNumber: serialNumber
                });
            }

            for (const child of (products.product_child)) {
                for (const childDetail of (child.product_child_detail)) {
                    const dataSN = await getSerialNumber('child', childDetail.product_child_id, childDetail.id);
                    const serialNumber = [];
                    (dataSN.data).forEach((sn) => {
                        sn.select = 0;
                        serialNumber.push(sn);
                    })

                    listProduct.push({
                        productChildDetailId: childDetail.id,
                        productChildId: childDetail.product_child_id,
                        purchaseOrderDetailId: childDetail.purchase_order_detail_id,
                        salesDoc: childDetail.sales_doc,
                        qty: childDetail.qty,
                        qtyPa: 0,
                        item: childDetail.purchase_order_detail.item,
                        material: child.product.material,
                        poItemDesc: child.product.po_item_desc,
                        prodHierarchyDesc: child.product.prod_hierarchy_desc,
                        productId: childDetail.product_id,
                        type: 'child',
                        serialNumber: serialNumber
                    });
                }
            }

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
                        <td>${product.type === 'parent' ? '<span class="badge bg-info-subtle text-info">Parent</span>' : ''}</td>
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
                        <td>${sn.serial_number}</td>
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
                            <td>${item.type === 'parent' ? '<span class="badge bg-info-subtle text-info">Parent</span>' : ''}</td>
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
                if (item.type === 'parent') {
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
                            <td><span class="badge bg-info-subtle text-info">Parent</span></td>
                            <td>${parent.item}</td>
                            <td>${parent.salesDoc}</td>
                            <td>${parent.material}</td>
                            <td class="text-center fw-bold">${parent.qtySelect}</td>
                            <td><a class="btn btn-info btn-sm" onclick="boxSerialNumber('parent', '${index}', '${indexParent}')">Detail Serial Number</a></td>
                            <td><a class="btn btn-danger btn-sm">Delete</a></td>
                        </tr>
                    `;
                });

                (item.child).forEach((child, indexChild) => {
                    html += `
                        <tr>
                            <td class="text-center fw-bold"></td>
                            <td></td>
                            <td>${child.item}</td>
                            <td>${child.salesDoc}</td>
                            <td>${child.material}</td>
                            <td class="text-center fw-bold">${child.qtySelect}</td>
                            <td><a class="btn btn-info btn-sm" onclick="boxSerialNumber('child', '${index}', '${indexChild}')">Detail Serial Number</a></td>
                            <td><a class="btn btn-danger btn-sm">Delete</a></td>
                        </tr>
                    `;
                });
            });

            document.getElementById('listBox').innerHTML = html;
        }

        function boxSerialNumber(type, index, indexDetail) {
            const box = JSON.parse(localStorage.getItem('box')) ?? [];
            const master = JSON.parse(localStorage.getItem('master')) ?? [];
            let SNAvailable = [];

            if (type === 'parent') {
                document.getElementById('boxSerialNumber_item').innerText = box[index].parent[indexDetail].item;
                document.getElementById('boxSerialNumber_salesDoc').innerText = box[index].parent[indexDetail].salesDoc;
                document.getElementById('boxSerialNumber_material').innerText = box[index].parent[indexDetail].material;
                document.getElementById('boxSerialNumber_qty').innerText = box[index].parent[indexDetail].qtySelect;
                document.getElementById('boxSerialNumber_type').value = box[index].parent[indexDetail].type;
                document.getElementById('boxSerialNumber_index').value = index;
                document.getElementById('boxSerialNumber_indexDetail').value = indexDetail;

                localStorage.setItem('serialNumber', JSON.stringify(box[index].parent[indexDetail].serialNumber));

                const listSN = master[box[index].parent[indexDetail].index].serialNumber;
                SNAvailable = listSN.filter(i => parseInt(i.select) === 0);
            } else {
                document.getElementById('boxSerialNumber_item').innerText = box[index].child[indexDetail].item;
                document.getElementById('boxSerialNumber_salesDoc').innerText = box[index].child[indexDetail].salesDoc;
                document.getElementById('boxSerialNumber_material').innerText = box[index].child[indexDetail].material;
                document.getElementById('boxSerialNumber_qty').innerText = box[index].child[indexDetail].qtySelect;
                document.getElementById('boxSerialNumber_type').value = box[index].child[indexDetail].type;
                document.getElementById('boxSerialNumber_index').value = index;
                document.getElementById('boxSerialNumber_indexDetail').value = indexDetail;

                localStorage.setItem('serialNumber', JSON.stringify(box[index].child[indexDetail].serialNumber));

                const listSN = master[box[index].child[indexDetail].index].serialNumber;
                SNAvailable = listSN.filter(i => parseInt(i.select) === 0);
            }

            // Load Serial Number Master
            console.log(SNAvailable);
            let html = '<option value="">Pilih Serial Number</option>';
            SNAvailable.forEach((sn) => {
                html+= `<option>${sn.serial_number}</option>`;
            });

            document.getElementById('selectSerialNumber').innerHTML = html;

            viewBoxSerialNumber();
            $('#boxSerialNumberModal').modal('show');
        }

        function viewBoxSerialNumber() {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];
            let html = '';

            serialNumber.forEach((sn, index) => {
                html +=
                `    <tr>
                        <td><input type="text" class="form-control" value="${sn}" onchange="changeSerialNumber(${index}, this.value)"></td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteSerialNumber(${index})">Delete</a></td>
                    </tr>
                `;
            });

            document.getElementById('listSnBox').innerHTML = html;
        }

        function changeSerialNumber(index, value) {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            serialNumber[index] = value;

            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            viewBoxSerialNumber();
        }

        function deleteSerialNumber(index) {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            serialNumber.splice(index, 1);

            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            viewBoxSerialNumber();
        }

        function tambahManualSerialNumber() {
            const box = JSON.parse(localStorage.getItem('box')) ?? [];
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            serialNumber.push("");

            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            viewBoxSerialNumber();
        }

        function pilihSerialNumber() {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];
            const valueSN = document.getElementById('selectSerialNumber').value;

            if (serialNumber.includes(valueSN)) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Serial Number Sudah ada didalam list',
                    icon: 'warning'
                });

                return true;
            }

            serialNumber.push(valueSN);

            document.getElementById('selectSerialNumber').value = "";
            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            viewBoxSerialNumber();
        }

        function simpanSerialNumber() {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];
            const box = JSON.parse(localStorage.getItem('box')) ?? [];
            const master = JSON.parse(localStorage.getItem('master')) ?? [];
            const qtyProduct = document.getElementById('boxSerialNumber_qty').value;

            if (serialNumber.length > parseInt(qtyProduct)) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Serial Number melebihi qty product',
                    icon: 'error'
                });

                return true;
            }

            const type = document.getElementById('boxSerialNumber_type').value;
            const index = document.getElementById('boxSerialNumber_index').value;
            const indexDetail = document.getElementById('boxSerialNumber_indexDetail').value;

            if (type === 'parent') {
                const masterIndex = box[index].parent[indexDetail].index;
                const masterSN = master[masterIndex].serialNumber;

                // Reset Master yang ada didalam box
                (box[index].parent[indexDetail].serialNumber).forEach((sn) => {
                    const findMasterSN = masterSN.find(i => i.serial_number === sn);
                    if (findMasterSN) {
                        findMasterSN.select = 0;
                    }
                });

                box[index].parent[indexDetail].serialNumber = serialNumber;

                // Hide Serial Number Master yang dipilih
                serialNumber.forEach((sn) => {
                    const findMasterSN = masterSN.find(i => i.serial_number === sn);
                    if (findMasterSN) {
                        findMasterSN.select = 1;
                    }
                });

            } else {
                const masterIndex = box[index].child[indexDetail].index;
                const masterSN = master[masterIndex].serialNumber;

                // Reset Master yang ada didalam box
                (box[index].child[indexDetail].serialNumber).forEach((sn) => {
                    const findMasterSN = masterSN.find(i => i.serial_number === sn);
                    if (findMasterSN) {
                        findMasterSN.select = 0;
                    }
                });

                box[index].child[indexDetail].serialNumber = serialNumber;

                // Hide Serial Number Master yang dipilih
                serialNumber.forEach((sn) => {
                    const findMasterSN = masterSN.find(i => i.serial_number === sn);
                    if (findMasterSN) {
                        findMasterSN.select = 1;
                    }
                });
            }

            localStorage.setItem('serialNumber', JSON.stringify([]));
            localStorage.setItem('master', JSON.stringify(master));
            localStorage.setItem('box', JSON.stringify(box));
            $('#boxSerialNumberModal').modal('hide');
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
                        url: '',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            box: JSON.parse(localStorage.getItem('box')) ?? []
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
