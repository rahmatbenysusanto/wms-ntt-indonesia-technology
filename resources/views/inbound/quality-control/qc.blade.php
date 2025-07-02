@extends('layout.index')
@section('title', 'Quality Control')
@section('sizeBarSize', 'sm')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Process Quality Control</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item">Quality Control</li>
                        <li class="breadcrumb-item active">Process</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Purchase Order Detail</h4>
                </div>
                <div class="card-body">
                    <table>
                        <tr>
                            <td class="fw-bold">Purc Doc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $purchaseOrder->purc_doc }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Sales Doc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ request()->get('sales-doc')  }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">List Item</h4>
                                <a class="btn btn-info btn-sm" onclick="pilihSemua()">Pilih Semua</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped align-middle" id="masterMaterial">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-center">Item</th>
                                        <th>Material</th>
                                        <th>Desc</th>
                                        <th>Hierarchy Desc</th>
                                        <th class="text-center">QTY</th>
                                        <th class="text-center">QTY QC</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="listItemMaster">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">Mapping Parent & Child Item</h4>
                                <a class="btn btn-primary btn-sm" onclick="addMappingItem()">Add Mapping Item</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-center">Item</th>
                                        <th>Material</th>
                                        <th style="width: 100px">QTY</th>
                                        <th>Parent</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="listMapping">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Quality Control Items</h4>
                        <a class="btn btn-primary" onclick="processQC()">Process Quality Control</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Material</th>
                                    <th class="text-center">Parent</th>
                                    <th>Item</th>
                                    <th>Desc</th>
                                    <th>Hierarchy Desc</th>
                                    <th class="text-center">QTY</th>
                                    <th>Serial Number</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="listQualityControl">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Default Modals -->
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
                            </tr>
                        </thead>
                        <tbody id="listSerialNumberUpload">

                        </tbody>
                    </table>

                    <input type="hidden" id="SN_type">
                    <input type="hidden" id="SN_index">
                    <input type="hidden" id="SN_index_detail">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        localStorage.clear();
        loadMasterItem();
        function loadMasterItem() {
            const item = @json($products);
            localStorage.setItem('master', JSON.stringify(item));
            viewListMaster();
        }

        function viewListMaster() {
            const products = JSON.parse(localStorage.getItem('master')) ?? [];
            let html = '';
            let number = 1;

            // Simpan halaman aktif sebelum destroy
            let currentPage = 0;
            if ($.fn.DataTable.isDataTable('#masterMaterial')) {
                currentPage = $('#masterMaterial').DataTable().page();
                $('#masterMaterial').DataTable().destroy();
            }

            products.forEach((item, index) => {
                let button = '';
                if (parseInt(item.qty) !== parseInt(item.qty_qc)) {
                    button = `<a class="btn btn-info btn-sm" onclick="addToMapping(${index})">Pilih</a>`;
                }

                html += `
                    <tr>
                        <td>${number}</td>
                        <td class="text-center">${item.item}</td>
                        <td>${item.sku}</td>
                        <td>${item.name}</td>
                        <td>${item.type}</td>
                        <td class="text-center">${item.qty}</td>
                        <td class="text-center">${item.qty_qc}</td>
                        <td>
                            <div class="d-flex gap-2">
                                ${button}
                            </div>
                        </td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listItemMaster').innerHTML = html;

            // Re-init DataTable dan kembali ke halaman sebelumnya
            const table = new DataTable('#masterMaterial');
            table.page(currentPage).draw('page');
        }

        function pilihSemua() {
            const products = JSON.parse(localStorage.getItem('master')) ?? [];
            let mapping = JSON.parse(localStorage.getItem('mapping')) ?? [];

            products.forEach((item, index) => {
                const product = products[index];

                product.parent = 0;
                product.index = index;

                const existingIndex = mapping.findIndex(m => m.id === product.id);

                if (existingIndex !== -1) {
                    // Jika sudah ada, tambahkan qty
                    mapping[existingIndex].qty = Number(mapping[existingIndex].qty) + 1;
                    products[index].qty_qc = Number(products[index].qty_qc + 1);
                } else {
                    // Jika belum ada, tambahkan produk baru ke mapping
                    mapping.push({
                        id: product.id,
                        index: product.index,
                        item: product.item,
                        name: product.name,
                        parent: product.parent,
                        qty: product.qty - product.qty_qc,
                        sku: product.sku,
                        type: product.type,
                    });
                    products[index].qty_qc += product.qty - product.qty_qc;
                }
            });

            localStorage.setItem('master', JSON.stringify(products));
            localStorage.setItem('mapping', JSON.stringify(mapping));

            viewListMaster();
            viewMappingList();
        }

        function addToMapping(index) {
            const products = JSON.parse(localStorage.getItem('master')) ?? [];
            const product = products[index];

            let mapping = JSON.parse(localStorage.getItem('mapping')) ?? [];

            product.parent = 0;
            product.index = index;

            const existingIndex = mapping.findIndex(m => m.id === product.id);

            if (existingIndex !== -1) {
                // Jika sudah ada, tambahkan qty
                mapping[existingIndex].qty = Number(mapping[existingIndex].qty) + 1;
                products[index].qty_qc = Number(products[index].qty_qc + 1);
            } else {
                // Jika belum ada, tambahkan produk baru ke mapping
                mapping.push({
                    id: product.id,
                    index: product.index,
                    item: product.item,
                    name: product.name,
                    parent: product.parent,
                    qty: product.qty - product.qty_qc,
                    sku: product.sku,
                    type: product.type,
                    sn: null
                });
                products[index].qty_qc += product.qty - product.qty_qc;
            }

            localStorage.setItem('master', JSON.stringify(products));
            localStorage.setItem('mapping', JSON.stringify(mapping));

            viewListMaster();
            viewMappingList();
        }

        function viewMappingList() {
            const mapping = JSON.parse(localStorage.getItem('mapping')) ?? [];
            let html = '';
            let number = 1;

            mapping.forEach((item, index) => {
                html += `
                     <tr>
                        <td>${number}</td>
                        <td class="text-center">${item.item}</td>
                        <td>
                            <p class="mb-1">${item.sku}</p>
                            <p class="mb-1">${item.name}</p>
                            <p>${item.type}</p>
                        </td>
                        <td><input type="number" class="form-control" value="${item.qty}" onchange="changeQtyMapping(${item.index}, ${index}, this.value)"></td>
                        <td>
                            <div class="form-check form-switch form-switch-md">
                                <input class="form-check-input" type="checkbox" role="switch" id="parent_${index}" value="${item.parent}" ${item.parent === 1 ? 'checked' : ''} onchange="changeParent(${index}, this.checked)">
                                <label class="form-check-label" for="parent_${index}">Parent Material</label>
                            </div>
                        </td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteMappingItem(${item.index}, ${index})">Delete</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listMapping').innerHTML = html;
        }

        function changeQtyMapping(itemIndex, index, value) {
            // Change Data Mapping
            const mapping = JSON.parse(localStorage.getItem('mapping')) ?? [];
            mapping[index].qty = value;
            localStorage.setItem('mapping', JSON.stringify(mapping));
            viewMappingList();

            // Calculate QTY QC Master Item
            const products = JSON.parse(localStorage.getItem('master')) ?? [];
            const listItemMapping = mapping.filter(item => parseInt(item.index) === itemIndex);
            let qty = 0;
            listItemMapping.forEach((item) => {
                qty += parseInt(item.qty);
            });
            products[itemIndex].qty_qc = qty;
            localStorage.setItem('master', JSON.stringify(products));
            viewListMaster();
        }

        function changeParent(index, isChecked) {
            const mapping = JSON.parse(localStorage.getItem('mapping')) ?? [];

            if (isChecked) {
                // Cek apakah sudah ada parent lain
                const existingParentIndex = mapping.findIndex(item => item.parent === 1);

                if (existingParentIndex !== -1 && existingParentIndex !== index) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Parent hanya boleh 1',
                        icon: 'warning'
                    });

                    const checkbox = document.getElementById(`parent_${index}`);
                    if (checkbox) checkbox.checked = false;

                    return;
                }

                mapping[index].parent = 1;
            } else {
                mapping[index].parent = 0;
            }

            localStorage.setItem('mapping', JSON.stringify(mapping));
            viewMappingList();
        }

        function deleteMappingItem(index, detailIndex) {
            const mapping = JSON.parse(localStorage.getItem('mapping')) ?? [];
            const products = JSON.parse(localStorage.getItem('master')) ?? [];

            mapping.splice(detailIndex, 1);
            products[index].qty_qc = 0;

            localStorage.setItem('mapping', JSON.stringify(mapping));
            localStorage.setItem('master', JSON.stringify(products));
            viewMappingList();
            viewListMaster();
        }

        function addMappingItem() {
            const mapping = JSON.parse(localStorage.getItem('mapping')) ?? [];
            const qualityControl = JSON.parse(localStorage.getItem('qc')) ?? [];
            const checkParent = mapping.find(item => item.parent === 1);
            if (!checkParent) {
                Swal.fire({
                    title: 'Warning',
                    text: 'Harus ada 1 parent disetiap produk',
                    icon: 'warning'
                });
                return true;
            }

            qualityControl.push({
                id: checkParent.id,
                item: checkParent.item,
                sku: checkParent.sku,
                name: checkParent.name,
                type: checkParent.type,
                qty: checkParent.qty,
                sn: null,
                child: mapping.filter(item => item.parent === 0)
            });

            localStorage.setItem('qc', JSON.stringify(qualityControl));
            viewListQC();
            localStorage.setItem('mapping', JSON.stringify([]));
            viewMappingList();
        }

        function viewListQC() {
            const qualityControl = JSON.parse(localStorage.getItem('qc')) ?? [];
            let html = '';
            let number = 1;

            qualityControl.forEach((item, index) => {
                let htmlSN = '';
                if (item.sn === null) {
                    htmlSN = `<a class="btn btn-info btn-sm" onclick="uploadSN('parent', ${index}, null)">Upload SN</a>`;
                } else {
                    htmlSN = `<a class="btn btn-success btn-sm" onclick="detailSN('parent', ${index}, null)">Detail SN</a>`;
                }

                html += `
                    <tr>
                        <td>${number}</td>
                        <td>${item.sku}</td>
                        <td class="text-center"><span class="badge bg-success-subtle text-success">Parent</span></td>
                        <td>${item.item}</td>
                        <td>${item.name}</td>
                        <td>${item.type}</td>
                        <td class="text-center fw-bold">${item.qty}</td>
                        <td>${htmlSN}</td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteQC(${index})">Delete</a></td>
                    </tr>
                `;

                (item.child).forEach((child, indexDetail) => {
                    let htmlSN = '';
                    if (item.sn === null) {
                        htmlSN = `<a class="btn btn-info btn-sm" onclick="uploadSN('child', ${index}, ${indexDetail})">Upload SN</a>`;
                    } else {
                        htmlSN = `<a class="btn btn-success btn-sm" onclick="detailSN('child', ${index}, ${indexDetail})">Detail SN</a>`;
                    }

                    html += `
                        <tr>
                            <td></td>
                            <td>${child.sku}</td>
                            <td class="text-center"></td>
                            <td>${child.item}</td>
                            <td>${child.name}</td>
                            <td>${child.type}</td>
                            <td class="text-center fw-bold">${child.qty}</td>
                            <td>${htmlSN}</td>
                            <td></td>
                        </tr>
                    `;
                });

                number++;
            });

            document.getElementById('listQualityControl').innerHTML = html;
        }

        function uploadSN(type, index, indexDetail) {
            document.getElementById('SN_type').value = type;
            document.getElementById('SN_index').value = index;
            document.getElementById('SN_index_detail').value = indexDetail;

            const qc = JSON.parse(localStorage.getItem('qc')) ?? [];
            if (type === 'parent') {
                document.getElementById('SN_item').innerText = qc[index].item;
                document.getElementById('SN_material').innerText = qc[index].sku;
                document.getElementById('SN_desc').innerText = qc[index].name;
                document.getElementById('SN_hie').innerText = qc[index].type;
            } else {
                document.getElementById('SN_item').innerText = qc[index].child[indexDetail].item;
                document.getElementById('SN_material').innerText = qc[index].child[indexDetail].sku;
                document.getElementById('SN_desc').innerText = qc[index].child[indexDetail].name;
                document.getElementById('SN_hie').innerText = qc[index].child[indexDetail].type;
            }

            viewSerialNumber();
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
            const qc = JSON.parse(localStorage.getItem('qc')) ?? [];

            const index = document.getElementById('SN_index').value;
            const indexDetail = document.getElementById('SN_index_detail').value;

            if (document.getElementById('SN_type').value === 'parent') {
                if (parseInt(qc[index].qty) !== serialNumber.length) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Jumlah serial number harus sama dengan qty product',
                        icon: 'warning'
                    });

                    return true;
                }

                qc[index].sn = serialNumber;
            } else {
                if (parseInt(qc[index].child[indexDetail].qty) !== serialNumber.length) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Jumlah serial number harus sama dengan qty product',
                        icon: 'warning'
                    });

                    return true;
                }

                qc[index].child[indexDetail].sn = serialNumber;
            }

            localStorage.setItem('qc', JSON.stringify(qc));
            localStorage.setItem('serialNumber', JSON.stringify([]));

            $('#uploadSerialNumberModal').modal('hide');
            viewListQC();
        }

        function detailSN(type, index, indexDetail) {
            const qc = JSON.parse(localStorage.getItem('qc')) ?? [];
            if (type === 'parent') {
                document.getElementById('detail_SN_item').innerText = qc[index].item;
                document.getElementById('detail_SN_material').innerText = qc[index].sku;
                document.getElementById('detail_SN_desc').innerText = qc[index].name;
                document.getElementById('detail_SN_hie').innerText = qc[index].type;

                const serialNumber = qc[index].sn;
            } else {
                document.getElementById('detail_SN_item').innerText = qc[index].child[indexDetail].item;
                document.getElementById('detail_SN_material').innerText = qc[index].child[indexDetail].sku;
                document.getElementById('detail_SN_desc').innerText = qc[index].child[indexDetail].name;
                document.getElementById('detail_SN_hie').innerText = qc[index].child[indexDetail].type;

                const serialNumber = qc[index].child[indexDetail].sn;
            }

            let html = '';
            let number = 1;

            serialNumber.forEach((sn) => {
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

        function deleteQC(index) {
            const qualityControl = JSON.parse(localStorage.getItem('qc')) ?? [];
            const products = JSON.parse(localStorage.getItem('master')) ?? [];
            const data = qualityControl[index];

            const product = products.find((item => item.id === data.id));
            product.qty = data.qty;
            product.qty_qc = 0;

            (data.child).forEach((child) => {
                const product = products.find((item => item.id === child.id));
                product.qty = child.qty;
                product.qty_qc = 0;
            });

            qualityControl.splice(index, 1);
            localStorage.setItem('master', JSON.stringify(products));
            localStorage.setItem('qc', JSON.stringify(qualityControl));
            viewListMaster();
            viewListQC();
        }

        function processQC() {
            Swal.fire({
                title: "Are you sure?",
                text: "Process Quality Control",
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

                    $.ajax({
                        url: '{{ route('inbound.quality-control-store-process') }}',
                        method: 'POST',
                        data:{
                            _token: '{{ csrf_token() }}',
                            qualityControl: JSON.parse(localStorage.getItem('qc')) ?? [],
                            purchaseOrderId: '{{ request()->get('po') }}',
                            salesDoc: '{{ request()->get('sales-doc') }}'
                        },
                        success: (res) => {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Quality Control successfully!',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: "btn btn-primary w-xs mt-2"
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    window.location.href = '{{ route('inbound.quality-control') }}';
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Quality Control Failed!',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: "btn btn-primary w-xs mt-2"
                                    },
                                    buttonsStyling: false
                                });
                            }
                        }
                    });
                }
            });
        }

        function tambahSerialNumberManual() {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            serialNumber.push({
                serialNumber: ''
            });

            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            viewSerialNumber();
        }
    </script>
@endsection



















































