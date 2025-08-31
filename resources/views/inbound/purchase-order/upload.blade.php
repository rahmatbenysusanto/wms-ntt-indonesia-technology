@extends('layout.index')
@section('title', 'Purchase Order')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Upload PO</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item">Purchase Order</li>
                        <li class="breadcrumb-item active">Upload</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Dropzone</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Upload file excel hasil dari SAP</p>
                    <div class="mb-3">
                        <input type="file" class="form-control" id="excelFile">
                    </div>
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-info" id="uploadBtn">Upload File</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Data Upload</h4>
                        <a class="btn btn-primary" onclick="processImport()">Import Purchase Order</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th>Item</th>
                                    <th>Material</th>
                                    <th>PO Item Desc</th>
                                    <th>Prod Hierarchy Desc</th>
                                    <th>Acc Ass Cat</th>
                                    <th>Vendor name</th>
                                    <th>Customer name</th>
                                    <th>Stor Loc</th>
                                    <th>SLoc Desc</th>
                                    <th>Valuation</th>
                                    <th>PO Itm Qty</th>
                                    <th>Net Order Price</th>
                                    <th>Currency</th>
                                </tr>
                            </thead>
                            <tbody id="dataUploadFile">

                            </tbody>
                        </table>
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

        document.getElementById('uploadBtn').addEventListener('click', function () {
            const fileInput = document.getElementById('excelFile');
            const file = fileInput.files[0];

            if (!file) {
                alert("Silakan pilih file Excel terlebih dahulu.");
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });

                // Ambil sheet pertama
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];

                // Ubah ke JSON
                const jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: "" });

                const filteredData = jsonData.map((row) => ({
                    purc_doc: row["Pur. Doc."],
                    sales_doc: row["Sales Doc"],
                    item: row["Item"],
                    material: row["Material"] = row["Material"].replace(/\./g, ""),
                    po_item_desc: row["PO Item Desc"],
                    prod_hierarchy_desc: row["Prod Hierarchy Desc"],
                    acc_ass_cat: row["Acc Ass Cat"],
                    vendor_name: row["Vendor name"],
                    customer_name: row["Customer name"],
                    stor_loc: row["Stor Loc"],
                    sloc_desc: row["SLoc Desc"],
                    valuation: row["Valuation"],
                    po_item_qty: parseInt(row["PO Itm Qty"]),
                    net_order_price: row["Net Price"] ?? 0,
                    currency: row["Crcy"] ?? "",
                }));

                viewListData(filteredData);

                localStorage.setItem('purchaseOrder', JSON.stringify(filteredData));
            };

            reader.readAsArrayBuffer(file);
        });

        function viewListData(filteredData) {
            const tbody = document.getElementById("dataUploadFile");
            let html = "";
            let number = 1;

            filteredData.forEach(row => {
                html += `
                    <tr>
                        <td>${number++}</td>
                        <td>${row.purc_doc}</td>
                        <td>${row.sales_doc}</td>
                        <td>${row.item}</td>
                        <td>${row.material}</td>
                        <td>${row.po_item_desc}</td>
                        <td>${row.prod_hierarchy_desc}</td>
                        <td>${row.acc_ass_cat}</td>
                        <td>${row.vendor_name}</td>
                        <td>${row.customer_name}</td>
                        <td>${row.stor_loc}</td>
                        <td>${row.sloc_desc}</td>
                        <td>${row.valuation}</td>
                        <td>${row.po_item_qty}</td>
                        <td>${row.net_order_price}</td>
                        <td>${row.currency}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }
    </script>

    <script>
        function processImport() {
            Swal.fire({
                title: "Are you sure?",
                text: "Import Purchase Order",
                icon: "warning",
                showCancelButton: true,
                customClass: {
                    confirmButton: "btn btn-primary w-xs me-2 mt-2",
                    cancelButton: "btn btn-danger w-xs mt-2"
                },
                confirmButtonText: "Yes, Import it!",
                buttonsStyling: false,
                showCloseButton: true
            }).then(function(t) {
                if (t.value) {
                    const allData = JSON.parse(localStorage.getItem('purchaseOrder')) ?? [];
                    const batchSize = 100;
                    const totalBatches = Math.ceil(allData.length / batchSize);
                    let currentBatch = 0;

                    async function sendNextBatch() {
                        if (currentBatch >= totalBatches) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Import Purchase Order successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: "btn btn-primary w-xs mt-2"
                                },
                                buttonsStyling: false
                            }).then(() => {
                                window.location.href = '{{ route('inbound.purchase-order') }}';
                            });
                            return;
                        }

                        const batchData = allData.slice(currentBatch * batchSize, (currentBatch + 1) * batchSize);

                        Swal.fire({
                            title: `Processing batch ${currentBatch + 1} of ${totalBatches}`,
                            html: 'Please wait...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        try {
                            await $.ajax({
                                url: '{{ route('inbound.purchase-order-upload-process') }}',
                                method: 'POST',
                                contentType: 'application/json',
                                data: JSON.stringify({
                                    _token: '{{ csrf_token() }}',
                                    purchaseOrder: batchData
                                })
                            });

                            currentBatch++;
                            await sendNextBatch();

                        } catch (error) {
                            Swal.fire({
                                title: 'Error!',
                                text: `Batch ${currentBatch + 1} failed to import. Please try again.`,
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: "btn btn-danger w-xs mt-2"
                                },
                                buttonsStyling: false
                            });
                        }
                    }

                    sendNextBatch();
                }
            });
        }
    </script>

@endsection
