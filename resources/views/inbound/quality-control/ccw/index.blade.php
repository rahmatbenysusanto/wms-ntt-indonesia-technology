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

    <script>
        localStorage.clear();

        loadProductPurchaseOrder();
        viewPoSAP();
        function loadProductPurchaseOrder() {
            const data = @json($purcDocDetail);
            const products = [];

            data.forEach((item) => {
                products.push({
                    id: item.id,
                    salesDoc: item.sales_doc,
                    item: item.item,
                    material: item.material,
                    poItemDesc: item.po_item_desc,
                    prodHierarchyDesc: item.prod_hierarchy_desc,
                    qty: item.po_item_qty,
                    select: 0,
                    manual:false
                });
            });

            localStorage.setItem('sap', JSON.stringify(products));
        }

        function uploadFileCCW() {
            const fileInput = document.getElementById('fileUpload');
            const file = fileInput.files[0];

            if (!file) {
                alert("Silakan pilih file Excel terlebih dahulu.");
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });

                const firstSheetName = workbook.SheetNames[1];
                const worksheet = workbook.Sheets[firstSheetName];

                const jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: "" });

                const filteredData = jsonData.map((row) => ({
                    lineNumber: row['Line Number'],
                    itemName: row['Item Name'].replace(/\./g, ""),
                    itemDesc: row['Item Description'],
                    serialNumber: row['Serial Numbers'].split("\r\n").filter(Boolean) ?? [],
                    qty: parseInt(row['Quantity Ordered']),
                    qtyAdd: 0,
                    salesDoc: [],
                    listSalesDoc: [],
                    purchaseOrderDetailId: null,
                    putAwayStep: 1,
                    snAvailable: []
                }));

                filteredData.forEach((data) => {
                    data.serialNumber.forEach((sn) => {
                        data.snAvailable.push({
                            serialNumber: sn,
                            status: true
                        })
                    });
                });

                localStorage.setItem('ccw', JSON.stringify(filteredData));
                compareSAPCCW();
            };

            reader.readAsArrayBuffer(file);
        }

        function compareSAPCCW() {
            const sap = JSON.parse(localStorage.getItem('sap')) ?? [];
            const ccw = JSON.parse(localStorage.getItem('ccw')) ?? [];

            ccw.forEach((item) => {
                const findSAP = sap.filter(i => i.material === item.itemName);
                const findSAPQTY = findSAP.filter(i => parseInt(i.qty) === parseInt(item.qty));

                if (findSAPQTY.length === 1) {
                    item.salesDoc.push({
                        id: findSAPQTY[0].id,
                        salesDoc: findSAPQTY[0].salesDoc,
                        qty: findSAPQTY[0].qty,
                        serialNumber: item.serialNumber,
                        qtyDirect: 0,
                        snDirect: []
                    });
                    item.qtyAdd = findSAPQTY[0].qty;
                    findSAPQTY[0].select = 1;
                    item.purchaseOrderDetailId = findSAPQTY[0].id;
                } else {
                    item.listSalesDoc = findSAP;
                }
            });

            localStorage.setItem('compare', JSON.stringify(ccw));
            localStorage.setItem('sap', JSON.stringify(sap));
            viewCompareSAPCCW();
        }

        function isParentFormat(value) {
            const str = String(value);
            const parts = str.split('.');
            return parts.length === 2 && parts[1] === '0';
        }

        function viewCompareSAPCCW() {
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            let html = '';
            let number = 1;

            let currentPage = 0;
            if ($.fn.DataTable.isDataTable('#tableListProduct')) {
                currentPage = $('#tableListProduct').DataTable().page();
                $('#tableListProduct').DataTable().destroy();
            }

            compare.forEach((item, index) => {
                const isEmptySalesDoc = parseInt(item.qty) !== parseInt(item.qtyAdd);

                // View List Sales Doc
                let htmlSalesDoc = '';
                (item.salesDoc).forEach((sales, indexSalesDoc) => {
                    htmlSalesDoc += `
                        <div class="d-flex gap-2 align-items-center">
                            <p class="mb-0" style="min-width: 140px;">${sales.salesDoc} <b>(QTY : ${sales.qty})</b></p>
                            ${item.putAwayStep === 0 ? `<a class="btn btn-dark btn-sm" onclick="directOutboundSerialNumber(${index}, ${indexSalesDoc})">SN Direct Outbound</a>` : ''}
                            <a class="btn ${(parseInt(sales.serialNumber.length) + parseInt(sales.snDirect.length ?? [])) === parseInt(sales.qty) ? 'btn-success' : 'btn-secondary'} btn-sm" onclick="detailSerialNumber(${index}, ${indexSalesDoc})">Serial Number</a>
                            <a class="btn btn-danger btn-sm" onclick="hapusSalesDoc(${index}, ${indexSalesDoc}, ${sales.id})">Hapus</a>
                        </div>
                    `;
                });

                // Check Parent
                let statusParent = isParentFormat(item.lineNumber);
                let putAwayStep = `
                        <div class="form-check form-switch form-switch-md">
                          <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            ${parseInt(item.putAwayStep) === 1 ? 'checked' : ''}
                            onchange="handlePutAwayStepChange(this, ${index})"
                          >
                        </div>
                    `;

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
                        <td>
                            ${(parseInt(item.qty) === parseInt(item.qtyAdd) ? `` : `<div class="d-flex gap-2"><a class="btn btn-info btn-sm" onclick="pilihSalesDoc('${index}')">Pilih Sales Doc</a> <a class="btn btn-warning btn-sm" onclick="manualSalesDoc('${index}')">Manual Sales Doc</a></div>`)}
                        </td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listProducts').innerHTML = html;
            const table = new DataTable('#tableListProduct');
            table.page(currentPage).draw('page');

            viewPoSAP();
        }

        function directOutboundSerialNumber(index, indexSalesDoc) {
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            const product = compare[index];

            document.getElementById('detail_Direct_material').innerText = product.itemName;
            document.getElementById('detail_Direct_desc').innerText = product.itemDesc;
            document.getElementById('detail_Direct_qty').innerText = product.qty;
            document.getElementById('detail_Direct_index').value = index;
            document.getElementById('detail_Direct_indexSalesDoc').value = indexSalesDoc;

            viewSerialNumberDirectOutbound(index, indexSalesDoc);

            $('#serialNumberDirectOutboundModal').modal('show');

            setTimeout(() => {
                document.getElementById('scanSerialNumberDirect').focus();
            }, 500);
        }

        function viewSerialNumberDirectOutbound(index, indexSalesDoc) {
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            const serialNumber = compare[index].salesDoc[indexSalesDoc].snDirect;
            let html = '';

            serialNumber.forEach((item, indexSN) => {
                html += `
                    <tr>
                        <td>${indexSN + 1}</td>
                        <td><input type="text" class="form-control" value="${item}" onchange="changeSNDirect(${indexSN}, this.value)" placeholder="N/A"></td>
                        <td><a class="btn btn-danger btn-sm">Delete</a></td>
                    </tr>
                `;
            });

            document.getElementById('detail_Direct_qty_scan_serial_number').innerText = serialNumber.length;
            document.getElementById('listDetailSerialNumberDirect').innerHTML = html;
        }

        function addSerialNumberManualDirect() {
            const index = document.getElementById('detail_Direct_index').value;
            const indexSalesDoc = document.getElementById('detail_Direct_indexSalesDoc').value;
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            const serialNumber = compare[index].salesDoc[indexSalesDoc].snDirect;

            if ((compare[index].salesDoc[indexSalesDoc].snDirect.length + compare[index].salesDoc[indexSalesDoc].serialNumber.length) === compare[index].salesDoc[indexSalesDoc].qty) {
                document.getElementById('scanSerialNumberDirectErrorMessage').innerText = "Serial number exceeds item quantity";
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

            serialNumber.push("");
            compare[index].salesDoc[indexSalesDoc].qtyDirect = serialNumber.length;

            localStorage.setItem('compare', JSON.stringify(compare));
            viewSerialNumberDirectOutbound(index, indexSalesDoc);
        }

        function changeSNDirect(indexSN, value) {
            const index = document.getElementById('detail_Direct_index').value;
            const indexSalesDoc = document.getElementById('detail_Direct_indexSalesDoc').value;
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            const serialNumber = compare[index].salesDoc[indexSalesDoc].snDirect;

            serialNumber[indexSN] = value;
            compare[index].salesDoc[indexSalesDoc].qtyDirect = serialNumber.length;

            localStorage.setItem('compare', JSON.stringify(compare));
            viewSerialNumberDirectOutbound(index, indexSalesDoc);
        }

        function handlePutAwayStepChange(checkbox, index) {
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];

            compare[index].putAwayStep = checkbox.checked ? 1 : 0;

            localStorage.setItem('compare', JSON.stringify(compare));
            viewCompareSAPCCW();
        }

        function hapusSalesDoc(index, indexSalesDoc, idPoDetail) {
            const sap = JSON.parse(localStorage.getItem('sap')) ?? [];
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            const ccw = JSON.parse(localStorage.getItem('ccw')) ?? [];

            // Kembalikan Serial Number
            const serialNumber = compare[index].salesDoc[indexSalesDoc].serialNumber;
            serialNumber.forEach((item) => {
                const change = ccw[index].snAvailable.find((sn) =>sn.serialNumber === item);
                change.status = true;
            });

            // Hapus Sales Doc didata CCW
            const findCcwData = compare[index].salesDoc[indexSalesDoc];
            compare[index].salesDoc.splice(indexSalesDoc, 1);
            compare[index].qtyAdd = parseInt(compare[index].qtyAdd) - parseInt(findCcwData.qty);

            // Kembalikan QTY data SAP
            const sapFind = sap.find(i => parseInt(i.id) === parseInt(idPoDetail));
            sapFind.select = 0;

            // Temukan item SAP berdasarkan idPoDetail
            const sapIndex = sap.findIndex(i => parseInt(i.id) === parseInt(idPoDetail));
            if (sapIndex !== -1) {
                const item = sap[sapIndex];

                if (item.manual === true) {
                    // Jika manual, hapus dari SAP
                    sap.splice(sapIndex, 1);
                } else {
                    // Jika bukan manual, cukup reset select ke 0
                    item.select = 0;
                }
            }

            localStorage.setItem('sap', JSON.stringify(sap));
            localStorage.setItem('compare', JSON.stringify(compare));
            localStorage.setItem('ccw', JSON.stringify(ccw));

            viewCompareSAPCCW();
        }

        function viewPoSAP() {
            const sap = JSON.parse(localStorage.getItem('sap')) ?? [];
            const sapFilter = sap.filter(i => parseInt(i.select) === 0);

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
                    </tr>
                `;

                number++;
            });

            document.getElementById('listPoSAP').innerHTML = html;
            const table = new DataTable('#tableListPoSAP');
            table.page(currentPage).draw('page');
        }

        function detailSerialNumber(index, indexSalesDoc) {
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            const salesDoc = compare[index].salesDoc[indexSalesDoc];

            document.getElementById('detailSN_lineNumber').innerText = compare[index].lineNumber;
            document.getElementById('detailSN_itemName').innerText = compare[index].itemName;
            document.getElementById('detailSN_itemDesc').innerText = compare[index].itemDesc;
            document.getElementById('detailSN_qty').innerText = compare[index].salesDoc[indexSalesDoc].qty;
            document.getElementById('detailSN_sales_doc').innerText = compare[index].salesDoc[indexSalesDoc].salesDoc;
            document.getElementById('detailSN_index').value = index;
            document.getElementById('detailSN_index_sales_doc').value = indexSalesDoc;

            localStorage.setItem('serialNumber', JSON.stringify(salesDoc.serialNumber));
            viewSerialNumber(index, indexSalesDoc);

            $('#detailSerialNumberModal').modal('show');

            setTimeout(() => {
                document.getElementById('scanSerialNumber').focus();
            }, 500);
        }

        function uploadSerialNumber() {
            const fileInput = document.getElementById('uploadFileSerialNumber');
            const file = fileInput.files[0];

            if (!file) {
                alert("Silakan pilih file Excel terlebih dahulu.");
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });

                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];

                const jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: "" });

                const serialNumber = jsonData.map((row) => ({
                    serialNumber: row['Serial Number']
                }));

                const index = document.getElementById('detailSN_index').value;
                const indexSalesDoc = document.getElementById('detailSN_index_sales_doc').value;
                const compare = JSON.parse(localStorage.getItem('compare')) ?? [];

                if (serialNumber.length === parseInt(compare[index].salesDoc[indexSalesDoc].qty)) {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Jumlah Serial Number tidak boleh lebih dari QTY',
                        icon: 'warning'
                    });
                    return true;
                }

                compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;

                localStorage.setItem('compare', JSON.stringify(compare));
                localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
                fileInput.value = "";

                viewSerialNumber(index, indexSalesDoc);
            };

            reader.readAsArrayBuffer(file);
        }

        function viewSerialNumber(index, indexSalesDoc) {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];
            let html = '';
            let number = 1;

            (serialNumber).forEach((item, indexDetail) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td><input type="text" class="form-control" value="${item}" onchange="changeSerialNumber(${index}, ${indexSalesDoc}, ${indexDetail}, this.value)" placeholder="N/A"></td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteSerialNumber(${index}, ${indexSalesDoc}, ${indexDetail}, '${item}')">Delete</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('detailSN_qty_scan_serial_number').innerText = serialNumber.length;
            document.getElementById('listSerialNumber').innerHTML = html;

            // View SN Available
            const ccw = JSON.parse(localStorage.getItem('ccw'));
            const data = ccw[index];

            let htmlSN = '';
            (data.snAvailable).forEach((item) => {
                let button = '';
                if (item.status === true) {
                    button = `<a class="btn btn-primary btn-sm" onclick="selectSnAvailable('${item.serialNumber}')">Select SN</a>`;
                }

                htmlSN += `
                    <tr>
                        <td>${item.serialNumber}</td>
                        <td>${button}</td>
                    </tr>
                `;
            });

            document.getElementById('detailSN_ccw_index').value = index;
            document.getElementById('listSerialNumberAvailableCCW').innerHTML = htmlSN;
        }

        function addManualSN() {
            const index = document.getElementById('detailSN_index').value;
            const indexSalesDoc = document.getElementById('detailSN_index_sales_doc').value;

            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            let serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            if (serialNumber.length === parseInt(compare[index].salesDoc[indexSalesDoc].qty)) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Jumlah Serial Number tidak boleh lebih dari QTY',
                    icon: 'warning'
                });
                return true;
            }

            serialNumber.push("");
            compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;

            localStorage.setItem('compare', JSON.stringify(compare));
            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));

            viewSerialNumber(index, indexSalesDoc);
        }

        function deleteSerialNumber(index, indexSalesDoc, indexDetail, sn) {
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            serialNumber.splice(indexDetail, 1);

            compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;

            localStorage.setItem('compare', JSON.stringify(compare));
            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));

            const ccw = JSON.parse(localStorage.getItem('ccw'));
            const data = ccw[index];

            (data.snAvailable).forEach((item) => {
                if (item.serialNumber === sn) {
                    item.status = true;
                }
            });

            localStorage.setItem('ccw', JSON.stringify(ccw));

            viewSerialNumber(index, indexSalesDoc);
        }

        function changeSerialNumber(index, indexSalesDoc, indexDetail, value) {
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            serialNumber[indexDetail] = value;

            compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;

            localStorage.setItem('compare', JSON.stringify(compare));
            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));

            viewSerialNumber(index, indexSalesDoc);
        }

        function pilihSalesDoc(index) {
            const sap = JSON.parse(localStorage.getItem('sap')) ?? [];
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            const compareFind = compare[index];

            const sapFilter = sap.filter(item => {
                return (
                    item.material === compareFind.itemName && item.select === 0
                );
            });

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
                    </tr>
                `;
                number++;
            });

            if ($.fn.DataTable.isDataTable('#salesDocTable')) {
                $('#salesDocTable').DataTable().destroy();
            }

            document.getElementById('listSalesDoc').innerHTML = html;

            // Re-inisialisasi DataTable
            $('#salesDocTable').DataTable({
                pageLength: 10,
            });

            // Tampilkan modal
            $('#listSalesDocModal').modal('show');
        }

        function pilihSalesDocProcess(index, id, indexDetail) {
            const sap = JSON.parse(localStorage.getItem('sap')) ?? [];
            const findSAP = sap.find(i => parseInt(i.id) === parseInt(id));
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];

            if (parseInt(compare[index].qtyAdd + findSAP.qty) > parseInt(compare[index].qty)) {
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

            localStorage.setItem('sap', JSON.stringify(sap));
            localStorage.setItem('compare', JSON.stringify(compare));

            if (parseInt(compare[index].qtyAdd) === parseInt(compare[index].qty)) {
                $('#listSalesDocModal').modal('hide');
            }

            if (indexDetail !== null) {
                document.getElementById(`btn-sales-doc-${index}-${id}-${indexDetail}`).style.display = 'none';
            }
            viewCompareSAPCCW();
        }

        function processQualityControl() {
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

                    // Validation Serial Number n/a
                    const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
                    compare.forEach((item) => {
                        item.salesDoc.forEach((salesDoc) => {
                            if ((salesDoc.serialNumber.length + salesDoc.snDirect.length) !== salesDoc.qty) {
                                const sisaQTY = (salesDoc.serialNumber.length + salesDoc.snDirect.length) - salesDoc.qty;
                                for (let i = 0; i < sisaQTY; i++) {
                                    salesDoc.serialNumber.push('N/A');
                                }
                            }
                        });

                        if (parseInt(item.qty) !== parseInt(item.qtyAdd)) {
                            Swal.fire({
                               title: 'Warning!',
                               text: 'There are unprocessed CCW items',
                               icon: 'warning',
                            });

                            return true;
                        }
                    });
                    localStorage.setItem('compare', JSON.stringify(compare));

                    // Kirim File JSON ke Backend Terlebih dahulu
                    sendLocalStorageAsJsonFileToBackend({
                        key: 'compare',
                        filename: 'compare.json',
                        uploadUrl: '{{ route('inbound.quality-control.upload.ccw') }}',
                        onSuccess: (fileName) => {
                            // Baru jalankan AJAX setelah upload selesai

                            $.ajax({
                                url: '{{ route('inbound.quality-control-process-ccw-store') }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    fileName: JSON.parse(localStorage.getItem('fileName')),
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
            });
        }

        function sendLocalStorageAsJsonFileToBackend({ key = 'compare', filename = 'compare.json', uploadUrl, onSuccess }) {
            const rawData = localStorage.getItem(key);
            if (!rawData) {
                console.error(`❌ Tidak ada data di localStorage untuk key "${key}"`);
                return;
            }

            const blob = new Blob([rawData], { type: 'application/json' });
            const file = new File([blob], filename, { type: 'application/json' });

            const formData = new FormData();
            formData.append('json_file', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch(uploadUrl, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(result => {
                    console.log('✅ Upload selesai:', result);

                    localStorage.setItem('fileName', JSON.stringify(result.fileName));

                    if (typeof onSuccess === 'function') {
                        onSuccess(filename);
                    }
                })
                .catch(error => {
                    console.error('❌ Upload gagal:', error);
                });
        }

        function selectSnAvailable(value) {
            const index = document.getElementById('detailSN_index').value;
            const indexSalesDoc = document.getElementById('detailSN_index_sales_doc').value;

            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            let serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            if (serialNumber.length === parseInt(compare[index].salesDoc[indexSalesDoc].qty)) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'qty serial number exceeds qty product',
                    icon: 'error'
                });
                return true;
            }

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
            compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;

            localStorage.setItem('compare', JSON.stringify(compare));
            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));

            const sound = new Audio("{{ asset('assets/sound/scan.mp3') }}");
            sound.play();

            const ccw = JSON.parse(localStorage.getItem('ccw'));
            const data = ccw[index];

            (data.snAvailable).forEach((item) => {
                if (item.serialNumber === value) {
                    item.status = false;
                }
            });

            localStorage.setItem('ccw', JSON.stringify(ccw));

            viewSerialNumber(index, indexSalesDoc);
            document.getElementById('scanSerialNumber').value = "";
            document.getElementById('scanSerialNumber').focus();
            viewCompareSAPCCW();
        }

        document.getElementById('scanSerialNumber').addEventListener('keydown', function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                const value = this.value.trim();
                if (value !== "") {
                    const index = document.getElementById('detailSN_index').value;
                    const indexSalesDoc = document.getElementById('detailSN_index_sales_doc').value;

                    const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
                    let serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

                    if (serialNumber.length === parseInt(compare[index].salesDoc[indexSalesDoc].qty)) {
                        Swal.fire({
                            title: 'Warning!',
                            text: 'qty serial number exceeds qty product',
                            icon: 'error'
                        });
                        return true;
                    }

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
                    compare[index].salesDoc[indexSalesDoc].serialNumber = serialNumber;

                    localStorage.setItem('compare', JSON.stringify(compare));
                    localStorage.setItem('serialNumber', JSON.stringify(serialNumber));

                    const sound = new Audio("{{ asset('assets/sound/scan.mp3') }}");
                    sound.play();

                    viewSerialNumber(index, indexSalesDoc);
                    document.getElementById('scanSerialNumber').value = "";
                    document.getElementById('scanSerialNumber').focus();
                    viewCompareSAPCCW();
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
                    const indexSalesDoc = document.getElementById('detail_Direct_indexSalesDoc').value;
                    const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
                    const serialNumber = compare[index].salesDoc[indexSalesDoc].snDirect;

                    const checkSN = serialNumber.find((item) => item === value);
                    if (checkSN != null) {
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

                    const checkQTY = compare[index].salesDoc[indexSalesDoc].snDirect.length + compare[index].salesDoc[indexSalesDoc].serialNumber.length;
                    if (checkQTY === parseInt(compare[index].salesDoc[indexSalesDoc].qty)) {
                        Swal.fire({
                            title: 'Warning!',
                            text: 'qty serial number exceeds qty product',
                            icon: 'error'
                        });

                        return true;
                    }

                    serialNumber.push(value);
                    compare[index].salesDoc[indexSalesDoc].qtyDirect = serialNumber.length;

                    localStorage.setItem('compare', JSON.stringify(compare));
                    viewSerialNumberDirectOutbound(index, indexSalesDoc);

                    const sound = new Audio("{{ asset('assets/sound/scan.mp3') }}");
                    sound.play();

                    document.getElementById('scanSerialNumberDirect').value = "";
                    document.getElementById('scanSerialNumberDirect').focus();
                    viewCompareSAPCCW();
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

        function pilihSemuaSN() {
            const ccw = JSON.parse(localStorage.getItem('ccw')) ?? [];
            const indexCCW = document.getElementById('detailSN_ccw_index').value;

            const serialNumber = ccw[indexCCW].snAvailable;
            serialNumber.forEach((item) => {
                if (item.status === true) {
                    selectSnAvailable(item.serialNumber);
                }
            });
        }

        // Manual Sales Doc
        function manualSalesDoc(index) {
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            const findCompare = compare[index];
            localStorage.setItem('salesDocManual', JSON.stringify(findCompare));

            document.getElementById('salesDocManual_line_number').innerText = findCompare.lineNumber;
            document.getElementById('salesDocManual_item_name').innerText = findCompare.itemName;
            document.getElementById('salesDocManual_item_desc').innerText = findCompare.itemDesc;
            document.getElementById('salesDocManual_qty').innerText = findCompare.qty;
            document.getElementById('salesDocManual_compareIndex').value = index;

            $('#manualSalesDocModal').modal('show');
        }

        function addSalesDocManualProcess() {
            const sap = JSON.parse(localStorage.getItem('sap')) ?? [];
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            const salesDocManual = JSON.parse(localStorage.getItem('salesDocManual')) ?? [];

            // Tambahkan data ke PO SAP
            const id = new Date().toISOString().replace(/[-:TZ.]/g, '').slice(0,14);

            sap.push({
                id: id,
                item: '',
                material: salesDocManual.itemName,
                poItemDesc: salesDocManual.itemDesc,
                prodHierarchyDesc: '',
                qty: parseInt(document.getElementById('salesDocManualQTY').value),
                salesDoc: document.getElementById('salesDocManual').value,
                select: 1,
                manual: true
            });
            localStorage.setItem('sap', JSON.stringify(sap));

            // Pilih Sales Doc
            const index = parseInt(document.getElementById('salesDocManual_compareIndex').value);
            pilihSalesDocProcess(index, id, null);

            document.getElementById('salesDocManualQTY').value = '';
            document.getElementById('salesDocManual').value = '';
            $('#manualSalesDocModal').modal('hide');
        }
    </script>
@endsection
























