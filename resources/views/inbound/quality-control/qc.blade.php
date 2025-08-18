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
                                    <th>Sales Doc</th>
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
                                <th>PA Step</th>
                                <th>Sales Doc</th>
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
                            <tr>
                                <td class="fw-bold">QTY</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="detail_SN_qty"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Sales Doc</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="detail_SN_sales_doc"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">QTY Serial Number Scan</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1 fw-bold" id="detail_SN_qty_serial_number_scan"></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Inject Data -->
                    <input type="hidden" id="detail_SN_index">
                    <input type="hidden" id="detail_SN_productIndex">

                    <div class="mb-3">
                        <input type="text" class="form-control" id="scanSerialNumber" placeholder="Scan Serial Number" autofocus>
                    </div>

                    <div id="scanSerialNumberError" class="alert alert-danger alert-dismissible alert-label-icon label-arrow shadow fade show" role="alert" style="display: none">
                        <i class="ri-error-warning-line label-icon"></i>
                        <strong>Error</strong> - <span id="scanSerialNumberErrorMessage"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                    <a class="btn btn-secondary w-100" onclick="addSerialNumberManual()">SN Manual</a>
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
                        <tbody id="listDetailSerialNumber">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

    <div id="serialNumberDirectOutboundModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
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
                                <td class="ps-1" id="detail_Direct_item"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Material</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="detail_Direct_material"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Desc</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="detail_Direct_desc"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Hierarchy</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="detail_Direct_hie"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">QTY</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="detail_Direct_qty"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Sales Doc</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="detail_Direct_sales_doc"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">QTY Serial Number Scan</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1 fw-bold" id="detail_SN_qty_serial_number_direct_scan"></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Inject Data -->
                    <input type="hidden" id="detail_Direct_index">
                    <input type="hidden" id="detail_Direct_productIndex">

                    <div class="mb-3">
                        <input type="text" class="form-control" id="scanSerialNumberDirect" placeholder="Scan Serial Number" autofocus>
                    </div>

                    <div id="scanSerialNumberDirectError" class="alert alert-danger alert-dismissible alert-label-icon label-arrow shadow fade show" role="alert" style="display: none">
                        <i class="ri-error-warning-line label-icon"></i>
                        <strong>Error</strong> - <span id="scanSerialNumberDirectErrorMessage"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

{{--                    <div class="mb-3">--}}
{{--                        <div class="row">--}}
{{--                            <div class="col-2">--}}
{{--                                <label class="form-label text-white">-</label>--}}
{{--                                <div>--}}
{{--                                    <a class="btn btn-secondary w-100" onclick="addSerialNumberManualDirect()">SN Manual</a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <table class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Serial Number</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="listDetailSerialNumberDirect">

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
        let serialNumberIndex = null;
        let serialNumberIndexProduct = null;

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
                        <td>${item.sales_doc}</td>
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
                        salesDoc: product.sales_doc
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
                    salesDoc: product.sales_doc,
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
                            <p class="mb-1">${item.type}</p>
                            <p class="mb-1"><b>Sales Doc:</b> ${item.salesDoc}</p>
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

                // if (existingParentIndex !== -1 && existingParentIndex !== index) {
                //     Swal.fire({
                //         title: 'Warning',
                //         text: 'Parent hanya boleh 1',
                //         icon: 'warning'
                //     });
                //
                //     const checkbox = document.getElementById(`parent_${index}`);
                //     if (checkbox) checkbox.checked = false;
                //
                //     return;
                // }

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

            const qc = [];
            mapping.forEach((item) => {
                qc.push({
                    id: item.id,
                    item: item.item,
                    sku: item.sku,
                    name: item.name,
                    type: item.type,
                    qty: item.qty,
                    salesDoc: item.salesDoc,
                    serialNumber: [],
                    putAwayStep: 1,
                    parent: item.parent,
                    qtyDirect: 0,
                    SnDirect: []
                });
            });

            qualityControl.push(qc);

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
                item.forEach((product, indexProduct) => {
                    html += `
                        <tr>
                            <td>${indexProduct === 0 ? `${number}` : ''}</td>
                            <td>${product.sku}</td>
                            <td class="text-center">${product.parent === 1 ? '<span class="badge bg-danger-subtle text-danger">Parent</span>' : '<span class="badge bg-secondary-subtle text-secondary">Child</span>'}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <div class="form-check form-switch form-switch-md">
                                      <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        ${parseInt(product.putAwayStep) === 1 ? 'checked' : ''}
                                        onchange="handlePutAwayStepChange(this, ${index}, ${indexProduct})"
                                      >
                                    </div>
                                    ${product.putAwayStep === 0 ? `<a class="btn btn-secondary btn-sm" onclick="directOutboundSerialNumber(${index}, ${indexProduct})">Pilih SN</a>` : ''}
                                </div>
                            </td>
                            <td>${product.salesDoc}</td>
                            <td>${product.item}</td>
                            <td>${product.name}</td>
                            <td>${product.type}</td>
                            <td class="text-center fw-bold">${product.qty}</td>
                            <td><a class="btn ${(parseInt(product.serialNumber.length) + parseInt(product.SnDirect.length ?? [])) === parseInt(product.qty) ? 'btn-success' : 'btn-info'} btn-sm" onclick="serialNumber(${index}, ${indexProduct})">Serial Number</a></td>
                            <td>${indexProduct === 0 ? `<a class="btn btn-danger btn-sm" onclick="deleteQC(${index})">Delete</a>` : ''}</td>
                        </tr>
                    `;
                });

                number++;
            });

            document.getElementById('listQualityControl').innerHTML = html;
        }

        function directOutboundSerialNumber(index, indexProduct) {
            const qc = JSON.parse(localStorage.getItem('qc')) ?? [];
            const product = qc[index][indexProduct];

            document.getElementById('detail_Direct_item').innerText = product.item;
            document.getElementById('detail_Direct_material').innerText = product.sku;
            document.getElementById('detail_Direct_desc').innerText = product.name;
            document.getElementById('detail_Direct_hie').innerText = product.type;
            document.getElementById('detail_Direct_qty').innerText = product.qty - product.qtyDirect;
            document.getElementById('detail_Direct_sales_doc').innerText = product.salesDoc;
            document.getElementById('detail_Direct_index').value = index;
            document.getElementById('detail_Direct_productIndex').value = indexProduct;

            serialNumberIndex = index;
            serialNumberIndexProduct = indexProduct;

            // List SN
            const serialNumber = product.SnDirect;
            let html = '';
            let number = 1;
            serialNumber.forEach((item, indexSN) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td><input type="text" class="form-control" value="${item}" onchange="changeSNDirect(${index}, ${indexProduct}, ${indexSN}, this.value)"></td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteSNDirect(${index}, ${indexProduct}, ${indexSN})">Delete</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listDetailSerialNumberDirect').innerHTML = html;
            $('#serialNumberDirectOutboundModal').modal('show');

            setTimeout(() => {
                document.getElementById('scanSerialNumberDirect').focus();
            }, 500);
        }

        function deleteSNDirect(index, indexProduct, indexSN) {
            const qc = JSON.parse(localStorage.getItem('qc')) ?? [];
            const product = qc[index][indexProduct];
            const serialNumber = product.SnDirect;

            serialNumber.splice(indexSN, 1);
            product.qtyDirect = serialNumber.length;

            localStorage.setItem('qc', JSON.stringify(qc));
            viewSerialNumberDirect();
        }

        function changeSNDirect(index, indexProduct, indexSN, value) {
            const qc = JSON.parse(localStorage.getItem('qc')) ?? [];
            const product = qc[index][indexProduct];
            const serialNumber = product.SnDirect;

            serialNumber[indexSN] = value;
            product.qtyDirect = serialNumber.length;

            localStorage.setItem('qc', JSON.stringify(qc));
            viewSerialNumberDirect();
        }

        function viewSerialNumberDirect() {
            const index = document.getElementById('detail_Direct_index').value;
            const indexProduct = document.getElementById('detail_Direct_productIndex').value;

            const qc = JSON.parse(localStorage.getItem('qc')) ?? [];
            const product = qc[index][indexProduct];
            const serialNumber = product.SnDirect;
            let html = '';
            let number = 1;
            serialNumber.forEach((item, indexSN) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td><input type="text" class="form-control" value="${item}" onchange="changeSNDirect(${index}, ${indexProduct}, ${indexSN}, this.value)"></td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteSNDirect(${index}, ${indexProduct}, ${indexSN})">Delete</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('detail_SN_qty_serial_number_direct_scan').innerText = serialNumber.length;
            document.getElementById('listDetailSerialNumberDirect').innerHTML = html;
        }

        function addSerialNumberManualDirect() {
            const index = document.getElementById('detail_Direct_index').value;
            const indexProduct = document.getElementById('detail_Direct_productIndex').value;
            const qc = JSON.parse(localStorage.getItem('qc')) ?? [];
            const product = qc[index][indexProduct].SnDirect;

            product.push("");

            localStorage.setItem('qc', JSON.stringify(qc));
            viewSerialNumberDirect();
        }

        function serialNumber(index, indexProduct) {
            const qc = JSON.parse(localStorage.getItem('qc')) ?? [];
            const serialNumber = qc[index][indexProduct].serialNumber;

            document.getElementById('detail_SN_item').innerText = qc[index][indexProduct].item;
            document.getElementById('detail_SN_material').innerText = qc[index][indexProduct].sku;
            document.getElementById('detail_SN_desc').innerText = qc[index][indexProduct].name;
            document.getElementById('detail_SN_hie').innerText = qc[index][indexProduct].type;
            document.getElementById('detail_SN_qty').innerText = qc[index][indexProduct].qty;
            document.getElementById('detail_SN_sales_doc').innerText = qc[index][indexProduct].salesDoc;
            document.getElementById('detail_SN_index').value = index;
            document.getElementById('detail_SN_productIndex').value = indexProduct;

            serialNumberIndex = index;
            serialNumberIndexProduct = indexProduct;
            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            viewSerialNumber();

            $('#detailSerialNumberModal').modal('show');

            setTimeout(() => {
                document.getElementById('scanSerialNumber').focus();
            }, 500);
        }

        function handlePutAwayStepChange(checkbox, index, indexProduct) {
            const qualityControl = JSON.parse(localStorage.getItem('qc')) ?? [];

            qualityControl[index][indexProduct].putAwayStep = checkbox.checked ? 1 : 0;

            localStorage.setItem('qc', JSON.stringify(qualityControl));
            viewListQC();
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
                document.getElementById('SN_qty').innerText = qc[index].qty;
            } else if (type === 'parent multi') {
                document.getElementById('SN_item').innerText = qc[index].parent[indexDetail].item;
                document.getElementById('SN_material').innerText = qc[index].parent[indexDetail].sku;
                document.getElementById('SN_desc').innerText = qc[index].parent[indexDetail].name;
                document.getElementById('SN_hie').innerText = qc[index].parent[indexDetail].type;
                document.getElementById('SN_qty').innerText = qc[index].parent[indexDetail].qty;
            } else {
                document.getElementById('SN_item').innerText = qc[index].child[indexDetail].item;
                document.getElementById('SN_material').innerText = qc[index].child[indexDetail].sku;
                document.getElementById('SN_desc').innerText = qc[index].child[indexDetail].name;
                document.getElementById('SN_hie').innerText = qc[index].child[indexDetail].type;
                document.getElementById('SN_qty').innerText = qc[index].child[indexDetail].qty;
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
            const qc = JSON.parse(localStorage.getItem('qc')) ?? [];
            const index = document.getElementById('detail_SN_index').value;
            const indexProduct = document.getElementById('detail_SN_productIndex').value;

            qc[index][indexProduct].serialNumber = serialNumber;
            localStorage.setItem('qc', JSON.stringify(qc));

            let html = '';
            let number = 1;

            serialNumber.forEach((sn, index) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td><input type="text" class="form-control" value="${sn}" onchange="changeSerialNumber(${index}, this.value)"></td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteSerialNumber(${index})">Delete</a></td>
                    </tr>
                `;
                number++;
            });

            document.getElementById('detail_SN_qty_serial_number_scan').innerText = serialNumber.length;
            document.getElementById('listDetailSerialNumber').innerHTML = html;
            viewListQC();
        }

        function changeSerialNumber(index, value) {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            serialNumber[index] = value;

            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            viewSerialNumber();
        }

        function deleteSerialNumber(index) {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            serialNumber.splice(index, 1);

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
            } else if (document.getElementById('SN_type').value === 'parent multi') {
                if (parseInt(qc[index].parent[indexDetail].qty) !== serialNumber.length) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Jumlah serial number harus sama dengan qty product',
                        icon: 'warning'
                    });

                    return true;
                }

                qc[index].parent[indexDetail].sn = serialNumber;
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
            let serialNumber = [];

            if (type === 'parent') {
                document.getElementById('detail_SN_item').innerText = qc[index].item;
                document.getElementById('detail_SN_material').innerText = qc[index].sku;
                document.getElementById('detail_SN_desc').innerText = qc[index].name;
                document.getElementById('detail_SN_hie').innerText = qc[index].type;
                document.getElementById('detail_SN_qty').innerText = qc[index].qty;

                serialNumber = qc[index].sn;
            } else if (type === 'parent multi') {
                document.getElementById('detail_SN_item').innerText = qc[index].parent[indexDetail].item;
                document.getElementById('detail_SN_material').innerText = qc[index].parent[indexDetail].sku;
                document.getElementById('detail_SN_desc').innerText = qc[index].parent[indexDetail].name;
                document.getElementById('detail_SN_hie').innerText = qc[index].parent[indexDetail].type;
                document.getElementById('detail_SN_qty').innerText = qc[index].parent[indexDetail].qty;

                serialNumber = qc[index].parent[indexDetail].sn;
            } else {
                document.getElementById('detail_SN_item').innerText = qc[index].child[indexDetail].item;
                document.getElementById('detail_SN_material').innerText = qc[index].child[indexDetail].sku;
                document.getElementById('detail_SN_desc').innerText = qc[index].child[indexDetail].name;
                document.getElementById('detail_SN_hie').innerText = qc[index].child[indexDetail].type;
                document.getElementById('detail_SN_qty').innerText = qc[index].child[indexDetail].qty;

                serialNumber = qc[index].child[indexDetail].sn;
            }

            let html = '';
            let number = 1;

            serialNumber.forEach((sn, indexSN) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td>
                            <input type="text" class="form-control" value="${sn.serialNumber}"
                                onchange="changeSerialNumberDetail('${type}', ${index}, ${indexDetail}, ${indexSN}, this.value)">
                        </td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listDetailSerialNumberUpload').innerHTML = html;

            $('#detailSerialNumberModal').modal('show');
        }

        function changeSerialNumberDetail(type, index, indexDetail, indexSN, value) {
            const qc = JSON.parse(localStorage.getItem('qc')) ?? [];
            let serialNumber = [];

            console.info(type)

            if (type === 'parent') {
                qc[index].sn[indexSN].serialNumber = value;
                serialNumber = qc[index].sn;
            } else {
                qc[index].child[indexDetail].sn[indexSN].serialNumber = value;
                serialNumber = qc[index].child[indexDetail].sn;
            }

            let html = '';
            let number = 1;

            serialNumber.forEach((sn) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td><input type="text" class="form-control" value="${sn.serialNumber}" onchange="changeSerialNumberDetail(type, index, indexDetail, this.value)"></td>
                    </tr>
                `;
                number++;
            });

            document.getElementById('listDetailSerialNumberUpload').innerHTML = html;

            localStorage.setItem('qc', JSON.stringify(qc));
        }

        function deleteQC(index) {
            const qualityControl = JSON.parse(localStorage.getItem('qc')) ?? [];
            const products = JSON.parse(localStorage.getItem('master')) ?? [];
            const data = qualityControl[index];

            (data.parent).forEach((parent) => {
                const product = products.find((item => item.id === parent.id));
                product.qty = parent.qty;
                product.qty_qc = 0;
            });

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

                    // Validation Serial Number & QTY
                    const qualityControl = JSON.parse(localStorage.getItem('qc')) ?? [];
                    qualityControl.forEach((item) => {
                        item.forEach((product) => {
                            const qtySN = parseInt(product.serialNumber.length) + parseInt(product.SnDirect.length ?? []);
                            if (product.qty !== qtySN) {
                                Swal.fire({
                                    title: 'Warning!',
                                    text: 'Validation QTY & Serial Number Failed, Please Check Serial Number Product',
                                    icon: 'warning',
                                });

                                return true;
                            }
                        });
                    });

                    $.ajax({
                        url: '{{ route('inbound.quality-control-store-process') }}',
                        method: 'POST',
                        data:{
                            _token: '{{ csrf_token() }}',
                            qualityControl: JSON.parse(localStorage.getItem('qc')) ?? [],
                            purchaseOrderId: '{{ request()->get('id') }}',
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

        function addSerialNumberManual() {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];
            const qc = JSON.parse(localStorage.getItem('qc')) ?? [];

            const index = document.getElementById('detail_SN_index').value;
            const indexProduct = document.getElementById('detail_SN_productIndex').value;

            serialNumber.push("");

            if (serialNumber.length > parseInt(qc[index][indexProduct].qty)) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'qty serial number exceeds qty product',
                    icon: 'error'
                });

                const sound = new Audio("{{ asset('assets/sound/error.mp3') }}");
                sound.play();

                return true;
            }

            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
            viewSerialNumber();
        }

        document.getElementById('scanSerialNumber').addEventListener('keydown', function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                const value = this.value.trim();
                if (value !== "") {
                    const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];
                    const qc = JSON.parse(localStorage.getItem('qc')) ?? [];

                    const index = document.getElementById('detail_SN_index').value;
                    const indexProduct = document.getElementById('detail_SN_productIndex').value;

                    const checkSN = serialNumber.find((item) => item === value);
                    if (checkSN != null) {
                        document.getElementById('scanSerialNumberErrorMessage').innerText = "Serial number is already in the list";
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

                    serialNumber.push(value);

                    if (serialNumber.length > parseInt(qc[index][indexProduct].qty)) {
                        Swal.fire({
                            title: 'Warning!',
                            text: 'qty serial number exceeds qty product',
                            icon: 'error'
                        });

                        return true;
                    }

                    const sound = new Audio("{{ asset('assets/sound/scan.mp3') }}");
                    sound.play();

                    localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
                    viewSerialNumber();

                    document.getElementById('scanSerialNumber').value = "";
                    document.getElementById('scanSerialNumber').focus();
                } else {
                    document.getElementById('scanSerialNumberErrorMessage').innerText = "Serial number cannot be empty";
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

        document.getElementById('scanSerialNumberDirect').addEventListener('keydown', function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                const value = this.value.trim();
                if (value !== "") {
                    const index = document.getElementById('detail_Direct_index').value;
                    const indexProduct = document.getElementById('detail_Direct_productIndex').value;
                    const qc = JSON.parse(localStorage.getItem('qc')) ?? [];
                    const product = qc[index][indexProduct].SnDirect;

                    const check = product.find((item) => item === value);
                    if (check != null) {
                        document.getElementById('scanSerialNumberDirectErrorMessage').innerText = "Serial number is already in the list";
                        document.getElementById('scanSerialNumberDirectError').style.display = "block";

                        setTimeout(() => {
                            document.getElementById('scanSerialNumberDirectError').style.display = "none";
                        }, 3000);

                        const sound = new Audio("{{ asset('assets/sound/error.mp3') }}");
                        sound.play();

                        document.getElementById('scanSerialNumberDirect').value = "";
                        document.getElementById('scanSerialNumberDirect').focus();

                        return true;
                    }

                    const checkQTY = parseInt(qc[index][indexProduct].serialNumber.length) + parseInt(qc[index][indexProduct].SnDirect.length ?? []);
                    if (checkQTY === parseInt(qc[index][indexProduct].qty)) {
                        Swal.fire({
                            title: 'Warning!',
                            text: 'qty serial number exceeds qty product',
                            icon: 'error'
                        });

                        return true;
                    }

                    product.push(value);

                    localStorage.setItem('qc', JSON.stringify(qc));
                    viewSerialNumberDirect();

                    const sound = new Audio("{{ asset('assets/sound/scan.mp3') }}");
                    sound.play();

                    document.getElementById('scanSerialNumberDirect').value = "";
                    document.getElementById('scanSerialNumberDirect').focus();

                    viewListQC();
                } else {
                    document.getElementById('scanSerialNumberDirectErrorMessage').innerText = "Serial number cannot be empty";
                    document.getElementById('scanSerialNumberDirectError').style.display = "block";

                    setTimeout(() => {
                        document.getElementById('scanSerialNumberDirectError').style.display = "none";
                    }, 3000);

                    const sound = new Audio("{{ asset('assets/sound/error.mp3') }}");
                    sound.play();

                    document.getElementById('scanSerialNumberDirect').value = "";
                    document.getElementById('scanSerialNumberDirect').focus();
                }
            }
        });
    </script>
@endsection


















































