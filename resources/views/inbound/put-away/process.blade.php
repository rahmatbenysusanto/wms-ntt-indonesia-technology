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
                                <th>Location</th>
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
    <div id="addBoxModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
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
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>
                                    <select class="form-control" onchange="changeMassQty(this.value)">
                                        <option value="0">Choose QTY</option>
                                        @for ($i = 1; $i <= 200; $i++)
                                            <option>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </th>
                            </tr>
                        </thead>
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
    <div id="detailSerialNumberModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
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
                                <th>Status</th>
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
    <div id="boxSerialNumberModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
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
                        <tr>
                            <td class="fw-bold">QTY Scan Serial Number</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1" id="boxSerialNumber_qty_scan_sn"></td>
                        </tr>
                    </table>

                    <div class="row mt-3">
                        <div class="col-10">
                            {{--                            <div class="row"> --}}
                            {{--                                <div class="col-8"> --}}
                            {{--                                    <select class="form-control" id="selectSerialNumber"></select> --}}
                            {{--                                </div> --}}
                            {{--                                <div class="col-4"> --}}
                            {{--                                    <a class="btn btn-info w-100" onclick="pilihSerialNumber()">Pilih SN</a> --}}
                            {{--                                </div> --}}
                            {{--                            </div> --}}
                            <input type="text" class="form-control" id="scanSerialNumber"
                                placeholder="Scan Serial Number ...">
                        </div>
                        <div class="col-2">
                            <a class="btn btn-secondary w-100" onclick="tambahManualSerialNumber()">Tambah Manual</a>
                        </div>
                    </div>

                    <div id="scanSerialNumberError"
                        class="alert alert-danger alert-dismissible alert-label-icon label-arrow shadow fade show mt-2"
                        role="alert" style="display: none">
                        <i class="ri-error-warning-line label-icon"></i>
                        <strong>Error</strong> - <span id="scanSerialNumberErrorMessage"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-1">Data Serial Number Product</h5>
                                <button class="btn btn-primary btn-sm" onclick="selectAllSN()">Select All SN</button>
                            </div>
                            <table class="table table-striped align-middle mt-3">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Serial Number</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="listSerialNumberAvailable">

                                </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <h5 class="mb-1">Serial Number Product In Box</h5>
                            <table class="table table-striped align-middle mt-3">
                                <thead>
                                    <tr>
                                        <th>#</th>
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
                        select: sn.status ?? 0
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
                    qtyPaDb: product.qty_pa ?? 0,
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

            const dataLocation = @json($storage);
            const location = [];

            dataLocation.forEach((loc) => {
                location.push({
                    id: loc.id,
                    raw: loc.raw,
                    area: loc.area,
                    rak: loc.rak,
                    bin: loc.bin,
                });
            });
            localStorage.setItem('location', JSON.stringify(location));
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
                        <td class="text-center fw-bold">
                            ${product.qty}
                            ${product.qtyPaDb > 0 ? `<br><small class="text-danger">(Sisa: ${product.qty - product.qtyPaDb})</small>` : ''}
                        </td>
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
                        <td>
                            ${parseInt(sn.select) === 1 ? '<span class="badge bg-success">Done</span>' : '<span class="badge bg-warning">Open</span>'}
                        </td>
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
                if (parseInt(item.qty - item.qtyPaDb) !== parseInt(item.qtyPa)) {
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
                            <td>${(item.qty - item.qtyPaDb) - item.qtyPa}</td>
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
                const qtySelect = parseInt(item.qtySelect);
                if (qtySelect !== 0) {
                    // Update QTY Master
                    master[item.index].qtyPa = parseInt(master[item.index].qtyPa) + qtySelect;

                    // Create a deep clone to ensure this box has its own item instance
                    const itemToBox = JSON.parse(JSON.stringify(item));
                    itemToBox.serialNumber = []; // Reset SN as it's newly scanned for this box
                    itemToBox.qtySelect = qtySelect;

                    if (parseInt(itemToBox.isParent) === 1) {
                        parent.push(itemToBox);
                    } else {
                        child.push(itemToBox);
                    }
                }
            });

            box.push({
                boxNumber: box.length + 1,
                location: null,
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
                const parentsInBox = (item.parent || []).map((p, pIdx) => ({
                    ...p,
                    _type: 'parent',
                    _idxDetail: pIdx
                }));
                const childrenInBox = (item.child || []).map((c, cIdx) => ({
                    ...c,
                    _type: 'child',
                    _idxDetail: cIdx
                }));
                const allItems = [...parentsInBox, ...childrenInBox];

                allItems.forEach((subItem, subIndex) => {
                    const isFirstRow = subIndex === 0;
                    let colorBtn = 'info';
                    if (parseInt(subItem.qtySelect) === subItem.serialNumber.length) {
                        colorBtn = 'success';
                    }

                    let locationHtml = '';
                    if (isFirstRow) {
                        const location = JSON.parse(localStorage.getItem('location')) ?? [];
                        let options = '<option value="">-- Choose Location --</option>';
                        location.forEach((loc) => {
                            options +=
                                `<option value="${loc.id}" ${item.location === loc.id ? 'selected' : ''}>${loc.raw} | ${loc.area} | ${loc.rak} | ${loc.bin}</option>`;
                        });
                        locationHtml =
                            `<select class="form-control" onchange="changeLocation(${index}, this.value)">${options}</select>`;
                    }

                    html += `
                        <tr style="${isFirstRow ? 'border-top: 2px solid #ccc;' : ''}">
                            <td class="text-center fw-bold">${isFirstRow ? item.boxNumber : ''}</td>
                            <td>
                                ${subItem._type === 'parent' ? 
                                    '<span class="badge bg-danger-subtle text-danger">Parent</span>' : 
                                    '<span class="badge bg-secondary-subtle text-secondary">Child</span>'}
                            </td>
                            <td>${subItem.item}</td>
                            <td>${subItem.salesDoc}</td>
                            <td>${subItem.material}</td>
                            <td class="text-center fw-bold">${subItem.qtySelect}</td>
                            <td>
                                <a class="btn btn-${colorBtn} btn-sm" onclick="serialNumber('${subItem._type}', '${index}', '${subItem._idxDetail}', '${subItem.indexMaster}')">
                                    Serial Number
                                </a>
                            </td>
                            <td>${locationHtml}</td>
                            <td>${isFirstRow ? `<a class="btn btn-danger btn-sm" onclick="deleteBox(${index})">Delete Box</a>` : ''}</td>
                        </tr>
                    `;
                });
            });

            document.getElementById('listBox').innerHTML = html;
        }

        function changeLocation(index, value) {
            const box = JSON.parse(localStorage.getItem('box')) ?? [];

            box[index].location = parseInt(value);

            localStorage.setItem('box', JSON.stringify(box));
            viewListBox();
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

            setTimeout(() => {
                document.getElementById('scanSerialNumber').focus();
            }, 500);
        }

        function viewListSerialNumber() {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];
            let html = '';
            let number = 1;

            serialNumber.forEach((sn, index) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td><input type="text" class="form-control" value="${sn}" onchange="changeSN(${index}, this.value)"></td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteSN(${index})">Delete</a></td>
                    </tr>
                `;

                number++;
            });

            viewListBox();
            document.getElementById('listSnBox').innerHTML = html;
            document.getElementById('boxSerialNumber_qty_scan_sn').innerText = serialNumber.length;
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

            const findSN = serialNumber[indexDelete];
            const findMasterSN = master[indexMaster].serialNumber.find((item) => item.serialNumber === findSN);
            if (findMasterSN) {
                findMasterSN.select = 0;
            }

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

            let html = '';
            let number = 1;

            serialNumberAvailable.forEach((sn) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td>${sn.serialNumber}</td>
                        <td>
                            <a class="btn btn-info btn-sm" onclick="pilihSerialNumber('${sn.serialNumber}')">Pilih</a>
                        </td>
                    </tr>
                `;
                number++;
            });

            document.getElementById('listSerialNumberAvailable').innerHTML = html;
        }

        function pilihSerialNumber(valueSN) {
            const box = JSON.parse(localStorage.getItem('box')) ?? [];
            const master = JSON.parse(localStorage.getItem('master')) ?? [];
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            const type = document.getElementById('boxSerialNumber_type').value;
            const index = document.getElementById('boxSerialNumber_index').value;
            const indexDetail = document.getElementById('boxSerialNumber_indexDetail').value;
            const indexMaster = document.getElementById('boxSerialNumber_indexMaster').value;

            // Validation Apakah SN ada didalam list master dan belum dipilih
            // Cari yang valuenya sama DAN belum dipilih (select == 0)
            let check = master[indexMaster].serialNumber.find((item) => item.serialNumber === valueSN && parseInt(item
                .select) === 0);

            if (!check) {
                // Jika tidak ditemukan item available, cek apakah SN itu sebenarnya ada di master?
                const exists = master[indexMaster].serialNumber.find((item) => item.serialNumber === valueSN);

                if (exists) {
                    // Ada di master, berarti semuanya sudah terpilih (select=1)
                    document.getElementById('scanSerialNumberErrorMessage').innerText = "Serial Number has been added";
                    document.getElementById('scanSerialNumberError').style.display = "block";

                    setTimeout(() => {
                        document.getElementById('scanSerialNumberError').style.display = "none";
                    }, 3000);

                    const sound = new Audio("{{ asset('assets/sound/error.mp3') }}");
                    sound.play();

                    document.getElementById('scanSerialNumber').value = "";
                    document.getElementById('scanSerialNumber').focus();

                    return true;
                } else {
                    // Benar-benar tidak ada di master
                    document.getElementById('scanSerialNumberErrorMessage').innerText =
                        "Serial number is not in master data";
                    document.getElementById('scanSerialNumberError').style.display = "block";

                    setTimeout(() => {
                        document.getElementById('scanSerialNumberError').style.display = "none";
                    }, 3000);

                    const sound = new Audio("{{ asset('assets/sound/error.mp3') }}");
                    sound.play();

                    document.getElementById('scanSerialNumber').value = "";
                    document.getElementById('scanSerialNumber').focus();

                    return true;
                }
            }

            // Validation QTY
            if (type === 'parent') {
                if (serialNumber.length === parseInt(box[index].parent[indexDetail].qtySelect)) {
                    const sound = new Audio("{{ asset('assets/sound/error.mp3') }}");
                    sound.play();

                    Swal.fire({
                        title: 'Warning!',
                        text: 'Count serial numbers is the same as the product QTY',
                        icon: 'warning'
                    });
                    return true;
                }
            } else {
                if (serialNumber.length === parseInt(box[index].child[indexDetail].qtySelect)) {
                    const sound = new Audio("{{ asset('assets/sound/error.mp3') }}");
                    sound.play();

                    Swal.fire({
                        title: 'Warning!',
                        text: 'Count serial numbers is the same as the product QTY',
                        icon: 'warning'
                    });
                    return true;
                }
            }

            if (type === 'parent') {
                if (parseInt(box[index].parent[indexDetail].qtySelect + 1) > serialNumber.count) {
                    const sound = new Audio("{{ asset('assets/sound/error.mp3') }}");
                    sound.play();

                    Swal.fire({
                        title: 'Error!',
                        text: 'Count serial numbers exceeds qty',
                        icon: 'warning'
                    });

                    return true;
                }
            } else {
                if (parseInt(box[index].child[indexDetail].qtySelect + 1) > serialNumber.count) {
                    const sound = new Audio("{{ asset('assets/sound/error.mp3') }}");
                    sound.play();

                    Swal.fire({
                        title: 'Error!',
                        text: 'Count serial numbers exceeds qty',
                        icon: 'warning'
                    });

                    return true;
                }
            }

            serialNumber.push(valueSN);
            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));

            // Mark as selected in Master
            // Note: 'check' is a reference to the object inside the 'master' array structure? 
            // Dexie/LocalStorage might return copies.
            // Since we parsed 'master' from localStorage at the top, 'check' is an object in that array.
            // We just need to modify it and save 'master' back.
            // However, finding it again is safer if we want to be explicit.
            // Let's use the object found:
            check.select = 1;

            localStorage.setItem('master', JSON.stringify(master));

            // Save SN to BOX
            if (type === 'parent') {
                box[index].parent[indexDetail].serialNumber = serialNumber;
            } else {
                box[index].child[indexDetail].serialNumber = serialNumber;
            }
            localStorage.setItem('box', JSON.stringify(box));

            viewListSerialNumber();
            viewSelectSnAvailable(indexMaster);

            const sound = new Audio("{{ asset('assets/sound/scan.mp3') }}");
            sound.play();
        }

        function tambahManualSerialNumber() {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];
            const box = JSON.parse(localStorage.getItem('box')) ?? [];

            const type = document.getElementById('boxSerialNumber_type').value;
            const index = document.getElementById('boxSerialNumber_index').value;
            const indexDetail = document.getElementById('boxSerialNumber_indexDetail').value;
            const indexMaster = document.getElementById('boxSerialNumber_indexMaster').value;

            // Validation QTY
            if (type === 'parent') {
                if (serialNumber.length === parseInt(box[index].parent[indexDetail].qtySelect)) {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Jumlah serial number sudah sama dengan QTY product',
                        icon: 'warning'
                    });
                    return true;
                }
            } else {
                if (serialNumber.length === parseInt(box[index].child[indexDetail].qtySelect)) {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Jumlah serial number sudah sama dengan QTY product',
                        icon: 'warning'
                    });
                    return true;
                }
            }

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

            // Kembalikan QTY Master & SN
            const targetBox = box[index];
            const allItemsInBox = [...(targetBox.parent || []), ...(targetBox.child || [])];

            allItemsInBox.forEach((itemInBox) => {
                const masterIdx = itemInBox.indexMaster; // Gunakan indexMaster untuk mapping yang tepat
                if (master[masterIdx]) {
                    master[masterIdx].qtyPa = parseInt(master[masterIdx].qtyPa) - parseInt(itemInBox.qtySelect);

                    (itemInBox.serialNumber || []).forEach((sn) => {
                        const findSnMaster = master[masterIdx].serialNumber.find(i => i.serialNumber ===
                            sn);
                        if (findSnMaster) {
                            findSnMaster.select = 0;
                        }
                    });
                }
            });

            box.splice(index, 1);

            // Re-indexing Box Number dan pertahankan location
            const dataBox = box.map((item, idx) => ({
                ...item,
                boxNumber: idx + 1
            }));

            localStorage.setItem('box', JSON.stringify(dataBox));
            localStorage.setItem('master', JSON.stringify(master));
            viewListBox();
        }

        document.getElementById('scanSerialNumber').addEventListener('keydown', function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                const value = this.value.trim();
                if (value !== "") {
                    pilihSerialNumber(value);

                    document.getElementById('scanSerialNumber').value = "";
                    document.getElementById('scanSerialNumber').focus();
                } else {
                    document.getElementById('scanSerialNumberErrorMessage').innerText =
                        "Serial number cannot be empty";
                    document.getElementById('scanSerialNumberError').style.display = "block";

                    setTimeout(() => {
                        document.getElementById('scanSerialNumberError').style.display = "none";
                    }, 3000);

                    const sound = new Audio("{{ asset('assets/sound/error.mp3') }}");
                    sound.play();

                    document.getElementById('scanSerialNumber').value = "";
                    document.getElementById('scanSerialNumber').focus();
                }
            }
        });

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
                    // Validation Products
                    const master = JSON.parse(localStorage.getItem('master')) ?? [];
                    // Removed validation for checking if all QTY is entered into box to allow Partial Put Away
                    // master.forEach((item) => {
                    //     if (parseInt(item.qty) !== parseInt(item.qtyPa)) {
                    //         Swal.fire({
                    //             title: 'Warning!',
                    //             text: 'Not all QTY have been entered into the box',
                    //             icon: 'warning'
                    //         });
                    //         return true;
                    //     }
                    // });

                    // Validation Serial Number N/A
                    const box = JSON.parse(localStorage.getItem('box')) ?? [];

                    const updateSerialNumbers = (items) => {
                        items.forEach(item => {
                            item.forEach(subItem => {
                                const qtySelect = Number(subItem.qtySelect);
                                const serialNumberLength = subItem.serialNumber.length;

                                if (qtySelect !== serialNumberLength) {
                                    const missingQty = qtySelect - serialNumberLength;
                                    subItem.serialNumber.push(...Array(missingQty).fill('N/A'));
                                }

                                subItem.serialNumber = subItem.serialNumber.map(sn => (sn ===
                                    null || sn === '') ? 'N/A' : sn);
                            });
                        });
                    };

                    box.forEach(item => {
                        updateSerialNumbers([item.parent, item.child]);

                        if (item.location === null || item.location === '') {
                            Swal.fire({
                                title: 'Warning!',
                                text: 'Location cannot be empty',
                                icon: 'warning'
                            });
                            return true;
                        }
                    });

                    // Insert ke Database
                    $.ajax({
                        url: '{{ route('inbound.put-away.store') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            box: box,
                            productPackageId: '{{ request()->get('id') }}'
                        },
                        success: (res) => {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Success',
                                    text: 'Put Away Product Success',
                                    icon: 'success'
                                }).then((e) => {
                                    window.location.href = '/inbound/put-away/detail?id=' + res
                                        .data;
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

        function changeMassQty(value) {
            const addBox = JSON.parse(localStorage.getItem('addBox')) ?? [];
            addBox.forEach((item) => {
                item.qtySelect = value;
            });
            localStorage.setItem('addBox', JSON.stringify(addBox));
            viewMasterProductModal();
        }

        function selectAllSN() {
            const box = JSON.parse(localStorage.getItem('box')) ?? [];
            const master = JSON.parse(localStorage.getItem('master')) ?? [];
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            const type = document.getElementById('boxSerialNumber_type').value;
            const index = document.getElementById('boxSerialNumber_index').value;
            const indexDetail = document.getElementById('boxSerialNumber_indexDetail').value;
            const indexMaster = document.getElementById('boxSerialNumber_indexMaster').value;

            // Determine Target Quantity
            let targetQty = 0;
            if (type === 'parent') {
                targetQty = parseInt(box[index].parent[indexDetail].qtySelect);
            } else {
                targetQty = parseInt(box[index].child[indexDetail].qtySelect);
            }

            // Calculate needed amount
            const neededParams = targetQty - serialNumber.length;

            if (neededParams <= 0) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Quantity is already full',
                    icon: 'warning'
                });
                return;
            }

            // Get Available SNs from Master
            const masterSNList = master[indexMaster].serialNumber;
            // Find SNs that are NOT selected (status == 0)
            const availableSNs = masterSNList.filter(sn => parseInt(sn.select) === 0);

            if (availableSNs.length === 0) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'No more available serial numbers',
                    icon: 'warning'
                });
                return;
            }

            // Take as many as needed or as many as available
            const toAdd = availableSNs.slice(0, neededParams);
            const addedCount = toAdd.length;

            toAdd.forEach(snObj => {
                // Add to Box SN list
                serialNumber.push(snObj.serialNumber);

                // Mark as selected in Master
                // We need to find the specific object in the master array to update it
                // Since filter returned references (or we iterate master again to be safe)
                // Let's iterate master directly to find and update reference
                const snInMaster = master[indexMaster].serialNumber.find(mSn => mSn ===
                    snObj); // Check reference or content
                if (snInMaster) snInMaster.select = 1;
            });

            // Save Updates
            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            localStorage.setItem('master', JSON.stringify(master));

            if (type === 'parent') {
                box[index].parent[indexDetail].serialNumber = serialNumber;
            } else {
                box[index].child[indexDetail].serialNumber = serialNumber;
            }
            localStorage.setItem('box', JSON.stringify(box));

            // Refresh Views
            viewListSerialNumber();
            viewSelectSnAvailable(indexMaster);

            const sound = new Audio("{{ asset('assets/sound/scan.mp3') }}");
            sound.play();

            Swal.fire({
                title: 'Success!',
                text: `${addedCount} Serial Numbers added automatically.`,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }
    </script>

    <script>
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
                    let html = '<option value="">-- Select Raw --</option>';

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
