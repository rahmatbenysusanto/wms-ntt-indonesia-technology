@extends('layout.index')
@section('title', 'QC CCW')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Quality Control CCW</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item active">Quality Control CCW</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Upload File CCW</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-10">
                            <input type="file" class="form-control" id="fileUpload">
                        </div>
                        <div class="col-2">
                            <a class="btn btn-info w-100" onclick="uploadFileCCW()">Upload File CCW</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Purchase Order CCW</h4>
                        <a class="btn btn-primary" onclick="processQualityControl()">Process Quality Control</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="tableListProduct">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Put Away Step</th>
                                    <th>Line Number</th>
                                    <th>Item Name</th>
                                    <th>Item Desc</th>
                                    <th class="text-center">QTY</th>
                                    <th>Sales Doc</th>
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

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Purchase Order SAP</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="tableListPoSAP">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Sales Doc</th>
                                <th>Item</th>
                                <th>Material</th>
                                <th>PO Item Desc</th>
                                <th>Prod Hierarchy Desc</th>
                                <th class="text-center">QTY</th>
                            </tr>
                            </thead>
                            <tbody id="listPoSAP">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Default Modals -->
    <div id="listSalesDocModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">List Sales Doc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped align-middle" id="salesDocTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sales Doc</th>
                                <th>Item</th>
                                <th>Material</th>
                                <th>PO Item Desc</th>
                                <th>Prod Hierarchy Desc</th>
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
    </div>

    <div id="detailSerialNumberModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Detail Serial Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <table>
                        <tr>
                            <td class="fw-bold">Line Number</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1" id="detailSN_lineNumber"></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Item Name</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1" id="detailSN_itemName"></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Item Desc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1" id="detailSN_itemDesc"></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">QTY</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1" id="detailSN_qty"></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Sales Doc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1" id="detailSN_sales_doc"></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">QTY Scan Serial Number</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1" id="detailSN_qty_scan_serial_number"></td>
                        </tr>
                    </table>

                    <div class="mb-3">
                        <input type="text" class="form-control" id="scanSerialNumber" placeholder="Scan Serial Number" autofocus>
                    </div>

                    <div id="scanSerialNumberError" class="alert alert-danger alert-dismissible alert-label-icon label-arrow shadow fade show" role="alert" style="display: none">
                        <i class="ri-error-warning-line label-icon"></i>
                        <strong>Error</strong> - <span id="scanSerialNumberErrorMessage"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="row mb-3 mt-3">
                        <div class="col-8">
                            <input type="file" class="form-control" id="uploadFileSerialNumber">
                        </div>
                        <div class="col-2">
                            <a class="btn btn-primary w-100" onclick="uploadSerialNumber()">Upload Excel</a>
                        </div>
                        <div class="col-2">
                            <a class="btn btn-info w-100" onclick="addManualSN()">Add SN Manual</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex justify-content-between">
                                <h5>List Serial Number Available</h5>
                                <a class="btn btn-danger btn-sm" onclick="pilihSemuaSN()">Pilih Semua SN</a>
                            </div>
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Serial Number</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="listSerialNumberAvailableCCW">

                                </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <h5>List Serial Number SO</h5>
                            <table class="table table-striped align-middle">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Serial Number</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody id="listSerialNumber">

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <input type="hidden" id="detailSN_index">
                    <input type="hidden" id="detailSN_index_sales_doc">
                    <input type="hidden" id="detailSN_ccw_index">
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
                    <div class="mb-2">
                        <table>
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
                                <td class="fw-bold">QTY</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="detail_Direct_qty"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">QTY Scan Serial Number</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1" id="detail_Direct_qty_scan_serial_number"></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Inject Data -->
                    <input type="hidden" id="detail_Direct_index">
                    <input type="hidden" id="detail_Direct_indexSalesDoc">

                    <div class="mb-3">
                        <input type="text" class="form-control" id="scanSerialNumberDirect" placeholder="Scan Serial Number" autofocus>
                    </div>

                    <div id="scanSerialNumberDirectError" class="alert alert-danger alert-dismissible alert-label-icon label-arrow shadow fade show" role="alert" style="display: none">
                        <i class="ri-error-warning-line label-icon"></i>
                        <strong>Error</strong> - <span id="scanSerialNumberDirectErrorMessage"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="mb-3">
                        <div class="row">
                            <div class="col-2">
                                <label class="form-label text-white">-</label>
                                <div>
                                    <a class="btn btn-secondary w-100" onclick="addSerialNumberManualDirect()">SN Manual</a>
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

    <div id="manualSalesDocModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Manual Sales Doc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <table>
                                <tr>
                                    <td class="fw-bold">Line Number</td>
                                    <td class="fw-bold ps-2">:</td>
                                    <td class="ps-1" id="salesDocManual_line_number"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Item Name</td>
                                    <td class="fw-bold ps-2">:</td>
                                    <td class="ps-1" id="salesDocManual_item_name"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Item Desc</td>
                                    <td class="fw-bold ps-2">:</td>
                                    <td class="ps-1" id="salesDocManual_item_desc"></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">QTY</td>
                                    <td class="fw-bold ps-2">:</td>
                                    <td class="ps-1" id="salesDocManual_qty"></td>
                                </tr>
                            </table>
                        </div>

                        <input type="hidden" id="salesDocManual_compareIndex">

                        <div class="col-6">
                            <div class="mb-3 mt-4">
                                <label class="form-label">Sales Doc</label>
                                <input type="text" class="form-control" placeholder="Sales Doc ..." id="salesDocManual">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">QTY</label>
                                <input type="text" class="form-control" placeholder="Sales Doc ..." id="salesDocManualQTY">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a class="btn btn-primary" onclick="addSalesDocManualProcess()">Add Sales Doc Manual</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script src="https://unpkg.com/dexie@4/dist/dexie.js"></script>
    <script>
        // ================================
        // Dexie (IndexedDB) KV Helpers
        // ================================
        const db = new Dexie('qc-offline');
        db.version(1).stores({ kv: 'key' });

        const storage = {
            async getJSON(key, fallback = null) {
                const row = await db.kv.get(key);
                return row?.value ?? fallback;
            },
            async setJSON(key, value) { await db.kv.put({ key, value }); },
            async remove(key) { await db.kv.delete(key); },
            async clear() { await db.kv.clear(); }
        };
        const toInt = (v) => parseInt(v ?? 0, 10);

        // ================================
        // Init Page
        // ================================
        (async function initPage(){
            await storage.clear();
            await loadProductPurchaseOrder();
            await viewPoSAP();
        })();

        // ================================
        // Load PO -> sap
        // ================================
        async function loadProductPurchaseOrder() {
            const data = @json($purcDocDetail);
            const products = (data || []).map((item) => ({
                id: item.id,
                salesDoc: item.sales_doc,
                item: item.item,
                material: item.material,
                poItemDesc: item.po_item_desc,
                prodHierarchyDesc: item.prod_hierarchy_desc,
                qty: item.po_item_qty,
                select: 0,
                manual: false
            }));
            await storage.setJSON('sap', products);
        }

        // ================================
        // Upload CCW Excel -> ccw
        // ================================
        async function uploadFileCCW() {
            const fileInput = document.getElementById('fileUpload');
            const file = fileInput?.files?.[0];
            if (!file) return alert('Silakan pilih file Excel terlebih dahulu.');

            const reader = new FileReader();
            reader.onload = async (e) => {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheetName = workbook.SheetNames[1];
                const worksheet = workbook.Sheets[firstSheetName];
                const jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: '' });

                const filteredData = jsonData.map((row) => {
                    const snList = (row['Serial Numbers'] || '').split('\r\n').filter(Boolean);
                    const itemName = (row['Item Name'] || '').replace(/\./g, '');
                    const qty = toInt(row['Quantity Ordered']);
                    return {
                        lineNumber: row['Line Number'],
                        itemName,
                        itemDesc: row['Item Description'],
                        serialNumber: snList,
                        qty,
                        qtyAdd: 0,
                        salesDoc: [],
                        listSalesDoc: [],
                        purchaseOrderDetailId: null,
                        putAwayStep: 1,
                        snAvailable: snList.map(sn => ({ serialNumber: sn, status: true }))
                    };
                });

                await storage.setJSON('ccw', filteredData);
                await compareSAPCCW();
            };
            reader.readAsArrayBuffer(file);
        }

        // ================================
        // Match SAP vs CCW -> compare
        // ================================
        async function compareSAPCCW() {
            const sap = await storage.getJSON('sap', []);
            const ccw = await storage.getJSON('ccw', []);

            ccw.forEach((item) => {
                const findSAP = sap.filter(i => i.material === item.itemName);
                const findSAPQTY = findSAP.filter(i => toInt(i.qty) === toInt(item.qty));

                if (findSAPQTY.length === 1) {
                    const s = findSAPQTY[0];
                    item.salesDoc.push({
                        id: s.id,
                        salesDoc: s.salesDoc,
                        qty: s.qty,
                        serialNumber: item.serialNumber,
                        qtyDirect: 0,
                        snDirect: []
                    });
                    item.qtyAdd = s.qty;
                    s.select = 1;
                    item.purchaseOrderDetailId = s.id;
                } else {
                    item.listSalesDoc = findSAP;
                }
            });

            await storage.setJSON('compare', ccw);
            await storage.setJSON('sap', sap);
            await viewCompareSAPCCW();
        }

        function isParentFormat(value) {
            const str = String(value);
            const parts = str.split('.');
            return parts.length === 2 && parts[1] === '0';
        }

        // ================================
        // View Compare Table
        // ================================
        async function viewCompareSAPCCW() {
            const compare = await storage.getJSON('compare', []);
            let html = '';
            let number = 1;

            let currentPage = 0;
            if ($.fn.DataTable.isDataTable('#tableListProduct')) {
                currentPage = $('#tableListProduct').DataTable().page();
                $('#tableListProduct').DataTable().destroy();
            }

            compare.forEach((item, index) => {
                const isEmptySalesDoc = toInt(item.qty) !== toInt(item.qtyAdd);

                // SalesDoc chips/buttons
                let htmlSalesDoc = '';
                (item.salesDoc || []).forEach((sales, indexSalesDoc) => {
                    const done = (toInt(sales.serialNumber.length) + toInt(sales.snDirect.length)) === toInt(sales.qty);
                    htmlSalesDoc += `
          <div class="d-flex gap-2 align-items-center">
            <p class="mb-0" style="min-width: 140px;">${sales.salesDoc} <b>(QTY : ${sales.qty})</b></p>
            ${toInt(item.putAwayStep) === 0 ? `<a class="btn btn-dark btn-sm" onclick="directOutboundSerialNumber(${index}, ${indexSalesDoc})">SN Direct Outbound</a>` : ''}
            <a class="btn ${done ? 'btn-success' : 'btn-secondary'} btn-sm" onclick="detailSerialNumber(${index}, ${indexSalesDoc})">Serial Number</a>
            <a class="btn btn-danger btn-sm" onclick="hapusSalesDoc(${index}, ${indexSalesDoc}, ${sales.id})">Hapus</a>
          </div>`;
                });

                const statusParent = isParentFormat(item.lineNumber);
                const putAwayStep = `
        <div class="form-check form-switch form-switch-md">
          <input class="form-check-input" type="checkbox" role="switch"
            ${toInt(item.putAwayStep) === 1 ? 'checked' : ''}
            onchange="handlePutAwayStepChange(this, ${index})">
        </div>`;

                html += `
        <tr class="${isEmptySalesDoc ? 'table-danger' : ''}">
          <td>${number}</td>
          <td>${statusParent ? '<span class="badge bg-danger-subtle text-danger">Parent</span>' : '<span class="badge bg-secondary-subtle text-secondary">Child</span>'}</td>
          <td>${putAwayStep}</td>
          <td>${item.lineNumber}</td>
          <td>${item.itemName}</td>
          <td>${item.itemDesc}</td>
          <td class="text-center fw-bold">${item.qty}</td>
          <td><div class="d-flex flex-column gap-2">${htmlSalesDoc}</div></td>
          <td>${(toInt(item.qty) === toInt(item.qtyAdd)) ? '' : `
            <div class="d-flex gap-2">
              <a class="btn btn-info btn-sm" onclick="pilihSalesDoc('${index}')">Pilih Sales Doc</a>
              <a class="btn btn-warning btn-sm" onclick="manualSalesDoc('${index}')">Manual Sales Doc</a>
            </div>`}
          </td>
        </tr>`;
                number++;
            });

            document.getElementById('listProducts').innerHTML = html;
            const table = new DataTable('#tableListProduct');
            table.page(currentPage).draw('page');

            await viewPoSAP();
        }

        // ================================
        // Direct Outbound SN (modal)
        // ================================
        async function directOutboundSerialNumber(index, indexSalesDoc) {
            const compare = await storage.getJSON('compare', []);
            const product = compare[index];

            document.getElementById('detail_Direct_material').innerText = product.itemName;
            document.getElementById('detail_Direct_desc').innerText = product.itemDesc;
            document.getElementById('detail_Direct_qty').innerText = product.qty;
            document.getElementById('detail_Direct_index').value = index;
            document.getElementById('detail_Direct_indexSalesDoc').value = indexSalesDoc;

            await viewSerialNumberDirectOutbound(index, indexSalesDoc);

            $('#serialNumberDirectOutboundModal').modal('show');
            setTimeout(() => document.getElementById('scanSerialNumberDirect').focus(), 500);
        }

        async function viewSerialNumberDirectOutbound(index, indexSalesDoc) {
            const compare = await storage.getJSON('compare', []);
            const serialNumber = compare[index].salesDoc[indexSalesDoc].snDirect || [];
            let html = '';

            serialNumber.forEach((item, indexSN) => {
                html += `
        <tr>
          <td>${indexSN + 1}</td>
          <td><input type="text" class="form-control" value="${item}" onchange="changeSNDirect(${indexSN}, this.value)" placeholder="N/A"></td>
          <td><a class="btn btn-danger btn-sm">Delete</a></td>
        </tr>`;
            });

            document.getElementById('detail_Direct_qty_scan_serial_number').innerText = serialNumber.length;
            document.getElementById('listDetailSerialNumberDirect').innerHTML = html;
        }

        async function addSerialNumberManualDirect() {
            const index = document.getElementById('detail_Direct_index').value;
            const indexSalesDoc = document.getElementById('detail_Direct_indexSalesDoc').value;
            const compare = await storage.getJSON('compare', []);
            const serialNumber = compare[index].salesDoc[indexSalesDoc].snDirect;

            if ((serialNumber.length + compare[index].salesDoc[indexSalesDoc].serialNumber.length) === compare[index].salesDoc[indexSalesDoc].qty) {
                document.getElementById('scanSerialNumberDirectErrorMessage').innerText = 'Serial number exceeds item quantity';
                document.getElementById('scanSerialNumberDirectError').style.display = 'block';
                setTimeout(() => { document.getElementById('scanSerialNumberDirectError').style.display = 'none'; }, 3000);
                new Audio("{{ asset('assets/sound/error.mp3') }}").play();
                document.getElementById('scanSerialNumberDirect').value = '';
                document.getElementById('scanSerialNumberDirect').focus();
                return true;
            }

            serialNumber.push('');
            compare[index].salesDoc[indexSalesDoc].qtyDirect = serialNumber.length;

            await storage.setJSON('compare', compare);
            await viewSerialNumberDirectOutbound(index, indexSalesDoc);
        }

        async function changeSNDirect(indexSN, value) {
            const index = document.getElementById('detail_Direct_index').value;
            const indexSalesDoc = document.getElementById('detail_Direct_indexSalesDoc').value;
            const compare = await storage.getJSON('compare', []);
            const serialNumber = compare[index].salesDoc[indexSalesDoc].snDirect;

            serialNumber[indexSN] = value;
            compare[index].salesDoc[indexSalesDoc].qtyDirect = serialNumber.length;

            await storage.setJSON('compare', compare);
            await viewSerialNumberDirectOutbound(index, indexSalesDoc);
        }

        // ================================
        // PutAway Toggle
        // ================================
        async function handlePutAwayStepChange(checkbox, index) {
            const compare = await storage.getJSON('compare', []);
            compare[index].putAwayStep = checkbox.checked ? 1 : 0;
            await storage.setJSON('compare', compare);
            await viewCompareSAPCCW();
        }

        // ================================
        // Hapus SalesDoc
        // ================================
        async function hapusSalesDoc(index, indexSalesDoc, idPoDetail) {
            const sap = await storage.getJSON('sap', []);
            const compare = await storage.getJSON('compare', []);
            const ccw = await storage.getJSON('ccw', []);

            // Kembalikan SN ke available
            const serialNumber = compare[index].salesDoc[indexSalesDoc].serialNumber || [];
            serialNumber.forEach((sn) => {
                const change = ccw[index].snAvailable.find((s) => s.serialNumber === sn);
                if (change) change.status = true;
            });

            // Hapus dari compare
            const findCcwData = compare[index].salesDoc[indexSalesDoc];
            compare[index].salesDoc.splice(indexSalesDoc, 1);
            compare[index].qtyAdd = toInt(compare[index].qtyAdd) - toInt(findCcwData.qty);

            // Reset SAP
            const sapIndex = sap.findIndex(i => toInt(i.id) === toInt(idPoDetail));
            if (sapIndex !== -1) {
                const item = sap[sapIndex];
                if (item.manual === true) sap.splice(sapIndex, 1); else item.select = 0;
            }

            await storage.setJSON('sap', sap);
            await storage.setJSON('compare', compare);
            await storage.setJSON('ccw', ccw);

            await viewCompareSAPCCW();
        }

        // ================================
        // View PO SAP (select = 0)
        // ================================
        async function viewPoSAP() {
            const sap = await storage.getJSON('sap', []);
            const sapFilter = sap.filter(i => toInt(i.select) === 0);

            let currentPage = 0;
            if ($.fn.DataTable.isDataTable('#tableListPoSAP')) {
                currentPage = $('#tableListPoSAP').DataTable().page();
                $('#tableListPoSAP').DataTable().destroy();
            }

            let html = '';
            let number = 1;
            sapFilter.forEach((item) => {
                html += `
        <tr>
          <td>${number}</td>
          <td>${item.salesDoc}</td>
          <td>${item.item}</td>
          <td>${item.material}</td>
          <td>${item.poItemDesc}</td>
          <td>${item.prodHierarchyDesc}</td>
          <td class="text-center fw-bold">${item.qty}</td>
        </tr>`;
                number++;
            });

            document.getElementById('listPoSAP').innerHTML = html;
            const table = new DataTable('#tableListPoSAP');
            table.page(currentPage).draw('page');
        }

        // ================================
        // Detail SN Modal (compare -> serialNumber temp)
        // ================================
        async function detailSerialNumber(index, indexSalesDoc) {
            const compare = await storage.getJSON('compare', []);
            const salesDoc = compare[index].salesDoc[indexSalesDoc];

            document.getElementById('detailSN_lineNumber').innerText = compare[index].lineNumber;
            document.getElementById('detailSN_itemName').innerText = compare[index].itemName;
            document.getElementById('detailSN_itemDesc').innerText = compare[index].itemDesc;
            document.getElementById('detailSN_qty').innerText = salesDoc.qty;
            document.getElementById('detailSN_sales_doc').innerText = salesDoc.salesDoc;
            document.getElementById('detailSN_index').value = index;
            document.getElementById('detailSN_index_sales_doc').value = indexSalesDoc;

            await storage.setJSON('serialNumber', salesDoc.serialNumber);
            await viewSerialNumber(index, indexSalesDoc);

            $('#detailSerialNumberModal').modal('show');
            setTimeout(() => document.getElementById('scanSerialNumber').focus(), 500);
        }

        // Upload Excel SN
        async function uploadSerialNumber() {
            const fileInput = document.getElementById('uploadFileSerialNumber');
            const file = fileInput?.files?.[0];
            if (!file) return alert('Silakan pilih file Excel terlebih dahulu.');

            const reader = new FileReader();
            reader.onload = async (e) => {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                const jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: '' });
                const serialNumber = jsonData.map((row) => row['Serial Number']);

                const index = document.getElementById('detailSN_index').value;
                const indexSalesDoc = document.getElementById('detailSN_index_sales_doc').value;
                const compare = await storage.getJSON('compare', []);

                if (serialNumber.length === toInt(compare[index].salesDoc[indexSalesDoc].qty)) {
                    Swal.fire({ title: 'Warning!', text: 'Jumlah Serial Number tidak boleh lebih dari QTY', icon: 'warning' });
                    return true;
                }

                compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;
                await storage.setJSON('compare', compare);
                await storage.setJSON('serialNumber', serialNumber);
                fileInput.value = '';

                await viewSerialNumber(index, indexSalesDoc);
            };
            reader.readAsArrayBuffer(file);
        }

        async function viewSerialNumber(index, indexSalesDoc) {
            const serialNumber = await storage.getJSON('serialNumber', []);
            let html = '';
            let number = 1;

            (serialNumber || []).forEach((item, indexDetail) => {
                html += `
        <tr>
          <td>${number}</td>
          <td><input type="text" class="form-control" value="${item}" onchange="changeSerialNumber(${index}, ${indexSalesDoc}, ${indexDetail}, this.value)" placeholder="N/A"></td>
          <td><a class="btn btn-danger btn-sm" onclick="deleteSerialNumber(${index}, ${indexSalesDoc}, ${indexDetail}, '${item}')">Delete</a></td>
        </tr>`;
                number++;
            });

            document.getElementById('detailSN_qty_scan_serial_number').innerText = serialNumber.length;
            document.getElementById('listSerialNumber').innerHTML = html;

            // SN Available dari CCW
            const ccw = await storage.getJSON('ccw', []);
            const data = ccw[index];

            let htmlSN = '';
            (data.snAvailable || []).forEach((i) => {
                const btn = i.status ? `<a class="btn btn-primary btn-sm" onclick="selectSnAvailable('${i.serialNumber}')">Select SN</a>` : '';
                htmlSN += `<tr><td>${i.serialNumber}</td><td>${btn}</td></tr>`;
            });

            document.getElementById('detailSN_ccw_index').value = index;
            document.getElementById('listSerialNumberAvailableCCW').innerHTML = htmlSN;
        }

        async function addManualSN() {
            const index = document.getElementById('detailSN_index').value;
            const indexSalesDoc = document.getElementById('detailSN_index_sales_doc').value;

            const compare = await storage.getJSON('compare', []);
            let serialNumber = await storage.getJSON('serialNumber', []);

            if (serialNumber.length === toInt(compare[index].salesDoc[indexSalesDoc].qty)) {
                Swal.fire({ title: 'Warning!', text: 'Jumlah Serial Number tidak boleh lebih dari QTY', icon: 'warning' });
                return true;
            }

            serialNumber.push('');
            compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;
            await storage.setJSON('compare', compare);
            await storage.setJSON('serialNumber', serialNumber);

            await viewSerialNumber(index, indexSalesDoc);
        }

        async function deleteSerialNumber(index, indexSalesDoc, indexDetail, sn) {
            const compare = await storage.getJSON('compare', []);
            const serialNumber = await storage.getJSON('serialNumber', []);

            serialNumber.splice(indexDetail, 1);
            compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;

            await storage.setJSON('compare', compare);
            await storage.setJSON('serialNumber', serialNumber);

            const ccw = await storage.getJSON('ccw', []);
            const data = ccw[index];
            (data.snAvailable || []).forEach((item) => { if (item.serialNumber === sn) item.status = true; });
            await storage.setJSON('ccw', ccw);

            await viewSerialNumber(index, indexSalesDoc);
        }

        async function changeSerialNumber(index, indexSalesDoc, indexDetail, value) {
            const compare = await storage.getJSON('compare', []);
            const serialNumber = await storage.getJSON('serialNumber', []);

            serialNumber[indexDetail] = value;
            compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;

            await storage.setJSON('compare', compare);
            await storage.setJSON('serialNumber', serialNumber);

            await viewSerialNumber(index, indexSalesDoc);
        }

        // ================================
        // Pilih SalesDoc dari SAP
        // ================================
        async function pilihSalesDoc(index) {
            const sap = await storage.getJSON('sap', []);
            const compare = await storage.getJSON('compare', []);
            const compareFind = compare[index];

            const sapFilter = sap.filter(item => (item.material === compareFind.itemName && item.select === 0));

            let html = '';
            let number = 1;
            sapFilter.forEach((item, indexDetail) => {
                html += `
        <tr>
          <td>${number}</td>
          <td>${item.salesDoc}</td>
          <td>${item.item}</td>
          <td>${item.material}</td>
          <td>${item.poItemDesc}</td>
          <td>${item.prodHierarchyDesc}</td>
          <td>${item.qty}</td>
          <td><a class="btn btn-info btn-sm" id="btn-sales-doc-${index}-${item.id}-${indexDetail}" onclick="pilihSalesDocProcess('${index}', '${item.id}', ${indexDetail})">Pilih Sales Doc</a></td>
        </tr>`;
                number++;
            });

            if ($.fn.DataTable.isDataTable('#salesDocTable')) {
                $('#salesDocTable').DataTable().destroy();
            }

            document.getElementById('listSalesDoc').innerHTML = html;
            $('#salesDocTable').DataTable({ pageLength: 10 });
            $('#listSalesDocModal').modal('show');
        }

        async function pilihSalesDocProcess(index, id, indexDetail) {
            const sap = await storage.getJSON('sap', []);
            const compare = await storage.getJSON('compare', []);
            const findSAP = sap.find(i => toInt(i.id) === toInt(id));

            if (toInt(compare[index].qtyAdd + findSAP.qty) > toInt(compare[index].qty)) {
                Swal.fire({ title: 'Warning!', text: 'QTY melebihi ketentuan PO', icon: 'warning' });
                return true;
            }

            compare[index].salesDoc.push({
                id: findSAP.id,
                salesDoc: findSAP.salesDoc,
                qty: findSAP.qty,
                serialNumber: [],
                qtyDirect: 0,
                snDirect: [],
                manual: findSAP.manual,
                sap: findSAP
            });
            compare[index].qtyAdd += findSAP.qty;
            findSAP.select = 1;

            await storage.setJSON('sap', sap);
            await storage.setJSON('compare', compare);

            if (toInt(compare[index].qtyAdd) === toInt(compare[index].qty)) $('#listSalesDocModal').modal('hide');
            if (indexDetail !== null) {
                const el = document.getElementById(`btn-sales-doc-${index}-${id}-${indexDetail}`);
                if (el) el.style.display = 'none';
            }
            await viewCompareSAPCCW();
        }

        // ================================
        // Proses QC (upload JSON + store)
        // ================================
        async function processQualityControl() {
            Swal.fire({
                title: 'Are you sure?', text: 'Process Quality Control', icon: 'warning', showCancelButton: true,
                customClass: { confirmButton: 'btn btn-primary w-xs me-2 mt-2', cancelButton: 'btn btn-danger w-xs mt-2' },
                confirmButtonText: 'Yes, Process it!', buttonsStyling: false, showCloseButton: true
            }).then(async function(t) {
                if (!t.value) return;

                // Validation Serial Number n/a / qty
                const compare = await storage.getJSON('compare', []);
                for (const item of compare) {
                    for (const salesDoc of (item.salesDoc || [])) {
                        if ((salesDoc.serialNumber.length + salesDoc.snDirect.length) !== salesDoc.qty) {
                            const sisaQTY = (salesDoc.serialNumber.length + salesDoc.snDirect.length) - salesDoc.qty;
                            for (let i = 0; i < sisaQTY; i++) salesDoc.serialNumber.push('N/A');
                        }
                    }
                    if (toInt(item.qty) !== toInt(item.qtyAdd)) {
                        Swal.fire({ title: 'Warning!', text: 'There are unprocessed CCW items', icon: 'warning' });
                        return true;
                    }
                }
                await storage.setJSON('compare', compare);

                // Upload JSON dahulu
                await sendLocalStorageAsJsonFileToBackend({
                    key: 'compare', filename: 'compare.json', uploadUrl: '{{ route('inbound.quality-control.upload.ccw') }}',
                    onSuccess: async (fileName) => {
                        $.ajax({
                            url: '{{ route('inbound.quality-control-process-ccw-store') }}', method: 'POST',
                            data: { _token: '{{ csrf_token() }}', fileName: JSON.stringify(await storage.getJSON('fileName')), purchaseOrderId: '{{ request()->get('id') }}' },
                            success: (res) => {
                                if (res.status) {
                                    Swal.fire({ title: 'Success!', text: 'Quality Control successfully!', icon: 'success', confirmButtonText: 'OK', customClass: { confirmButton: 'btn btn-primary w-xs mt-2' }, buttonsStyling: false })
                                        .then(() => { window.location.href = '{{ route('inbound.quality-control') }}'; });
                                } else {
                                    Swal.fire({ title: 'Error!', text: 'Quality Control Failed!', icon: 'error', confirmButtonText: 'OK', customClass: { confirmButton: 'btn btn-primary w-xs mt-2' }, buttonsStyling: false });
                                }
                            }
                        });
                    }
                });
            });
        }

        async function sendLocalStorageAsJsonFileToBackend({ key = 'compare', filename = 'compare.json', uploadUrl, onSuccess }) {
            const rawObj = await storage.getJSON(key, null);
            if (!rawObj) { console.error(`❌ Tidak ada data untuk key "${key}"`); return; }

            const rawData = JSON.stringify(rawObj);
            const blob = new Blob([rawData], { type: 'application/json' });
            const file = new File([blob], filename, { type: 'application/json' });

            const formData = new FormData();
            formData.append('json_file', file);
            formData.append('_token', '{{ csrf_token() }}');

            return fetch(uploadUrl, { method: 'POST', body: formData })
                .then(r => r.json())
                .then(async (result) => {
                    await storage.setJSON('fileName', result.fileName);
                    if (typeof onSuccess === 'function') onSuccess(filename);
                })
                .catch(err => console.error('❌ Upload gagal:', err));
        }

        // ================================
        // Pilih SN Available (CCW)
        // ================================
        async function selectSnAvailable(value) {
            const index = document.getElementById('detailSN_index').value;
            const indexSalesDoc = document.getElementById('detailSN_index_sales_doc').value;

            const compare = await storage.getJSON('compare', []);
            let serialNumber = await storage.getJSON('serialNumber', []);

            if (serialNumber.length === toInt(compare[index].salesDoc[indexSalesDoc].qty)) {
                Swal.fire({ title: 'Warning!', text: 'qty serial number exceeds qty product', icon: 'error' });
                return true;
            }

            const exists = serialNumber.find((i) => i === value);
            if (exists != null) {
                document.getElementById('scanSerialNumberErrorMessage').innerText = 'Serial number is already in the list';
                document.getElementById('scanSerialNumberError').style.display = 'block';
                setTimeout(() => { document.getElementById('scanSerialNumberError').style.display = 'none'; }, 3000);
                new Audio("{{ asset('assets/sound/error.mp3') }}").play();
                const input = document.getElementById('scanSerialNumber');
                if (input) { input.value = ''; input.focus(); }
                return true;
            }

            serialNumber.push(value);
            compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;

            await storage.setJSON('compare', compare);
            await storage.setJSON('serialNumber', serialNumber);

            new Audio("{{ asset('assets/sound/scan.mp3') }}").play();

            const ccw = await storage.getJSON('ccw', []);
            const data = ccw[index];
            (data.snAvailable || []).forEach((item) => { if (item.serialNumber === value) item.status = false; });
            await storage.setJSON('ccw', ccw);

            await viewSerialNumber(index, indexSalesDoc);
            const input = document.getElementById('scanSerialNumber');
            if (input) { input.value = ''; input.focus(); }
            await viewCompareSAPCCW();
        }

        // ================================
        // Scanner Enter Listeners (SN & SN Direct)
        // ================================
        document.getElementById('scanSerialNumber')?.addEventListener('keydown', async function(e) {
            if (e.key !== 'Enter') return; e.preventDefault();
            const value = this.value.trim();

            if (!value) {
                document.getElementById('scanSerialNumberErrorMessage').innerText = 'Serial number cannot be empty';
                document.getElementById('scanSerialNumberError').style.display = 'block';
                setTimeout(() => { document.getElementById('scanSerialNumberError').style.display = 'none'; }, 3000);
                new Audio("{{ asset('assets/sound/error.mp3') }}").play();
                this.value = ''; this.focus();
                return;
            }

            const index = document.getElementById('detailSN_index').value;
            const indexSalesDoc = document.getElementById('detailSN_index_sales_doc').value;
            const compare = await storage.getJSON('compare', []);
            let serialNumber = await storage.getJSON('serialNumber', []);

            if (serialNumber.length === toInt(compare[index].salesDoc[indexSalesDoc].qty)) {
                Swal.fire({ title: 'Warning!', text: 'qty serial number exceeds qty product', icon: 'error' });
                return true;
            }

            const exists = serialNumber.find((i) => i === value);
            if (exists != null) {
                document.getElementById('scanSerialNumberErrorMessage').innerText = 'Serial number is already in the list';
                document.getElementById('scanSerialNumberError').style.display = 'block';
                setTimeout(() => { document.getElementById('scanSerialNumberError').style.display = 'none'; }, 3000);
                new Audio("{{ asset('assets/sound/error.mp3') }}").play();
                this.value = ''; this.focus();
                return true;
            }

            serialNumber.push(value);
            compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;

            await storage.setJSON('compare', compare);
            await storage.setJSON('serialNumber', serialNumber);

            new Audio("{{ asset('assets/sound/scan.mp3') }}").play();
            await viewSerialNumber(index, indexSalesDoc);
            this.value = ''; this.focus();
            await viewCompareSAPCCW();
        });

        document.getElementById('scanSerialNumberDirect')?.addEventListener('keydown', async function(e) {
            if (e.key !== 'Enter') return; e.preventDefault();
            const value = this.value.trim();
            if (!value) {
                document.getElementById('scanSerialNumberDirectErrorMessage').innerText = 'Serial number cannot be empty';
                document.getElementById('scanSerialNumberDirectError').style.display = 'block';
                setTimeout(() => { document.getElementById('scanSerialNumberDirectError').style.display = 'none'; }, 3000);
                new Audio("{{ asset('assets/sound/error.mp3') }}").play();
                this.value = ''; this.focus();
                return;
            }

            const index = document.getElementById('detail_Direct_index').value;
            const indexSalesDoc = document.getElementById('detail_Direct_indexSalesDoc').value;
            const compare = await storage.getJSON('compare', []);
            const serialNumber = compare[index].salesDoc[indexSalesDoc].snDirect;

            const exists = serialNumber.find((i) => i === value);
            if (exists != null) {
                document.getElementById('scanSerialNumberDirectErrorMessage').innerText = 'Serial number is already in the list';
                document.getElementById('scanSerialNumberDirectError').style.display = 'block';
                setTimeout(() => { document.getElementById('scanSerialNumberDirectError').style.display = 'none'; }, 3000);
                new Audio("{{ asset('assets/sound/error.mp3') }}").play();
                this.value = ''; this.focus();
                return true;
            }

            const checkQTY = serialNumber.length + compare[index].salesDoc[indexSalesDoc].serialNumber.length;
            if (checkQTY === toInt(compare[index].salesDoc[indexSalesDoc].qty)) {
                Swal.fire({ title: 'Warning!', text: 'qty serial number exceeds qty product', icon: 'error' });
                return true;
            }

            serialNumber.push(value);
            compare[index].salesDoc[indexSalesDoc].qtyDirect = serialNumber.length;

            await storage.setJSON('compare', compare);
            await viewSerialNumberDirectOutbound(index, indexSalesDoc);

            new Audio("{{ asset('assets/sound/scan.mp3') }}").play();
            this.value = ''; this.focus();
            await viewCompareSAPCCW();
        });

        // ================================
        // Pilih semua SN available
        // ================================
        async function pilihSemuaSN() {
            const ccw = await storage.getJSON('ccw', []);
            const indexCCW = document.getElementById('detailSN_ccw_index').value;
            const list = (ccw[indexCCW]?.snAvailable || []);
            for (const item of list) { if (item.status === true) await selectSnAvailable(item.serialNumber); }
        }

        // ================================
        // Manual Sales Doc (Modal)
        // ================================
        async function manualSalesDoc(index) {
            const compare = await storage.getJSON('compare', []);
            const findCompare = compare[index];
            await storage.setJSON('salesDocManual', findCompare);

            document.getElementById('salesDocManual_line_number').innerText = findCompare.lineNumber;
            document.getElementById('salesDocManual_item_name').innerText = findCompare.itemName;
            document.getElementById('salesDocManual_item_desc').innerText = findCompare.itemDesc;
            document.getElementById('salesDocManual_qty').innerText = findCompare.qty;
            document.getElementById('salesDocManual_compareIndex').value = index;

            $('#manualSalesDocModal').modal('show');
        }

        async function addSalesDocManualProcess() {
            const sap = await storage.getJSON('sap', []);
            const compare = await storage.getJSON('compare', []);
            const salesDocManual = await storage.getJSON('salesDocManual', {});

            // simple unique id (YYYYMMDDHHMMSS)
            const id = new Date().toISOString().replace(/[-:TZ.]/g, '').slice(0,14);

            sap.push({
                id,
                item: '',
                material: salesDocManual.itemName,
                poItemDesc: salesDocManual.itemDesc,
                prodHierarchyDesc: '',
                qty: toInt(document.getElementById('salesDocManualQTY').value),
                salesDoc: document.getElementById('salesDocManual').value,
                select: 1,
                manual: true
            });
            await storage.setJSON('sap', sap);

            // pilih otomatis
            const index = toInt(document.getElementById('salesDocManual_compareIndex').value);
            await pilihSalesDocProcess(index, id, null);

            document.getElementById('salesDocManualQTY').value = '';
            document.getElementById('salesDocManual').value = '';
            $('#manualSalesDocModal').modal('hide');
        }
    </script>
@endsection
























