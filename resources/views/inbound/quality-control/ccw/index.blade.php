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
                        <div class="d-flex gap-2">
                            <div class="input-group" style="width: 400px;">
                                <input type="text" class="form-control" id="prefixMassDelete"
                                    placeholder="Prefix (cth: 1)">
                                <button class="btn btn-outline-danger" type="button" onclick="massDeleteSOByPrefix()">Mass
                                    Delete SO</button>
                                <button class="btn btn-danger" type="button" onclick="massDeleteRowByPrefix()">Mass
                                    Delete Baris</button>
                            </div>
                            <a class="btn btn-warning" onclick="saveDraft()">Save Draft CCW</a>
                            <a class="btn btn-primary" onclick="processQualityControl()">Process Quality Control</a>
                        </div>
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
    <div id="listSalesDocModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
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

    <div id="detailSerialNumberModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
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
                        <input type="text" class="form-control" id="scanSerialNumber"
                            placeholder="Scan Serial Number" autofocus>
                    </div>

                    <div id="scanSerialNumberError"
                        class="alert alert-danger alert-dismissible alert-label-icon label-arrow shadow fade show"
                        role="alert" style="display: none">
                        <i class="ri-error-warning-line label-icon"></i>
                        <strong>Error</strong> - <span id="scanSerialNumberErrorMessage"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="row mb-3 mt-3">
                        <div class="col-6">
                            <input type="file" class="form-control" id="uploadFileSerialNumber">
                        </div>
                        <div class="col-2">
                            <a class="btn btn-primary w-100" onclick="uploadSerialNumber()">Upload Excel</a>
                        </div>
                        <div class="col-2">
                            <a class="btn btn-info w-100" onclick="addManualSN()">Add SN Manual</a>
                        </div>
                        <div class="col-2">
                            <a class="btn btn-warning w-100" onclick="generateNA()">Generate N/A</a>
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

    <div id="serialNumberDirectOutboundModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
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
                        <input type="text" class="form-control" id="scanSerialNumberDirect"
                            placeholder="Scan Serial Number" autofocus>
                    </div>

                    <div id="scanSerialNumberDirectError"
                        class="alert alert-danger alert-dismissible alert-label-icon label-arrow shadow fade show"
                        role="alert" style="display: none">
                        <i class="ri-error-warning-line label-icon"></i>
                        <strong>Error</strong> - <span id="scanSerialNumberDirectErrorMessage"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="mb-3">
                        <div class="row">
                            <div class="col-2">
                                <label class="form-label text-white">-</label>
                                <div>
                                    <a class="btn btn-secondary w-100" onclick="addSerialNumberManualDirect()">SN
                                        Manual</a>
                                </div>
                            </div>
                            <div class="col-2">
                                <label class="form-label text-white">-</label>
                                <div>
                                    <a class="btn btn-warning w-100" onclick="generateNADirect()">Generate N/A</a>
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

    <div id="manualSalesDocModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
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
                                <input type="text" class="form-control" placeholder="Sales Doc ..."
                                    id="salesDocManual">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">QTY</label>
                                <input type="text" class="form-control" placeholder="Sales Doc ..."
                                    id="salesDocManualQTY">
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
    <script src="https://unpkg.com/dexie@4/dist/dexie.js"></script>
    <script>
        // ================================
        // Dexie (IndexedDB) KV Helpers
        // ================================
        const db = new Dexie('qc-offline');
        db.version(1).stores({
            kv: 'key'
        });

        const storage = {
            async getJSON(key, fallback = null) {
                const row = await db.kv.get(key);
                return row?.value ?? fallback;
            },
            async setJSON(key, value) {
                await db.kv.put({
                    key,
                    value
                });
            },
            async remove(key) {
                await db.kv.delete(key);
            },
            async clear() {
                await db.kv.clear();
            }
        };
        const toInt = (v) => parseInt(v ?? 0, 10);

        // ================================
        // Init Page (with Draft support)
        // ================================
        (async function initPage() {
            await storage.clear();

            // Try load draft first; if none, load from Blade data
            const hasDraft = await loadDraftIfAny();
            if (!hasDraft) {
                await loadProductPurchaseOrder();
            }

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
                qty: toInt(item.po_item_qty) - toInt(item.qty_qc),
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
                const workbook = XLSX.read(data, {
                    type: 'array'
                });
                const firstSheetName = workbook.SheetNames[1];
                const worksheet = workbook.Sheets[firstSheetName];
                const jsonData = XLSX.utils.sheet_to_json(worksheet, {
                    defval: ''
                });

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
                        snAvailable: snList.map(sn => ({
                            serialNumber: sn,
                            status: true
                        }))
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
                        snDirect: [],
                        manual: false
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
                    const done = (toInt(sales.serialNumber.length) + toInt(sales.snDirect.length)) ===
                        toInt(sales.qty);
                    htmlSalesDoc += `
          <div class="d-flex gap-2 align-items-center">
            <p class="mb-0" style="min-width: 140px;">${sales.salesDoc} <b>(QTY : ${sales.qty})</b></p>
            ${toInt(item.putAwayStep) === 0 ? `<a class="btn btn-dark btn-sm" onclick="directOutboundSerialNumber(${index}, ${indexSalesDoc})">SN Direct Outbound</a>` : ''}
            <a class="btn ${done ? 'btn-success' : 'btn-secondary'} btn-sm" onclick="detailSerialNumber(${index}, ${indexSalesDoc})">Serial Number</a>
            <a class="btn btn-danger btn-sm" onclick="hapusSalesDoc(${index}, ${indexSalesDoc}, ${sales.id})">Hapus SO</a>
          </div>`;
                });

                const statusParent = isParentFormat(item.lineNumber);
                const putAwayStep = `
        <div class="form-check form-switch form-switch-md">
          <input class="form-check-input" type="checkbox" role="switch"
            ${toInt(item.putAwayStep) === 1 ? 'checked' : ''}
            onchange="handlePutAwayStepChange(this, ${index})">
        </div>`;

                let typeLabel = '';
                if (item.typeManual) {
                    typeLabel = item.typeManual === 'Parent' ?
                        '<span class="badge bg-danger-subtle text-danger" style="cursor: pointer;" onclick="changeTypeProcess(' +
                        index + ')">Parent <i class="ri-refresh-line ms-1"></i></span>' :
                        '<span class="badge bg-secondary-subtle text-secondary" style="cursor: pointer;" onclick="changeTypeProcess(' +
                        index + ')">Child <i class="ri-refresh-line ms-1"></i></span>';
                } else {
                    typeLabel = statusParent ?
                        '<span class="badge bg-danger-subtle text-danger" style="cursor: pointer;" onclick="changeTypeProcess(' +
                        index + ')">Parent <i class="ri-refresh-line ms-1"></i></span>' :
                        '<span class="badge bg-secondary-subtle text-secondary" style="cursor: pointer;" onclick="changeTypeProcess(' +
                        index + ')">Child <i class="ri-refresh-line ms-1"></i></span>';
                }

                html += `
        <tr class="${isEmptySalesDoc ? 'table-danger' : ''}">
          <td>${number}</td>
          <td>${typeLabel}</td>
          <td>${putAwayStep}</td>
          <td>${item.lineNumber}</td>
          <td>${item.itemName}</td>
          <td>${item.itemDesc}</td>
          <td class="text-center fw-bold">${item.qty}</td>
          <td><div class="d-flex flex-column gap-2">${htmlSalesDoc}</div></td>
          <td>
            <div class="d-flex gap-2">
                ${(toInt(item.qty) === toInt(item.qtyAdd)) ? '' : `
                                            <a class="btn btn-info btn-sm" onclick="pilihSalesDoc('${index}')">Pilih Sales Doc</a>
                                            <a class="btn btn-warning btn-sm" onclick="manualSalesDoc('${index}')">Manual Sales Doc</a>
                                        `}
                <a class="btn btn-danger btn-sm" onclick="deleteRow('${index}')">Hapus Baris</a>
            </div>
          </td>
        </tr>`;
                number++;
            });

            document.getElementById('listProducts').innerHTML = html;
            const table = new DataTable('#tableListProduct', {
                stateSave: true
            });

            const pageCount = table.page.info().pages;
            if (currentPage < pageCount) {
                table.page(currentPage).draw(false);
            } else if (pageCount > 0) {
                table.page(pageCount - 1).draw(false);
            }

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

            if ((serialNumber.length + compare[index].salesDoc[indexSalesDoc].serialNumber.length) === compare[index]
                .salesDoc[indexSalesDoc].qty) {
                document.getElementById('scanSerialNumberDirectErrorMessage').innerText =
                    'Serial number exceeds item quantity';
                document.getElementById('scanSerialNumberDirectError').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('scanSerialNumberDirectError').style.display = 'none';
                }, 3000);
                new Audio("{{ asset('assets/sound/error.mp3') }}").play();
                document.getElementById('scanSerialNumberDirect').value = '';
                document.getElementById('scanSerialNumberDirect').focus();
                return true;
            }

            serialNumber.push('');
            compare[index].salesDoc[indexSalesDoc].qtyDirect = serialNumber.length;

            await storage.setJSON('compare', compare);
            await viewSerialNumberDirectOutbound(index, indexSalesDoc);
            await viewCompareSAPCCW();
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
            await viewCompareSAPCCW();
        }

        async function generateNADirect() {
            const index = document.getElementById('detail_Direct_index').value;
            const indexSalesDoc = document.getElementById('detail_Direct_indexSalesDoc').value;
            const compare = await storage.getJSON('compare', []);
            const salesDoc = compare[index].salesDoc[indexSalesDoc];
            const serialNumber = salesDoc.snDirect || [];

            const targetQty = toInt(salesDoc.qty);
            const currentQty = (serialNumber.length + salesDoc.serialNumber.length);

            if (currentQty >= targetQty) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Jumlah Serial Number sudah lengkap',
                    icon: 'warning'
                });
                return;
            }

            const diff = targetQty - currentQty;
            for (let i = 0; i < diff; i++) {
                serialNumber.push('N/A');
            }

            salesDoc.qtyDirect = serialNumber.length;
            await storage.setJSON('compare', compare);
            await viewSerialNumberDirectOutbound(index, indexSalesDoc);
            await viewCompareSAPCCW();
        }

        // ================================
        // Mass Delete Row By Line Prefix (Hapus Baris)
        // ================================
        async function massDeleteRowByPrefix() {
            const prefix = document.getElementById('prefixMassDelete').value;
            if (!prefix) return alert('Silakan masukkan prefix angka depan (contoh: 1)');

            Swal.fire({
                title: 'Konfirmasi Mass Delete Row',
                text: `Anda yakin ingin menghapus semua BARIS dengan prefix "${prefix}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (!result.value) return;

                const sap = await storage.getJSON('sap', []);
                const compare = await storage.getJSON('compare', []);
                const ccw = await storage.getJSON('ccw', []);

                const regex = new RegExp('^' + prefix + '\\.');
                let affectedCount = 0;
                const newCompare = [];
                const newCcw = [];

                compare.forEach((item, index) => {
                    if (regex.test(item.lineNumber)) {
                        // Item ini akan dihapus
                        affectedCount++;
                        // Reset SAP status yang terhubung dengan item ini
                        (item.salesDoc || []).forEach(sales => {
                            const sapIndex = sap.findIndex(s => toInt(s.id) === toInt(sales
                                .id));
                            if (sapIndex !== -1) {
                                if (sap[sapIndex].manual === true) {
                                    sap[sapIndex]._toDelete = true;
                                } else {
                                    sap[sapIndex].select = 0;
                                }
                            }
                        });
                    } else {
                        newCompare.push(item);
                        newCcw.push(ccw[index]);
                    }
                });

                // Hapus SAP yang ditandai manual
                for (let i = sap.length - 1; i >= 0; i--) {
                    if (sap[i]._toDelete) sap.splice(i, 1);
                }

                if (affectedCount === 0) {
                    alert(`Tidak ditemukan Line Number dengan prefix "${prefix}."`);
                    return;
                }

                await storage.setJSON('sap', sap);
                await storage.setJSON('compare', newCompare);
                await storage.setJSON('ccw', newCcw);

                await viewCompareSAPCCW();
                document.getElementById('prefixMassDelete').value = '';

                Swal.fire({
                    title: 'Berhasil!',
                    text: `${affectedCount} baris berhasil dihapus dari data.`,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

        // ================================
        // Mass Delete SO By Line Prefix (Hapus Sales Doc saja)
        // ================================
        async function massDeleteSOByPrefix() {
            const prefix = document.getElementById('prefixMassDelete').value;
            if (!prefix) return alert('Silakan masukkan prefix angka depan (contoh: 1)');

            Swal.fire({
                title: 'Konfirmasi Mass Delete SO',
                text: `Anda yakin ingin me-reset SALES DOC untuk prefix "${prefix}"? (Baris tetap ada)`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset SO!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (!result.value) return;

                const sap = await storage.getJSON('sap', []);
                const compare = await storage.getJSON('compare', []);
                const ccw = await storage.getJSON('ccw', []);

                const regex = new RegExp('^' + prefix + '\\.');
                let affectedCount = 0;

                compare.forEach((item, index) => {
                    if (regex.test(item.lineNumber)) {
                        // 1. Kembalikan SN ke available di CCW jika ada
                        (item.salesDoc || []).forEach(sales => {
                            (sales.serialNumber || []).forEach(sn => {
                                const change = ccw[index].snAvailable.find(s => s
                                    .serialNumber === sn);
                                if (change) change.status = true;
                            });

                            // 2. Reset SAP status
                            const sapIndex = sap.findIndex(s => toInt(s.id) === toInt(sales
                                .id));
                            if (sapIndex !== -1) {
                                if (sap[sapIndex].manual === true) {
                                    sap[sapIndex]._toDelete = true;
                                } else {
                                    sap[sapIndex].select = 0;
                                }
                            }
                        });

                        // 3. Kosongkan Sales Doc di compare record ini
                        if ((item.salesDoc || []).length > 0) {
                            item.salesDoc = [];
                            item.qtyAdd = 0;
                            item.purchaseOrderDetailId = null;
                            affectedCount++;
                        }
                    }
                });

                // Hapus SAP yang ditandai manual
                for (let i = sap.length - 1; i >= 0; i--) {
                    if (sap[i]._toDelete) sap.splice(i, 1);
                }

                if (affectedCount === 0) {
                    alert(`Tidak ditemukan Sales Doc pada Line Number dengan prefix "${prefix}."`);
                    return;
                }

                await storage.setJSON('sap', sap);
                await storage.setJSON('compare', compare);
                await storage.setJSON('ccw', ccw);

                await viewCompareSAPCCW();
                document.getElementById('prefixMassDelete').value = '';

                Swal.fire({
                    title: 'Berhasil!',
                    text: `${affectedCount} baris berhasil dikosongkan Sales Doc-nya.`,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
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

        async function changeTypeProcess(index) {
            const compare = await storage.getJSON('compare', []);
            const item = compare[index];
            const currentType = item.typeManual || (isParentFormat(item.lineNumber) ? 'Parent' : 'Child');
            const targetType = currentType === 'Parent' ? 'Child' : 'Parent';

            Swal.fire({
                title: 'Konfirmasi Perubahan Type',
                text: `Apakah anda yakin ingin mengganti type dari ${currentType} ke ${targetType}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ganti!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary w-xs me-2 mt-2',
                    cancelButton: 'btn btn-danger w-xs mt-2'
                },
                buttonsStyling: false,
                showCloseButton: true
            }).then(async (result) => {
                if (result.value) {
                    item.typeManual = targetType;
                    await storage.setJSON('compare', compare);
                    await viewCompareSAPCCW();
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `Type berhasil diganti menjadi ${targetType}`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
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
                if (item.manual === true) sap.splice(sapIndex, 1);
                else item.select = 0;
            }

            await storage.setJSON('sap', sap);
            await storage.setJSON('compare', compare);
            await storage.setJSON('ccw', ccw);

            await viewCompareSAPCCW();
        }

        // ================================
        // Delete Row (Hapus Baris Excel)
        // ================================
        async function deleteRow(index) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah anda yakin ingin menghapus baris ini? Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-primary w-xs me-2 mt-2',
                    cancelButton: 'btn btn-secondary w-xs mt-2'
                },
                buttonsStyling: false,
                showCloseButton: true
            }).then(async (result) => {
                if (result.value) {
                    const sap = await storage.getJSON('sap', []);
                    const compare = await storage.getJSON('compare', []);
                    const ccw = await storage.getJSON('ccw', []);

                    const item = compare[index];

                    // Reset SAP status yang terhubung dengan baris ini
                    if (item.salesDoc && item.salesDoc.length > 0) {
                        // Kumpulkan ID manual yang perlu dihapus dari SAP
                        const manualIdsToDelete = [];

                        item.salesDoc.forEach(sales => {
                            const sapIndex = sap.findIndex(s => toInt(s.id) === toInt(sales.id));
                            if (sapIndex !== -1) {
                                if (sap[sapIndex].manual === true) {
                                    manualIdsToDelete.push(toInt(sales.id));
                                } else {
                                    sap[sapIndex].select = 0; // Kembalikan ke unselected
                                }
                            }
                        });

                        // Hapus SAP manual
                        if (manualIdsToDelete.length > 0) {
                            for (let i = sap.length - 1; i >= 0; i--) {
                                if (manualIdsToDelete.includes(toInt(sap[i].id))) {
                                    sap.splice(i, 1);
                                }
                            }
                        }
                    }

                    // Hapus dari compare dan ccw
                    compare.splice(index, 1);
                    ccw.splice(index, 1);

                    await storage.setJSON('sap', sap);
                    await storage.setJSON('compare', compare);
                    await storage.setJSON('ccw', ccw);

                    await viewCompareSAPCCW();

                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Baris berhasil dihapus.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
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
            const table = new DataTable('#tableListPoSAP', {
                stateSave: true
            });

            const pageCount = table.page.info().pages;
            if (currentPage < pageCount) {
                table.page(currentPage).draw(false);
            } else if (pageCount > 0) {
                table.page(pageCount - 1).draw(false);
            }
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
                const workbook = XLSX.read(data, {
                    type: 'array'
                });
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                const jsonData = XLSX.utils.sheet_to_json(worksheet, {
                    defval: ''
                });
                const serialNumber = jsonData.map((row) => row['Serial Number']);

                const index = document.getElementById('detailSN_index').value;
                const indexSalesDoc = document.getElementById('detailSN_index_sales_doc').value;
                const compare = await storage.getJSON('compare', []);

                if (serialNumber.length === toInt(compare[index].salesDoc[indexSalesDoc].qty)) {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Jumlah Serial Number tidak boleh lebih dari QTY',
                        icon: 'warning'
                    });
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
                const btn = i.status ?
                    `<a class="btn btn-primary btn-sm" onclick="selectSnAvailable('${i.serialNumber}')">Select SN</a>` :
                    '';
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
                Swal.fire({
                    title: 'Warning!',
                    text: 'Jumlah Serial Number tidak boleh lebih dari QTY',
                    icon: 'warning'
                });
                return true;
            }

            serialNumber.push('');
            compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;
            await storage.setJSON('compare', compare);
            await storage.setJSON('serialNumber', serialNumber);

            await viewSerialNumber(index, indexSalesDoc);
            await viewCompareSAPCCW();
        }

        async function generateNA() {
            const index = document.getElementById('detailSN_index').value;
            const indexSalesDoc = document.getElementById('detailSN_index_sales_doc').value;

            const compare = await storage.getJSON('compare', []);
            let serialNumber = await storage.getJSON('serialNumber', []);

            const targetQty = toInt(compare[index].salesDoc[indexSalesDoc].qty);
            const currentQty = serialNumber.length;

            if (currentQty >= targetQty) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Jumlah Serial Number sudah lengkap',
                    icon: 'warning'
                });
                return;
            }

            const diff = targetQty - currentQty;
            for (let i = 0; i < diff; i++) {
                serialNumber.push('N/A');
            }

            compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;
            await storage.setJSON('compare', compare);
            await storage.setJSON('serialNumber', serialNumber);

            await viewSerialNumber(index, indexSalesDoc);
            await viewCompareSAPCCW();
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
            (data.snAvailable || []).forEach((item) => {
                if (item.serialNumber === sn) item.status = true;
            });
            await storage.setJSON('ccw', ccw);

            await viewSerialNumber(index, indexSalesDoc);
            await viewCompareSAPCCW();
        }

        async function changeSerialNumber(index, indexSalesDoc, indexDetail, value) {
            const compare = await storage.getJSON('compare', []);
            const serialNumber = await storage.getJSON('serialNumber', []);

            serialNumber[indexDetail] = value;
            compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;

            await storage.setJSON('compare', compare);
            await storage.setJSON('serialNumber', serialNumber);

            await viewSerialNumber(index, indexSalesDoc);
            await viewCompareSAPCCW();
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
            $('#salesDocTable').DataTable({
                pageLength: 10
            });
            $('#listSalesDocModal').modal('show');
        }

        async function pilihSalesDocProcess(index, id, indexDetail) {
            const sap = await storage.getJSON('sap', []);
            const compare = await storage.getJSON('compare', []);
            const findSAP = sap.find(i => toInt(i.id) === toInt(id));

            if (toInt(compare[index].qtyAdd + findSAP.qty) > toInt(compare[index].qty)) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'QTY melebihi ketentuan PO',
                    icon: 'warning'
                });
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
                title: 'Are you sure?',
                text: 'Process Quality Control',
                icon: 'warning',
                showCancelButton: true,
                customClass: {
                    confirmButton: 'btn btn-primary w-xs me-2 mt-2',
                    cancelButton: 'btn btn-danger w-xs mt-2'
                },
                confirmButtonText: 'Yes, Process it!',
                buttonsStyling: false,
                showCloseButton: true
            }).then(async function(t) {
                if (!t.value) return;

                // Validation Serial Number & Sales Doc
                const compare = await storage.getJSON('compare', []);
                let totalProcessed = 0;
                for (const item of compare) {
                    if (toInt(item.qty) !== toInt(item.qtyAdd) || (item.salesDoc || []).length === 0)
                        continue;
                    for (const salesDoc of (item.salesDoc || [])) {
                        // Produk harus memiliki Sales Doc (sudah ada dalam array salesDoc)
                        // dan harus memiliki Serial Number yang lengkap sesuai QTY
                        if (!salesDoc.salesDoc) {
                            Swal.fire({
                                title: 'Warning!',
                                text: `Sales Doc belum terpilih untuk ${item.itemName}.`,
                                icon: 'warning'
                            });
                            return true;
                        }

                        if ((salesDoc.serialNumber.length + salesDoc.snDirect.length) !== salesDoc.qty) {
                            Swal.fire({
                                title: 'Warning!',
                                text: `Serial number untuk ${item.itemName} / ${salesDoc.salesDoc} belum lengkap. (Dibutuhkan: ${salesDoc.qty})`,
                                icon: 'warning'
                            });
                            return true;
                        }

                        // Pastikan Serial Number yang kosong diisi dengan N/A
                        salesDoc.serialNumber = (salesDoc.serialNumber || []).map(sn => (!sn || sn
                            .trim() === '') ? 'N/A' : sn);
                        salesDoc.snDirect = (salesDoc.snDirect || []).map(sn => (!sn || sn.trim() === '') ?
                            'N/A' : sn);

                        totalProcessed += salesDoc.qty;
                    }
                }

                if (totalProcessed === 0) {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Belum ada produk yang diproses (Sales Doc & SN wajib diisi).',
                        icon: 'warning'
                    });
                    return true;
                }

                await storage.setJSON('compare', compare);

                // Upload JSON dahulu
                await sendLocalStorageAsJsonFileToBackend({
                    key: 'compare',
                    filename: 'compare.json',
                    uploadUrl: '{{ route('inbound.quality-control.upload.ccw') }}',
                    onSuccess: async (fileName) => {
                        $.ajax({
                            url: '{{ route('inbound.quality-control-process-ccw-store') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                fileName: await storage.getJSON('fileName'),
                                purchaseOrderId: '{{ request()->get('id') }}'
                            },
                            success: (res) => {
                                if (res.status) {
                                    Swal.fire({
                                            title: 'Success!',
                                            text: 'Quality Control successfully!',
                                            icon: 'success',
                                            confirmButtonText: 'OK',
                                            customClass: {
                                                confirmButton: 'btn btn-primary w-xs mt-2'
                                            },
                                            buttonsStyling: false
                                        })
                                        .then(() => {
                                            window.location.href =
                                                '{{ route('inbound.quality-control') }}';
                                        });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Quality Control Failed!',
                                        icon: 'error',
                                        confirmButtonText: 'OK',
                                        customClass: {
                                            confirmButton: 'btn btn-primary w-xs mt-2'
                                        },
                                        buttonsStyling: false
                                    });
                                }
                            }
                        });
                    }
                });
            });
        }

        async function sendLocalStorageAsJsonFileToBackend({
            key = 'compare',
            filename = 'compare.json',
            uploadUrl,
            onSuccess
        }) {
            const rawObj = await storage.getJSON(key, null);
            if (!rawObj) {
                console.error(`❌ Tidak ada data untuk key "${key}"`);
                return;
            }

            const rawData = JSON.stringify(rawObj);
            const blob = new Blob([rawData], {
                type: 'application/json'
            });
            const file = new File([blob], filename, {
                type: 'application/json'
            });

            const formData = new FormData();
            formData.append('json_file', file);
            formData.append('_token', '{{ csrf_token() }}');

            return fetch(uploadUrl, {
                    method: 'POST',
                    body: formData
                })
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
                Swal.fire({
                    title: 'Warning!',
                    text: 'qty serial number exceeds qty product',
                    icon: 'error'
                });
                return true;
            }

            const exists = serialNumber.find((i) => i === value);
            if (exists != null) {
                document.getElementById('scanSerialNumberErrorMessage').innerText =
                    'Serial number is already in the list';
                document.getElementById('scanSerialNumberError').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('scanSerialNumberError').style.display = 'none';
                }, 3000);
                new Audio("{{ asset('assets/sound/error.mp3') }}").play();
                const input = document.getElementById('scanSerialNumber');
                if (input) {
                    input.value = '';
                    input.focus();
                }
                return true;
            }

            serialNumber.push(value);
            compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;

            await storage.setJSON('compare', compare);
            await storage.setJSON('serialNumber', serialNumber);

            new Audio("{{ asset('assets/sound/scan.mp3') }}").play();

            const ccw = await storage.getJSON('ccw', []);
            const data = ccw[index];
            (data.snAvailable || []).forEach((item) => {
                if (item.serialNumber === value) item.status = false;
            });
            await storage.setJSON('ccw', ccw);

            await viewSerialNumber(index, indexSalesDoc);
            const input = document.getElementById('scanSerialNumber');
            if (input) {
                input.value = '';
                input.focus();
            }
            await viewCompareSAPCCW();
        }

        // ================================
        // Scanner Enter Listeners (SN & SN Direct)
        // ================================
        document.getElementById('scanSerialNumber')?.addEventListener('keydown', async function(e) {
            if (e.key !== 'Enter') return;
            e.preventDefault();
            const value = this.value.trim();

            if (!value) {
                document.getElementById('scanSerialNumberErrorMessage').innerText =
                    'Serial number cannot be empty';
                document.getElementById('scanSerialNumberError').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('scanSerialNumberError').style.display = 'none';
                }, 3000);
                new Audio("{{ asset('assets/sound/error.mp3') }}").play();
                this.value = '';
                this.focus();
                return;
            }

            const index = document.getElementById('detailSN_index').value;
            const indexSalesDoc = document.getElementById('detailSN_index_sales_doc').value;
            const compare = await storage.getJSON('compare', []);
            let serialNumber = await storage.getJSON('serialNumber', []);

            if (serialNumber.length === toInt(compare[index].salesDoc[indexSalesDoc].qty)) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'qty serial number exceeds qty product',
                    icon: 'error'
                });
                return true;
            }

            const exists = serialNumber.find((i) => i === value);
            if (exists != null) {
                document.getElementById('scanSerialNumberErrorMessage').innerText =
                    'Serial number is already in the list';
                document.getElementById('scanSerialNumberError').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('scanSerialNumberError').style.display = 'none';
                }, 3000);
                new Audio("{{ asset('assets/sound/error.mp3') }}").play();
                this.value = '';
                this.focus();
                return true;
            }

            serialNumber.push(value);
            compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;

            await storage.setJSON('compare', compare);
            await storage.setJSON('serialNumber', serialNumber);

            new Audio("{{ asset('assets/sound/scan.mp3') }}").play();
            await viewSerialNumber(index, indexSalesDoc);
            this.value = '';
            this.focus();
            await viewCompareSAPCCW();
        });

        document.getElementById('scanSerialNumberDirect')?.addEventListener('keydown', async function(e) {
            if (e.key !== 'Enter') return;
            e.preventDefault();
            const value = this.value.trim();
            if (!value) {
                document.getElementById('scanSerialNumberDirectErrorMessage').innerText =
                    'Serial number cannot be empty';
                document.getElementById('scanSerialNumberDirectError').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('scanSerialNumberDirectError').style.display = 'none';
                }, 3000);
                new Audio("{{ asset('assets/sound/error.mp3') }}").play();
                this.value = '';
                this.focus();
                return;
            }

            const index = document.getElementById('detail_Direct_index').value;
            const indexSalesDoc = document.getElementById('detail_Direct_indexSalesDoc').value;
            const compare = await storage.getJSON('compare', []);
            const serialNumber = compare[index].salesDoc[indexSalesDoc].snDirect;

            const exists = serialNumber.find((i) => i === value);
            if (exists != null) {
                document.getElementById('scanSerialNumberDirectErrorMessage').innerText =
                    'Serial number is already in the list';
                document.getElementById('scanSerialNumberDirectError').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('scanSerialNumberDirectError').style.display = 'none';
                }, 3000);
                new Audio("{{ asset('assets/sound/error.mp3') }}").play();
                this.value = '';
                this.focus();
                return true;
            }

            const checkQTY = serialNumber.length + compare[index].salesDoc[indexSalesDoc].serialNumber.length;
            if (checkQTY === toInt(compare[index].salesDoc[indexSalesDoc].qty)) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'qty serial number exceeds qty product',
                    icon: 'error'
                });
                return true;
            }

            serialNumber.push(value);
            compare[index].salesDoc[indexSalesDoc].qtyDirect = serialNumber.length;

            await storage.setJSON('compare', compare);
            await viewSerialNumberDirectOutbound(index, indexSalesDoc);

            new Audio("{{ asset('assets/sound/scan.mp3') }}").play();
            this.value = '';
            this.focus();
            await viewCompareSAPCCW();
        });

        // ================================
        // Pilih semua SN available
        // ================================
        async function pilihSemuaSN() {
            const ccw = await storage.getJSON('ccw', []);
            const indexCCW = document.getElementById('detailSN_ccw_index').value;
            const list = (ccw[indexCCW]?.snAvailable || []);
            for (const item of list) {
                if (item.status === true) await selectSnAvailable(item.serialNumber);
            }
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
            const id = new Date().toISOString().replace(/[-:TZ.]/g, '').slice(0, 14);

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

        // ================================
        // Draft: Save & Load (Laravel API)
        // ================================
        const PURCHASE_ORDER_ID = '{{ request()->get('id') }}';
        const DRAFT_ROUTE_SAVE = '{{ route('inbound.qc.draft.save') }}'; // POST
        const DRAFT_ROUTE_LOAD = '{{ route('inbound.qc.draft.load') }}'; // GET ?purchaseOrderId=

        // Call this on a button: <button id="saveDraftBtn" onclick="saveDraft()">Save Draft</button>
        async function saveDraft() {
            try {
                const [sap, ccw, compare] = await Promise.all([
                    storage.getJSON('sap', []),
                    storage.getJSON('ccw', []),
                    storage.getJSON('compare', [])
                ]);

                const payload = {
                    purchaseOrderId: PURCHASE_ORDER_ID,
                    sap,
                    ccw,
                    compare
                };

                const res = await fetch(DRAFT_ROUTE_SAVE, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                if (!res.ok) throw new Error(await res.text());
                const out = await res.json().catch(() => ({}));

                // Optional: toast
                if (window.Swal) {
                    Swal.fire({
                        title: 'Draft saved',
                        text: out.message || 'Draft berhasil disimpan',
                        icon: 'success'
                    });
                } else {
                    console.log('Draft saved');
                }
            } catch (err) {
                console.error('Save draft error', err);
                if (window.Swal) Swal.fire({
                    title: 'Error',
                    text: 'Gagal menyimpan draft',
                    icon: 'error'
                });
            }
        }

        // Dipanggil saat initPage: kembalikan true kalau ada draft
        async function loadDraftIfAny() {
            try {
                const url = `${DRAFT_ROUTE_LOAD}?purchaseOrderId=${encodeURIComponent(PURCHASE_ORDER_ID)}`;
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) return false; // 404/empty -> no draft

                const data = await res.json(); // { sap:[], ccw:[], compare:[], updated_at?: string }
                const hasAny = (data && (data.sap?.length || data.ccw?.length || data.compare?.length));
                if (!hasAny) return false;

                if (data.sap) await storage.setJSON('sap', data.sap);
                if (data.ccw) await storage.setJSON('ccw', data.ccw);
                if (data.compare) await storage.setJSON('compare', data.compare);

                // Render UI from draft
                await viewCompareSAPCCW();
                await viewPoSAP();

                return true;
            } catch (err) {
                console.warn('Load draft error or no draft', err);
                return false;
            }
        }

        // ================================
        // Optional: Autosave Draft (every 60s if there is any data)
        // ================================
        // let autosaveTimer = setInterval(async () => {
        //     try {
        //         const [sap, ccw, compare] = await Promise.all([
        //             storage.getJSON('sap', []),
        //             storage.getJSON('ccw', []),
        //             storage.getJSON('compare', [])
        //         ]);
        //         const hasData = (sap?.length || ccw?.length || compare?.length);
        //         if (hasData) {
        //             await saveDraft();
        //         }
        //     } catch (_) {}
        // }, 60000);
    </script>
@endsection
