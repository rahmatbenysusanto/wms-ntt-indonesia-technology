@php $isEdit = isset($packingList) && $packingList; @endphp
@extends('layout.index')
@section('title', $isEdit ? 'Edit Packing List' : 'Create Packing List')
@section('sizeBarSize', 'sm')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $isEdit ? 'Edit Packing List' : 'Create Packing List' }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('outbound.packaging-list') }}">Packing List</a></li>
                        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Create' }}</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Data Packing</h4>
                        @if ($isEdit)
                            <span class="badge bg-warning">Draft #{{ $packingList->id }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Customer</label>
                            <select class="form-control select2Customer" id="customerId">
                                <option value="">-- Select Customer --</option>
                                @foreach ($customer as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $isEdit && $packingList->customer_id == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Location</label>
                            <input type="text" class="form-control" id="delivLocation" placeholder="Delivery Location"
                                value="{{ $isEdit ? $packingList->deliv_loc : '' }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Destination</label>
                            <select class="form-control" id="deliveryDest">
                                @foreach (['client', 'general room', 'pm room', 'spare room'] as $dest)
                                    <option value="{{ $dest }}"
                                        {{ $isEdit && $packingList->deliv_dest == $dest ? 'selected' : '' }}>
                                        {{ ucwords($dest) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Date</label>
                            <input type="datetime-local" class="form-control" id="deliveryDate"
                                value="{{ $isEdit && $packingList->delivery_date ? $packingList->delivery_date->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">DN No.</label>
                            <input type="text" class="form-control" id="dnNo" placeholder="DN No."
                                value="{{ $isEdit ? $packingList->dn_no : '' }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Cust. PO No.</label>
                            <input type="text" class="form-control" id="custPoNo" placeholder="Customer PO No."
                                value="{{ $isEdit ? $packingList->cust_po_no : '' }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">PIC</label>
                            <input type="text" class="form-control" id="pic" placeholder="PIC"
                                value="{{ $isEdit ? $packingList->pic : '' }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Ship Via</label>
                            <input type="text" class="form-control" id="shipVia" placeholder="Ship Via"
                                value="{{ $isEdit ? $packingList->ship_via : '' }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" id="note" rows="3" placeholder="Note - Optional">{{ $isEdit ? $packingList->note : '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">List Sumber</h4>
                    <a class="btn btn-success btn-sm" onclick="pilihYangDicentang()">
                        <i class="mdi mdi-check-all"></i> Pilih yang Dicentang
                    </a>
                </div>
                <div class="card-body">
                    <table id="tabelSalesDoc" class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAll" onchange="toggleCheckAll(this)"></th>
                                <th>#</th>
                                <th>Sumber</th>
                                <th>Info</th>
                                <th>Customer</th>
                                <th>QTY</th>
                            </tr>
                        </thead>
                        <tbody id="listSalesDoc">
                            @foreach ($sourceList as $idx => $src)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="row-check"
                                            data-source="{{ $src['source'] }}"
                                            data-id="{{ $src['id'] }}"
                                            data-customer-id="{{ $src['customer_id'] }}">
                                    </td>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        @if ($src['source'] === 'outbound')
                                            <span class="badge bg-primary">Outbound</span>
                                        @else
                                            <span class="badge bg-success">Inventory</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div><b>{{ $src['label'] }}</b></div>
                                        <div class="small text-muted">{{ $src['sub'] }}</div>
                                        <div class="small text-muted">
                                            {{ $src['date'] ? \Carbon\Carbon::parse($src['date'])->format('d M Y') : '' }}
                                        </div>
                                    </td>
                                    <td>{{ $src['customer_name'] }}</td>
                                    <td>{{ $src['qty'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Product</h4>
                        <div>
                            @if ($isEdit)
                                <input type="hidden" id="packingId" value="{{ $packingList->id }}">
                            @endif
                            <a class="btn btn-primary" onclick="savePacking()">
                                <i class="mdi mdi-content-save"></i>
                                {{ $isEdit ? 'Update Draft' : 'Save as Draft' }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>
                                    <select class="form-control" id="changeMassQTY" onchange="changeMassQTY(this.value)">
                                        <option value="0">-- Choose QTY --</option>
                                        @foreach (range(1, 15) as $v)
                                            <option>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th></th>
                            </tr>
                            <tr>
                                <th>Part Number</th>
                                <th>Item</th>
                                <th>Sales Doc</th>
                                <th class="text-center">QTY</th>
                                <th>HS Code</th>
                                <th>Gross Wt (kg)</th>
                                <th>Measurement (CM)</th>
                                <th>QTY Packing</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="listProductPacking">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://unpkg.com/dexie@3/dist/dexie.min.js"></script>

    <script>
        const isEdit = {{ $isEdit ? 'true' : 'false' }};
        const db = new Dexie('PackingDB');
        db.version(1).stores({ kv: 'key' });

        async function kvSet(key, value) { return db.kv.put({ key, value }); }
        async function kvGet(key, fallback = null) { const row = await db.kv.get(key); return row ? row.value : fallback; }
        async function kvDelete(key) { return db.kv.delete(key); }

        function reinitTable() {
            const $tbl = $('#tabelSalesDoc');
            if ($.fn.DataTable && $.fn.DataTable.isDataTable($tbl)) $tbl.DataTable().destroy();
            if ($.fn.DataTable) $tbl.DataTable({ pageLength: 10, responsive: true, autoWidth: false });
        }

        window.toggleCheckAll = function(el) {
            document.querySelectorAll('.row-check').forEach(cb => cb.checked = el.checked);
        };

        $(document).ready(async function() {
            $('.select2Customer').select2({ placeholder: '-- Select Customer --', allowClear: true, width: '100%' });
            reinitTable();

            // Pre-fill products when editing a draft
            const existingDetails = @json($isEdit ? $packingList->details : []);
            const existingOutboundId = @json($isEdit && $packingList ? $packingList->outbound_id : null);

            if (isEdit && existingDetails.length) {
                const products = existingDetails.map(d => ({
                    outboundDetailId: null,
                    inventoryPackageItemId: d.inventory_package_item_id,
                    purchaseOrderDetailId: d.purchase_order_detail_id,
                    qty: d.qty,
                    qtySelect: d.qty,
                    productId: d.product_id,
                    material: d.material,
                    poItemDesc: d.po_item_desc,
                    hsCode: d.hs_code,
                    grossWeight: d.gross_weight,
                    measurement: d.measurement,
                    salesDoc: d.sales_doc,
                    item: d.item,
                    disable: 0,
                }));
                await kvSet('salesDocProduct', products);
                await kvSet('selectedOutboundId', existingOutboundId);
            } else {
                await kvDelete('salesDocProduct');
                await kvDelete('selectedOutboundId');
            }

            await viewProductPacking();
        });

        window.pilihYangDicentang = async function() {
            const checked = [];
            document.querySelectorAll('.row-check:checked').forEach(cb => {
                checked.push({
                    source: cb.dataset.source,
                    id: cb.dataset.id,
                    customerId: cb.dataset.customerId,
                });
            });

            if (!checked.length) {
                Swal.fire({ title: 'Warning', text: 'Centang minimal 1 baris', icon: 'warning' });
                return;
            }

            // Fetch semua items secara parallel
            let results;
            try {
                results = await Promise.all(checked.map(item => {
                    const url = item.source === 'outbound'
                        ? '{{ route('outbound.packaging-list.outbound-items') }}'
                        : '{{ route('outbound.packaging-list.inventory-items') }}';
                    return $.get(url, { id: item.id });
                }));
            } catch (e) {
                Swal.fire({ title: 'Error', text: 'Gagal fetch data', icon: 'error' });
                return;
            }

            const allProducts = [];
            results.forEach((res, i) => {
                const src = checked[i];
                if (src.source === 'outbound') {
                    (res?.data?.outbound_detail ?? []).forEach(detail => {
                        const pod = detail?.inventory_package_item?.purchase_order_detail;
                        allProducts.push({
                            outboundDetailId: detail.id,
                            inventoryPackageItemId: detail.inventory_package_item_id,
                            purchaseOrderDetailId: pod?.id ?? null,
                            qty: detail.qty,
                            qtySelect: detail.qty,
                            productId: detail?.inventory_package_item?.product_id ?? null,
                            material: pod?.material ?? '',
                            poItemDesc: pod?.po_item_desc ?? '',
                            salesDoc: pod?.sales_doc ?? '',
                            item: pod?.item ?? '',
                            hsCode: '',
                            grossWeight: '',
                            measurement: '',
                            disable: 0,
                            sourceType: 'outbound',
                        });
                    });
                } else {
                    (res?.data?.inventory_package_item ?? []).forEach(ipItem => {
                        const pod = ipItem?.purchase_order_detail;
                        allProducts.push({
                            outboundDetailId: null,
                            inventoryPackageItemId: ipItem.id,
                            purchaseOrderDetailId: pod?.id ?? null,
                            qty: ipItem.qty,
                            qtySelect: ipItem.qty,
                            productId: ipItem.product_id ?? null,
                            material: pod?.material ?? '',
                            poItemDesc: pod?.po_item_desc ?? '',
                            salesDoc: pod?.sales_doc ?? '',
                            item: pod?.item ?? '',
                            hsCode: '',
                            grossWeight: '',
                            measurement: '',
                            disable: 0,
                            sourceType: 'inventory',
                        });
                    });
                }
            });

            // Set customer dari item pertama
            if (checked[0]?.customerId) {
                $('#customerId').val(checked[0].customerId).trigger('change');
            }

            // Append ke existing products
            const existing = await kvGet('salesDocProduct', []) ?? [];
            await kvSet('salesDocProduct', [...existing, ...allProducts]);
            await kvSet('selectedOutboundId', checked[0]?.id ?? null);
            await viewProductPacking();
        };

        async function viewProductPacking() {
            const products = await kvGet('salesDocProduct', []) ?? [];
            let html = '';

            products.forEach((item, index) => {
                if (parseInt(item?.disable) === 0) {
                    html += `
                        <tr>
                            <td>
                                <div>${item?.material ?? ''}</div>
                                <div class="small text-muted">${item?.poItemDesc ?? ''}</div>
                            </td>
                            <td>${item.item ?? ''}</td>
                            <td>${item?.salesDoc ?? ''}</td>
                            <td class="text-center fw-bold">${item?.qty ?? 0}</td>
                            <td><input type="text" class="form-control" onchange="changeField(${index}, 'hsCode', this.value)" value="${item?.hsCode ?? ''}"></td>
                            <td><input type="number" step="0.01" min="0" class="form-control" onchange="changeField(${index}, 'grossWeight', this.value)" value="${item?.grossWeight ?? ''}"></td>
                            <td><input type="text" class="form-control" placeholder="P x L x T" onchange="changeField(${index}, 'measurement', this.value)" value="${item?.measurement ?? ''}"></td>
                            <td><input type="number" class="form-control" onchange="changeQtySelect(${index}, this.value)" value="${item?.qtySelect ?? 0}"></td>
                            <td><a class="btn btn-danger btn-sm" onclick="deleteProduct(${index})">Delete</a></td>
                        </tr>
                    `;
                }
            });

            const el = document.getElementById('listProductPacking');
            if (el) el.innerHTML = html;
        }

        async function changeMassQTY(value) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            const qtyValue = parseInt(value);
            for (const item of products) {
                if (parseInt(item?.disable) === 0) {
                    if (qtyValue > parseInt(item.qty)) {
                        await Swal.fire({ icon: 'error', title: 'Qty tidak valid', text: 'Qty melebihi qty tersedia' });
                        return;
                    }
                    item.qtySelect = qtyValue;
                }
            }
            await kvSet('salesDocProduct', products);
            await viewProductPacking();
        }

        window.deleteProduct = async function deleteProduct(index) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            if (!products[index]) return;
            products[index].disable = 1;
            await kvSet('salesDocProduct', products);
            await viewProductPacking();
        };

        window.changeField = async function changeField(index, field, value) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            if (!products[index]) return;
            products[index][field] = value;
            await kvSet('salesDocProduct', products);
        };

        window.changeQtySelect = async function changeQtySelect(index, value) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            if (!products[index]) return;
            const newVal = parseInt(value || 0);
            if (newVal > parseInt(products[index]?.qty || 0)) {
                Swal.fire({ title: 'Warning!', text: 'QTY packing melebihi qty tersedia', icon: 'warning' });
                await viewProductPacking();
                return;
            }
            products[index].qtySelect = newVal;
            await kvSet('salesDocProduct', products);
            await viewProductPacking();
        };

        window.savePacking = async function savePacking() {
            const products = await kvGet('salesDocProduct', []) ?? [];
            const hasProduct = products.some(p => parseInt(p?.disable) === 0 && parseInt(p?.qtySelect || 0) > 0);
            if (!hasProduct) {
                Swal.fire({ title: 'Warning!', text: 'Pilih minimal 1 produk dengan QTY > 0', icon: 'warning' });
                return;
            }

            const t = await Swal.fire({
                title: isEdit ? 'Update this draft?' : 'Save as draft?',
                text: isEdit ? 'Packing list draft will be updated.' : 'Data will be saved as a draft and can be edited later.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: isEdit ? 'Yes, Update it!' : 'Yes, Save it!',
                cancelButtonText: 'No'
            });
            if (!t.value) return;

            const selectedOutboundId = await kvGet('selectedOutboundId', null);
            const payload = products.map(p => {
                const { dataSN, serialNumber, ...rest } = p;
                return rest;
            });

            const data = {
                _token: '{{ csrf_token() }}',
                products: payload,
                outboundId: selectedOutboundId,
                customerId: $('#customerId').val(),
                delivLocation: $('#delivLocation').val(),
                deliveryDest: $('#deliveryDest').val(),
                deliveryDate: $('#deliveryDate').val() ?? '',
                dnNo: $('#dnNo').val() ?? '',
                custPoNo: $('#custPoNo').val() ?? '',
                pic: $('#pic').val() ?? '',
                shipVia: $('#shipVia').val() ?? '',
                note: $('#note').val() ?? '',
            };
            if (isEdit) data.packing_id = $('#packingId').val();

            $.ajax({
                url: isEdit
                    ? '{{ route('outbound.packaging-list.update') }}'
                    : '{{ route('outbound.packaging-list.store') }}',
                method: 'POST',
                data: data,
                success: (res) => {
                    if (res?.status) {
                        Swal.fire({ title: 'Success', text: res?.message || 'Saved successfully', icon: 'success' })
                            .then(() => { window.location.href = '{{ route('outbound.packaging-list') }}'; });
                    } else {
                        Swal.fire({ title: 'Error', text: res?.message || 'Save failed', icon: 'error' });
                    }
                },
                error: (xhr) => {
                    let msg = 'Request failed';
                    try { msg = JSON.parse(xhr.responseText)?.message || msg; } catch {}
                    Swal.fire({ title: 'Error', text: msg, icon: 'error' });
                }
            });
        };
    </script>
@endsection
