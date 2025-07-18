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
                                    <th>Serial Number</th>
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
                    </table>

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

                    <input type="hidden" id="detailSN_index">
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
                    select: 0
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

                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];

                const jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: "" });

                const filteredData = jsonData.map((row) => ({
                    lineNumber: row['Line Number'],
                    itemName: row['Item Name'],
                    itemDesc: row['Item Description'],
                    serialNumber: row['Serial Numbers'].split("\r\n").filter(Boolean) ?? [],
                    qty: row['Quantity Ordered'],
                    qtyAdd: 0,
                    salesDoc: [],
                    listSalesDoc: [],
                    purchaseOrderDetailId: null,
                    putAwayStep: 1
                }));

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
                        qty: findSAPQTY[0].qty
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
                (item.salesDoc).forEach((sales) => {
                    htmlSalesDoc += `<p class="mb-0">${sales.salesDoc}</p>`;
                });

                // Check Parent
                let statusParent = isParentFormat(item.lineNumber);
                let putAwayStep = '';
                if (statusParent) {
                    putAwayStep = `
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
                }

                html += `
                    <tr class="${isEmptySalesDoc ? 'table-danger' : ''}">
                        <td>${number}</td>
                        <td>${statusParent ? '<span class="badge bg-danger-subtle text-danger">Parent</span>' : '<span class="badge bg-secondary-subtle text-secondary">Child</span>'}</td>
                        <td>${putAwayStep}</td>
                        <td>${item.lineNumber}</td>
                        <td>${item.itemName}</td>
                        <td>${item.itemDesc}</td>
                        <td><a class="btn btn-secondary btn-sm" onclick="detailSerialNumber(${index})">Detail</a></td>
                        <td class="text-center fw-bold">${item.qty}</td>
                        <td>${htmlSalesDoc}</td>
                        <td>${parseInt(item.qty) !== parseInt(item.qtyAdd) ? `<a class="btn btn-info btn-sm" onclick="pilihSalesDoc('${index}')">Pilih Sales Doc</a> <a class="btn btn-danger btn-sm ms-2" onclick="hapusSalesDoc(${index})">Hapus Sales Doc</a>` : `<a class="btn btn-danger btn-sm" onclick="hapusSalesDoc(${index})">Hapus Sales Doc</a>`} </td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listProducts').innerHTML = html;
            const table = new DataTable('#tableListProduct');
            table.page(currentPage).draw('page');

            viewPoSAP();
        }

        function handlePutAwayStepChange(checkbox, index) {
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];

            compare[index].putAwayStep = checkbox.checked ? 1 : 0;

            localStorage.setItem('compare', JSON.stringify(compare));
            viewCompareSAPCCW();
        }

        function hapusSalesDoc(index) {
            const sap = JSON.parse(localStorage.getItem('sap')) ?? [];
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];

            (compare[index].salesDoc).forEach((item) => {
                const sapFind = sap.find(i => parseInt(i.id) === parseInt(item.id));
                sapFind.select = 0;
            });

            compare[index].qtyAdd = 0;
            compare[index].salesDoc = [];

            localStorage.setItem('sap', JSON.stringify(sap));
            localStorage.setItem('compare', JSON.stringify(compare));

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

        function detailSerialNumber(index) {
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];

            document.getElementById('detailSN_lineNumber').innerText = compare[index].lineNumber;
            document.getElementById('detailSN_itemName').innerText = compare[index].itemName;
            document.getElementById('detailSN_itemDesc').innerText = compare[index].itemDesc;
            document.getElementById('detailSN_qty').innerText = compare[index].qty;
            document.getElementById('detailSN_index').value = index;

            localStorage.setItem('serialNumber', JSON.stringify(compare[index].serialNumber));
            viewSerialNumber(index);

            $('#detailSerialNumberModal').modal('show');
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
                const compare = JSON.parse(localStorage.getItem('compare')) ?? [];

                if (serialNumber.length === parseInt(compare[index].qty)) {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Jumlah Serial Number tidak boleh lebih dari QTY',
                        icon: 'warning'
                    });
                    return true;
                }

                compare[index].serialNumber = serialNumber;

                localStorage.setItem('compare', JSON.stringify(compare));
                localStorage.setItem('serialNumber', JSON.stringify(serialNumber));
                fileInput.value = "";

                viewSerialNumber(index);
            };

            reader.readAsArrayBuffer(file);
        }

        function viewSerialNumber(index) {
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];
            let html = '';
            let number = 1;

            (serialNumber).forEach((item, indexDetail) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td><input type="text" class="form-control" value="${item}" onchange="changeSerialNumber(${index}, ${indexDetail}, this.value)"></td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteSerialNumber(${index}, ${indexDetail})">Delete</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listSerialNumber').innerHTML = html;
        }

        function addManualSN() {
            const index = document.getElementById('detailSN_index').value;
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            let serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            if (serialNumber.length === parseInt(compare[index].qty)) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Jumlah Serial Number tidak boleh lebih dari QTY',
                    icon: 'warning'
                });
                return true;
            }

            serialNumber.push("");
            compare[index].serialNumber = serialNumber;

            localStorage.setItem('compare', JSON.stringify(compare));
            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));

            viewSerialNumber(index);
        }

        function deleteSerialNumber(index, indexDetail) {
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            serialNumber.splice(indexDetail, 1);

            compare[index].serialNumber = serialNumber;

            localStorage.setItem('compare', JSON.stringify(compare));
            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));

            viewSerialNumber(index);
        }

        function changeSerialNumber(index, indexDetail, value) {
            const compare = JSON.parse(localStorage.getItem('compare')) ?? [];
            const serialNumber = JSON.parse(localStorage.getItem('serialNumber')) ?? [];

            serialNumber[indexDetail] = value;

            compare[index].serialNumber = serialNumber;

            localStorage.setItem('compare', JSON.stringify(compare));
            localStorage.setItem('serialNumber', JSON.stringify(serialNumber));

            viewSerialNumber(index);
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

            console.log(sapFilter);

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
                qty: findSAP.qty
            });
            compare[index].qtyAdd += findSAP.qty;
            findSAP.select = 1;

            localStorage.setItem('sap', JSON.stringify(sap));
            localStorage.setItem('compare', JSON.stringify(compare));

            if (parseInt(compare[index].qtyAdd) === parseInt(compare[index].qty)) {
                $('#listSalesDocModal').modal('hide');
            }

            document.getElementById(`btn-sales-doc-${index}-${id}-${indexDetail}`).style.display = 'none';
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
                                    fileName: fileName,
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

                    localStorage.setItem('fileName', filename);

                    if (typeof onSuccess === 'function') {
                        onSuccess(filename);
                    }
                })
                .catch(error => {
                    console.error('❌ Upload gagal:', error);
                });
        }

    </script>
@endsection
























