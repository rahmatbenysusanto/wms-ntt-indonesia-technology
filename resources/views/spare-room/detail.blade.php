@extends('layout.index')
@section('title', 'Detail Spare Room')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Spare Room Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Spare Room</a></li>
                        <li class="breadcrumb-item">List</li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Item</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                            <tr>
                                <th>Box</th>
                                <th class="text-center">Item</th>
                                <th>Sales Doc</th>
                                <th class="text-center">Type</th>
                                <th>Material</th>
                                <th>Po Item Desc</th>
                                <th>Prod Hierarchy Desc</th>
                                <th class="text-center">QTY</th>
                                <th class="text-center">Serial Number</th>
                                <th class="text-center">Barcode</th>
                                <th>Storage</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($product->inventoryPackageItem as $item)
                                <tr>
                                    <td>{{ $loop->iteration == 1 ? $product->reff_number : '' }}</td>
                                    <td>{{ $item->purchaseOrderDetail->item }}</td>
                                    <td>{{ $item->purchaseOrderDetail->sales_doc }}</td>
                                    <td class="text-center">
                                        @if($item->is_parent == 1)
                                            <span class="badge bg-danger-subtle text-danger">Parent</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Child</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->purchaseOrderDetail->material }}</td>
                                    <td>{{ $item->purchaseOrderDetail->po_item_desc }}</td>
                                    <td>{{ $item->purchaseOrderDetail->prod_hierarchy_desc }}</td>
                                    <td class="text-center fw-bold">{{ $item->qty }}</td>
                                    <td class="text-center"><a class="btn btn-info btn-sm" onclick="detailSerialNumber('{{ $item->id }}')">Serial Number</a></td>
                                    <td class="text-center">
                                        @if($loop->iteration == 1)
                                            <a class="btn btn-secondary btn-sm" onclick="showBarcodeModal()">Download Barcode</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($loop->iteration == 1)
                                            Spare Room
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Preview -->
    <div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="barcodeModalLabel">Preview Barcode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">

                    <div class="mb-3">
                        <label for="sizeSelector" class="form-label">Ukuran Barcode:</label>
                        <select id="sizeSelector" class="form-select w-auto d-inline" onchange="updateBarcodePreview()">
                            <option value="small" selected>Small (5.8 x 4 cm)</option>
                            <option value="medium">Medium (7 x 5 cm)</option>
                            <option value="large">Large (8 x 6 cm)</option>
                            <option value="xlarge">Extra Large (10 x 7 cm)</option>
                        </select>
                    </div>

                    <!-- Preview Barcode -->
                    <div id="barcodePreviewArea" class="border p-2 d-inline-block" style="background: #fff;"></div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="printBarcode()">🖨️ Print</button>
                    <button class="btn btn-success" onclick="downloadBarcode()">⬇️ Download PNG</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Default Modals -->
    <div id="detailSNModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Detail Serial Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Serial Number</th>
                        </tr>
                        </thead>
                        <tbody id="listSerialNumber"></tbody>
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
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>

    <script>
        let currentBarcodeId = '';
        let locationText = 'Spare Room';
        let boxName = '';
        let purcDoc = '';
        let salesDoc = '';
        let customer = '';

        function getSize(scale) {
            const cmToPx = cm => cm * 37.8;
            switch (scale) {
                case 'small': return { width: cmToPx(5.8), height: cmToPx(5.5) };
                case 'medium': return { width: cmToPx(7), height: cmToPx(6) };
                case 'large': return { width: cmToPx(8), height: cmToPx(6) };
                case 'xlarge': return { width: cmToPx(10), height: cmToPx(6) };
                default: return { width: cmToPx(7), height: cmToPx(6) };
            }
        }

        function showBarcodeModal(index) {
            currentBarcodeId = '';
            locationText = 'Spare Room';
            boxName = '';
            purcDoc = '';
            salesDoc = '';
            customer = '';

            const dataPackage = @json($product);
            const find = @json($product);

            console.info(find);

            currentBarcodeId = 'hahahahaha';
            purcDoc = find.purchase_order.purc_doc;
            boxName = find.reff_number;
            customer = find.purchase_order.customer.name;

            const salesDocs = JSON.parse(find.sales_docs) ?? [];
            salesDocs.forEach((item) => {
                salesDoc += item+', ';
            });

            updateBarcodePreview();
            new bootstrap.Modal(document.getElementById('barcodeModal')).show();
        }

        function updateBarcodePreview() {
            const area = document.getElementById('barcodePreviewArea');
            area.innerHTML = '';

            const scale = document.getElementById('sizeSelector').value;
            const { width, height } = getSize(scale);

            const container = document.createElement('div');
            container.style.width = `${width}px`;
            container.style.height = `${height}px`;
            container.style.padding = '8px';
            container.style.background = '#fff';
            container.style.textAlign = 'center';
            container.style.fontSize = '12px';
            container.setAttribute('id', 'barcodeFinal');

            const qrWrapper = document.createElement('div');
            qrWrapper.style.display = 'flex';
            qrWrapper.style.justifyContent = 'center';
            qrWrapper.style.alignItems = 'center';

            const qrDiv = document.createElement('div');
            qrDiv.setAttribute('id', 'qrCodeContainer');

            qrWrapper.appendChild(qrDiv);

            const text1 = document.createElement('div');
            text1.innerHTML = '<b>Box:</b> '+boxName;
            text1.style.marginTop = '2px';

            const textPO = document.createElement('div');
            textPO.innerHTML = '<b>PO: </b>'+purcDoc;
            textPO.style.marginTop = '2px';

            const textSO = document.createElement('div');
            textSO.innerHTML = '<b>Sales Doc: </b>'+salesDoc;
            textSO.style.marginTop = '2px';

            const textCustomer = document.createElement('div');
            textCustomer.innerHTML = '<b>Customer: </b>'+customer;
            textCustomer.style.marginTop = '2px';

            const text2 = document.createElement('div');
            text2.innerHTML = '<b>Loc: </b>'+locationText;
            text2.style.marginTop = '2px';

            const text3 = document.createElement('div');
            text3.innerHTML = '<b>Tgl Inb: </b>'+'{{ \Carbon\Carbon::parse($product->created_at)->translatedFormat('d F Y') }}';
            text3.style.marginTop = '2px';
            text3.style.marginBottom = '2px';

            container.appendChild(qrWrapper);
            container.appendChild(text1);
            container.appendChild(textPO);
            container.appendChild(textSO);
            container.appendChild(textCustomer);
            container.appendChild(text2);
            container.appendChild(text3);
            area.appendChild(container);

            // Generate QR Code
            const qrSize = {
                small: 70,
                medium: 100,
                large: 140,
                xlarge: 180
            }[scale] || 100;

            new QRCode(qrDiv, {
                text: currentBarcodeId,
                width: qrSize,
                height: qrSize
            });
        }

        function printBarcode() {
            const content = document.getElementById("barcodeFinal").outerHTML;
            const printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write(`<html><head><title>Print Barcode</title></head><body>${content}</body></html>`);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }

        function downloadBarcode() {
            const element = document.getElementById("barcodeFinal");

            html2canvas(element, {
                scale: 3,
                useCORS: true
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = `${currentBarcodeId}.png`;
                link.href = canvas.toDataURL("image/png");
                link.click();
            });
        }

        function detailSerialNumber(id) {
            $.ajax({
                url: '{{ route('inbound.put-away.find-serial-number-inventory') }}',
                method: 'GET',
                data: {
                    id: id
                },
                success: (res) => {
                    const serialNumber = res.data ?? [];
                    let html = '';

                    serialNumber.forEach((sn) => {
                        html += `
                            <tr><td>${sn.serial_number}</td></tr>
                        `;
                    });

                    document.getElementById('listSerialNumber').innerHTML = html;
                    $('#detailSNModal').modal('show');
                }
            });
        }
    </script>

@endsection
