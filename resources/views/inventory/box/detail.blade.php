@extends('layout.index')
@section('title', 'Detail Box')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Inventory Box Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Box Detail</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Item</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('inventory.change.new-box', ['packageId' => $products[0]->id]) }}" class="btn btn-primary btn-sm">Change to new box</a>
                            <a href="{{ route('inventory.change.box', ['packageId' => $products[0]->id]) }}" class="btn btn-info btn-sm">Change to other box</a>
                        </div>
                    </div>
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
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $index => $product)
                                @foreach($product->inventoryPackageItem as $item)
                                    @if($item->qty != 0)
                                        @if(!in_array($product->storage->id, [1,2,3,4]))
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
                                                        <a class="btn btn-secondary btn-sm" onclick="showBarcodeModal('{{ $index }}')">Download Barcode</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($loop->iteration == 1)
                                                        {{ $product->storage->raw }} - {{ $product->storage->area }} - {{ $product->storage->rak }} - {{ $product->storage->bin }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a class="btn btn-secondary btn-sm" onclick="changeType('{{ $item->id }}', '{{ $item->is_parent }}')">Change Type</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
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
                            <option value="small" selected>Small (7 x 47 cm)</option>
                            <option value="medium">Medium (8 x 8 cm)</option>
                            <option value="large">Large (9 x 9 cm)</option>
                            <option value="xlarge">Extra Large (10 x 10 cm)</option>
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
                            <th class="text-center">QTY</th>
                            <th class="text-center">Status</th>
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
        let locationText = '{{ $products[0]->storage->raw.' - '.$products[0]->storage->area.' - '.$products[0]->storage->rak.' - '.$products[0]->storage->bin }}';
        let boxName = '';
        let purcDoc = '';
        let salesDoc = '';
        let customer = '';

        function getSize(scale) {
            const cmToPx = cm => cm * 37.8;
            switch (scale) {
                case 'small': return { width: cmToPx(7), height: cmToPx(7), fontSize: 12 };
                case 'medium': return { width: cmToPx(8), height: cmToPx(8), fontSize: 13 };
                case 'large': return { width: cmToPx(9), height: cmToPx(9), fontSize: 14 };
                case 'xlarge': return { width: cmToPx(10), height: cmToPx(10), fontSize: 15 };
                default: return { width: cmToPx(7), height: cmToPx(7), fontSize: 12 };
            }
        }

        function showBarcodeModal(index) {
            currentBarcodeId = '';
            locationText = '{{ $products[0]->storage->raw.' - '.$products[0]->storage->area.' - '.$products[0]->storage->rak.' - '.$products[0]->storage->bin }}';
            boxName = '';
            purcDoc = '';
            salesDoc = '';
            customer = '';

            const dataPackage = @json($products);
            const find = dataPackage[index];

            currentBarcodeId = find.number;
            purcDoc = find.purchase_order.purc_doc;
            boxName = find.reff_number;
            customer = find.purchase_order.customer.name;

            const salesDocs = JSON.parse(find.sales_docs);
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
            const { width, height, fontSize } = getSize(scale);

            const container = document.createElement('div');
            Object.assign(container.style, {
                width: `${width}px`,
                height: `${height}px`,
                padding: '8px',
                background: '#fff',
                fontSize: `${fontSize}px`,
            });
            container.setAttribute('id', 'barcodeFinal');

            const qrWrapper = document.createElement('div');
            Object.assign(qrWrapper.style, {
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                marginBottom: '15px',
            });

            const qrDiv = document.createElement('div');
            qrDiv.setAttribute('id', 'qrCodeContainer');
            qrWrapper.appendChild(qrDiv);

            const table = document.createElement('table');
            table.style.width = '100%';
            table.style.borderCollapse = 'collapse';

            const addRow = (label, value) => {
                const tr = document.createElement('tr');

                const tdLabel = document.createElement('td');
                tdLabel.textContent = label;
                tdLabel.style.fontWeight = '600';
                tdLabel.style.textAlign = 'left';
                tdLabel.style.whiteSpace = 'nowrap';

                const tdColon = document.createElement('td');
                tdColon.textContent = ':';
                tdColon.style.width = '10px';
                tdColon.style.textAlign = 'center';

                const tdValue = document.createElement('td');
                tdValue.textContent = value ?? '';
                tdValue.style.textAlign = 'left';
                tdValue.style.paddingLeft = '2px';

                tr.append(tdLabel, tdColon, tdValue);
                table.appendChild(tr);
            };

            addRow('Box', boxName);
            addRow('Purc Doc', purcDoc);
            addRow('Sales Doc', salesDoc);
            addRow('Customer', customer);
            addRow('Location', locationText);
            addRow('Inbound Date', '{{ \Carbon\Carbon::parse($products[0]->created_at)->translatedFormat('d F Y') }}');

            if ('{{ $products[0]->return }}' === '1') {
                addRow('Return Note', '{{ $products[0]->note }}');
            }

            container.appendChild(qrWrapper);
            container.appendChild(table);
            area.appendChild(container);

            const qrSize = { small: 100, medium: 120, large: 140, xlarge: 180 }[scale] || 100;
            new QRCode(qrDiv, { text: currentBarcodeId, width: qrSize, height: qrSize });
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
                            <tr>
                                <td>${sn.serial_number}</td>
                                <td class="text-center fw-bold">${sn.qty}</td>
                                <td class="text-center">${sn.qty === 1 ? '<span class="badge bg-success">Stock Available</span>' : '<span class="badge bg-danger">Stock Not Available</span>'}</td>
                            </tr>
                        `;
                    });

                    document.getElementById('listSerialNumber').innerHTML = html;
                    $('#detailSNModal').modal('show');
                }
            });
        }

        function changeType(id, isParent) {
            let text = '';
            if (parseInt(isParent) === 1) {
                text = 'Change Type to Child';
            } else {
                text = 'Change Type to Parent';
            }

            Swal.fire({
                title: "Are you sure?",
                text: text,
                icon: "warning",
                showCancelButton: true,
                customClass: {
                    confirmButton: "btn btn-primary w-xs me-2 mt-2",
                    cancelButton: "btn btn-danger w-xs mt-2"
                },
                confirmButtonText: "Yes, Change it!",
                buttonsStyling: false,
                showCloseButton: true
            }).then(function(t) {
                if (t.value) {

                $.ajax({
                    url: '{{ route('inventory.change.type.product') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        isParent: isParent
                    },
                    success: (res) => {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Change Type Product Success',
                            icon: 'success'
                        }).then((i) => {
                            window.location.reload();
                        });
                    }
                });

                }
            });
        }
    </script>

@endsection
