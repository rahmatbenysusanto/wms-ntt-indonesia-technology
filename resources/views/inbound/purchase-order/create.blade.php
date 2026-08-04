@extends('layout.index')
@section('title', 'Purchase Order')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Create Manual PO</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item">Purchase Order</li>
                        <li class="breadcrumb-item active">Create Manual</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- Card 1: PO Header --}}
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">PO Header Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Purc Doc <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="purcDoc" placeholder="e.g. 4500012345" required>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Vendor <span class="text-danger">*</span></label>
                            <select class="form-control select2Vendor" id="vendorSelect" required>
                                <option value="">-- Select or Type Vendor --</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->name }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Customer</label>
                            <select class="form-control select2Customer" id="customerSelect">
                                <option value="">-- Select or Type Customer --</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->name }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="poDate" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Line Items --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Line Items</h4>
                        <button type="button" class="btn btn-primary btn-sm" onclick="addRow()">
                            <i class="mdi mdi-plus"></i> Add Line Item
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="lineItemsTable">
                            <thead>
                                <tr>
                                    <th style="min-width:30px">#</th>
                                    <th style="min-width:100px">Sales Doc</th>
                                    <th style="min-width:80px">Item <span class="text-danger">*</span></th>
                                    <th style="min-width:120px">Material <span class="text-danger">*</span></th>
                                    <th style="min-width:150px">PO Item Desc</th>
                                    <th style="min-width:140px">Prod Hierarchy Desc</th>
                                    <th style="min-width:100px">Acc Ass Cat</th>
                                    <th style="min-width:80px">Stor Loc</th>
                                    <th style="min-width:100px">SLoc Desc</th>
                                    <th style="min-width:80px">Valuation</th>
                                    <th style="min-width:90px">PO Itm Qty <span class="text-danger">*</span></th>
                                    <th style="min-width:110px">Net Price <span class="text-danger">*</span></th>
                                    <th style="min-width:80px">Currency</th>
                                    <th style="min-width:60px">Action</th>
                                </tr>
                            </thead>
                            <tbody id="lineItemsBody">
                                {{-- Rows added dynamically via JS --}}
                            </tbody>
                        </table>
                    </div>
                    <div id="noItemsMsg" class="text-center text-muted py-3">
                        <i class="mdi mdi-information-outline"></i> No line items yet. Click "Add Line Item" to add one.
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Submit --}}
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('inbound.purchase-order') }}" class="btn btn-secondary">Cancel</a>
                        <button type="button" class="btn btn-success" onclick="processManualImport()">
                            <i class="mdi mdi-content-save"></i> Create Purchase Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        let rowCounter = 0;

        // ========== Row Template ==========
        function createRowHTML(index) {
            return `
                <tr id="row-${index}">
                    <td>${index + 1}</td>
                    <td><input type="text" class="form-control form-control-sm" data-field="sales_doc" placeholder="Auto-generated if empty"></td>
                    <td><input type="number" class="form-control form-control-sm" data-field="item" placeholder="e.g. 10" required></td>
                    <td><input type="text" class="form-control form-control-sm" data-field="material" placeholder="e.g. 023110AF6U" required></td>
                    <td><input type="text" class="form-control form-control-sm" data-field="po_item_desc" placeholder="Description"></td>
                    <td><input type="text" class="form-control form-control-sm" data-field="prod_hierarchy_desc" placeholder="Hierarchy"></td>
                    <td><input type="text" class="form-control form-control-sm" data-field="acc_ass_cat" placeholder="Acc Ass Cat"></td>
                    <td><input type="number" class="form-control form-control-sm" data-field="stor_loc" placeholder="Storage Loc"></td>
                    <td><input type="text" class="form-control form-control-sm" data-field="sloc_desc" placeholder="SLoc Desc"></td>
                    <td><input type="text" class="form-control form-control-sm" data-field="valuation" placeholder="Valuation"></td>
                    <td><input type="number" class="form-control form-control-sm" data-field="po_item_qty" placeholder="Qty" value="1" required></td>
                    <td><input type="number" class="form-control form-control-sm" data-field="net_order_price" placeholder="Price" step="0.01" required></td>
                    <td>
                        <select class="form-control form-control-sm" data-field="currency">
                            <option value="IDR">IDR</option>
                            <option value="USD">USD</option>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${index})" title="Remove">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </td>
                </tr>`;
        }

        // ========== Add / Remove Rows ==========
        function addRow() {
            const tbody = document.getElementById('lineItemsBody');
            const row = document.createElement('tr');
            row.innerHTML = createRowHTML(rowCounter);
            // Replace the inner structure properly
            const newRow = document.createElement('tbody');
            newRow.innerHTML = createRowHTML(rowCounter);
            tbody.appendChild(newRow.firstElementChild);
            rowCounter++;
            updateRowNumbers();
            toggleNoItemsMsg();
        }

        function removeRow(index) {
            const row = document.getElementById('row-' + index);
            if (row) {
                row.remove();
                updateRowNumbers();
                toggleNoItemsMsg();
            }
        }

        function updateRowNumbers() {
            const rows = document.querySelectorAll('#lineItemsBody tr');
            rows.forEach((row, i) => {
                row.querySelector('td:first-child').textContent = i + 1;
            });
        }

        function toggleNoItemsMsg() {
            const rows = document.querySelectorAll('#lineItemsBody tr');
            document.getElementById('noItemsMsg').style.display = rows.length === 0 ? '' : 'none';
        }

        // ========== Collect Data ==========
        function getLineItems() {
            const rows = document.querySelectorAll('#lineItemsBody tr');
            const items = [];
            rows.forEach(row => {
                const getVal = (field) => {
                    const el = row.querySelector(`[data-field="${field}"]`);
                    return el ? el.value.trim() : '';
                };

                const poItemQty = parseInt(getVal('po_item_qty')) || 0;
                const netOrderPrice = parseFloat(getVal('net_order_price')) || 0;

                items.push({
                    sales_doc: getVal('sales_doc'),
                    item: getVal('item'),
                    material: getVal('material'),
                    po_item_desc: getVal('po_item_desc'),
                    prod_hierarchy_desc: getVal('prod_hierarchy_desc'),
                    acc_ass_cat: getVal('acc_ass_cat'),
                    stor_loc: getVal('stor_loc'),
                    sloc_desc: getVal('sloc_desc'),
                    valuation: getVal('valuation'),
                    po_item_qty: poItemQty,
                    net_order_price: netOrderPrice,
                    currency: getVal('currency') || 'IDR',
                });
            });
            return items;
        }

        // ========== Validation ==========
        function validateForm(items) {
            const purcDoc = document.getElementById('purcDoc').value.trim();

            if (!purcDoc) {
                Swal.fire('Validation Error', 'Purc Doc is required.', 'warning');
                return false;
            }

            if (items.length === 0) {
                Swal.fire('Validation Error', 'At least one line item is required.', 'warning');
                return false;
            }

            for (let i = 0; i < items.length; i++) {
                const item = items[i];
                if (!item.item) {
                    Swal.fire('Validation Error', `Row ${i + 1}: Item is required.`, 'warning');
                    return false;
                }
                if (!item.material) {
                    Swal.fire('Validation Error', `Row ${i + 1}: Material is required.`, 'warning');
                    return false;
                }
                if (!item.po_item_qty || item.po_item_qty <= 0) {
                    Swal.fire('Validation Error', `Row ${i + 1}: PO Item Qty must be greater than 0.`, 'warning');
                    return false;
                }
            }

            return true;
        }

        // ========== Import / Submit ==========
        function processManualImport() {
            const purcDoc = document.getElementById('purcDoc').value.trim();
            const vendorName = $('#vendorSelect').val() || '';
            const customerName = $('#customerSelect').val() || '';
            const date = document.getElementById('poDate').value;

            const lineItemsRaw = getLineItems();

            if (!validateForm(lineItemsRaw)) return;

            // Build payload matching Excel upload format
            const purchaseOrder = lineItemsRaw.map(item => ({
                purc_doc: purcDoc,
                sales_doc: item.sales_doc || '',
                item: item.item,
                material: item.material.replace(/\./g, ''),
                po_item_desc: item.po_item_desc,
                prod_hierarchy_desc: item.prod_hierarchy_desc,
                acc_ass_cat: item.acc_ass_cat,
                vendor_name: vendorName,
                customer_name: customerName,
                stor_loc: item.stor_loc,
                sloc_desc: item.sloc_desc,
                valuation: item.valuation,
                po_item_qty: item.po_item_qty,
                net_order_price: item.net_order_price,
                currency: item.currency,
                date: date,
            }));

            Swal.fire({
                title: "Are you sure?",
                text: `Create Purchase Order "${purcDoc}" with ${purchaseOrder.length} line item(s)?`,
                icon: "warning",
                showCancelButton: true,
                customClass: {
                    confirmButton: "btn btn-primary w-xs me-2 mt-2",
                    cancelButton: "btn btn-danger w-xs mt-2"
                },
                confirmButtonText: "Yes, Create it!",
                buttonsStyling: false,
                showCloseButton: true
            }).then(async (t) => {
                if (!t.value) return;

                // Show progress
                Swal.fire({
                    title: 'Creating Purchase Order...',
                    html: `
                        <div class="d-flex align-items-center gap-2">
                          <div class="spinner-border" role="status" aria-hidden="true"></div>
                          <span class="small text-muted">Saving to server…</span>
                        </div>
                    `,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading(),
                });

                try {
                    const res = await fetch(`{{ route('inbound.purchase-order-upload-process') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ purchaseOrder: purchaseOrder })
                    });

                    if (!res.ok) throw new Error(`HTTP ${res.status}`);

                    const json = await res.json();

                    if (json.status === true) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Purchase Order created successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: "btn btn-primary w-xs mt-2"
                            },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.href = '{{ route('inbound.purchase-order') }}';
                        });
                    } else {
                        Swal.fire('Error', 'Failed to create Purchase Order. Please try again.', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    Swal.fire('Error', 'An unexpected error occurred. Please try again.', 'error');
                }
            });
        }

        // ========== Initialize ==========
        $(document).ready(function() {
            // Select2 with tags for Vendor
            $('#vendorSelect').select2({
                placeholder: "-- Select or Type Vendor --",
                tags: true,
                allowClear: true,
                width: '100%',
                createTag: function(params) {
                    const term = $.trim(params.term);
                    if (term === '') return null;
                    // Prevent duplicates with existing options
                    if ($('#vendorSelect').find("option[value='" + term + "']").length > 0) {
                        return null;
                    }
                    return { id: term, text: term, tag: true };
                }
            });

            // Select2 with tags for Customer
            $('#customerSelect').select2({
                placeholder: "-- Select or Type Customer --",
                tags: true,
                allowClear: true,
                width: '100%',
                createTag: function(params) {
                    const term = $.trim(params.term);
                    if (term === '') return null;
                    if ($('#customerSelect').find("option[value='" + term + "']").length > 0) {
                        return null;
                    }
                    return { id: term, text: term, tag: true };
                }
            });

            // Add first empty row
            addRow();
        });
    </script>
@endsection
