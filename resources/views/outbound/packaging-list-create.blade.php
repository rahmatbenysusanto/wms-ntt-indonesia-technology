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
                <div class="card-header">
                    <h4 class="card-title mb-0">List Sales Doc</h4>
                </div>
                <div class="card-body">
                    <table id="tabelSalesDoc" class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sales Doc</th>
                                <th>Data Box</th>
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
                                <th></th>
                            </tr>
                            <tr>
                                <th>Material</th>
                                <th>Item</th>
                                <th class="text-center">Type</th>
                                <th>Box</th>
                                <th>Sales Doc</th>
                                <th class="text-center">QTY</th>
                                <th>Part Number</th>
                                <th>HS Code</th>
                                <th>Gross Wt (kg)</th>
                                <th>Measurement (CM)</th>
                                <th>QTY Packing</th>
                                <th class="text-center">SN</th>
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
    <!-- Serial Number Modal -->
    <div id="serialNumberModal" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Serial Number Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="snIndex">
                    <div class="mb-2">
                        QTY Packing: <b id="sn-qty">0</b> &nbsp;|&nbsp; SN dipilih: <b id="sn-picked">0</b>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Data Serial Number</h5>
                                <div>
                                    <a class="btn btn-warning btn-sm me-1" onclick="togglePasteSN()">Paste SN</a>
                                    <a class="btn btn-danger btn-sm" onclick="pilihSemuaSN()">Pilih Semua SN</a>
                                </div>
                            </div>
                            <div id="pasteSNArea" class="mb-2 d-none">
                                <textarea class="form-control mb-1" id="pasteSNList" rows="4"
                                    placeholder="Paste SN dari Excel&#10;1 baris = 1 SN"></textarea>
                                <button type="button" class="btn btn-success btn-sm" onclick="pilihSNPaste()">Cocokkan &amp; Pilih</button>
                            </div>
                            <input type="text" class="form-control mb-2" id="searchSN" placeholder="Search Serial Number" onkeyup="filterSN()">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr><th>Serial Number</th><th>Action</th></tr>
                                </thead>
                                <tbody id="listDataSN"></tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <h5 class="card-title mb-2">SN Dipilih (Packing List)</h5>
                            <input type="text" class="form-control mb-2" id="snSearch" placeholder="Search SN dipilih" onkeyup="filterPickedSN()">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr><th>Serial Number</th><th>Action</th></tr>
                                </thead>
                                <tbody id="listDataOutboundSN"></tbody>
                            </table>
                        </div>
                    </div>
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
        db.version(1).stores({
            kv: 'key'
        });

        async function kvSet(key, value) {
            return db.kv.put({ key, value });
        }

        async function kvGet(key, fallback = null) {
            const row = await db.kv.get(key);
            return row ? row.value : fallback;
        }

        function normalizeSalesDocs(raw) {
            if (raw == null) return [];
            if (Array.isArray(raw)) return raw.filter(v => v != null && v !== '').map(String);
            if (typeof raw === 'object') {
                try { return Object.values(raw).filter(v => v != null && v !== '').map(String); } catch { return []; }
            }
            if (typeof raw === 'number' || typeof raw === 'boolean') return [String(raw)];
            let s = String(raw).trim();
            if (!s) return [];
            if ((s.startsWith('[') && s.endsWith(']')) || (s.startsWith('"') && s.endsWith('"')))
                try { const p = JSON.parse(s); return Array.isArray(p) ? p.filter(v => v != null && v !== '').map(String) : [String(p)]; } catch {}
            if (s.startsWith('{') && s.endsWith('}'))
                try { const p = JSON.parse(s); if (typeof p === 'object' && !Array.isArray(p)) return Object.values(p).filter(v => v != null && v !== '').map(String); return [String(p)]; } catch {}
            if (s.includes(',')) return s.split(',').map(x => x.trim()).filter(v => v).map(String);
            return [s];
        }

        function reinitSalesDocTable() {
            const $tbl = $('#tabelSalesDoc');
            if ($.fn.DataTable && $.fn.DataTable.isDataTable($tbl)) $tbl.DataTable().destroy();
            if ($.fn.DataTable) $tbl.DataTable({ pageLength: 10, responsive: true, autoWidth: false });
        }

        $(document).ready(async function() {
            $('.select2Customer').select2({
                placeholder: '-- Select Customer --',
                allowClear: true,
                width: '100%'
            });

            try {
                await kvSet('salesDoc', @json($salesDoc));
            } catch (e) {
                console.error('Gagal simpan salesDoc:', e);
            }
            await viewSalesDoc();

            // Pre-fill products when editing a draft
            const existingDetails = @json($isEdit ? $packingList->details : []);

            if (isEdit && existingDetails.length) {
                const products = existingDetails.map(d => {
                const picked = d.serial_numbers ?? [];
                const dataSN = (d.inventory_package_item?.inventory_package_item_sn ?? [])
                    .filter(sn => parseInt(sn?.qty) !== 0)
                    .map(sn => ({ serialNumber: sn.serial_number, select: picked.includes(sn.serial_number) ? 1 : 0 }));
                return {
                    inventoryPackageId: null,
                    inventoryPackageItemId: d.inventory_package_item_id,
                    purchaseOrderId: null,
                    purchaseOrderDetailId: d.purchase_order_detail_id,
                    isParent: 0,
                    directOutbound: 0,
                    qty: d.qty,
                    qtySelect: d.qty,
                    productId: d.product_id,
                    material: d.material,
                    poItemDesc: d.po_item_desc,
                    partNumber: d.part_number,
                    hsCode: d.hs_code,
                    grossWeight: d.gross_weight,
                    measurement: d.measurement,
                    prodHierarchyDesc: '',
                    salesDoc: d.sales_doc,
                    purcDoc: '',
                    number: '',
                    reffNumber: '',
                    loc: '',
                    storageId: null,
                    disable: 0,
                    item: d.item,
                    dataSN: dataSN,
                    serialNumber: picked,
                };
            });
                await kvSet('salesDocProduct', products);
            } else {
                await kvDelete('salesDocProduct');
            }
            await viewProductPacking();
        });

        async function viewSalesDoc() {
            const salesDoc = await kvGet('salesDoc', []) ?? [];
            let html = '';
            let number = 1;

            salesDoc.forEach((item) => {
                const docs = normalizeSalesDocs(item?.sales_docs);
                let salesDocHtml = docs.map(d => `<div>${d}</div>`).join('');

                let storage = '';
                if (item?.storage) {
                    storage = `${item.storage.raw ?? ''} - ${item.storage.area ?? ''} - ${item.storage.rak ?? ''} - ${item.storage.bin ?? ''}`;
                    if (parseInt(item.storage.id) === 1) storage = 'Cross Docking';
                }

                html += `
                    <tr>
                        <td>${number}</td>
                        <td>${salesDocHtml}</td>
                        <td>
                            ${parseInt(item?.storage?.id) === 1 ? '<div><span class="badge bg-danger"> Cross Docking </span></div>' : ''}
                            <div class="fw-bold mb-1">${item?.purchase_order?.customer?.name ?? ''}</div>
                            <div><b>Purc Doc: </b>${item?.purchase_order?.purc_doc ?? ''}</div>
                            <div>${item?.number ?? ''}</div>
                            <div><b>Box: </b>${item?.reff_number ?? ''}</div>
                            <div><b>Loc: </b>${storage}</div>
                        </td>
                        <td>${item?.qty ?? 0}</td>
                        <td><a class="btn btn-info btn-sm" onclick="pilihSalesDoc(${item?.id ?? 'null'})">Pilih</a></td>
                    </tr>
                `;
                number++;
            });

            const tbody = document.getElementById('listSalesDoc');
            if (tbody) tbody.innerHTML = html;
            reinitSalesDocTable();
        }

        window.pilihSalesDoc = function pilihSalesDoc(id) {
            $.ajax({
                url: '{{ route('outbound.sales-doc') }}',
                method: 'GET',
                data: { id },
                success: async (res) => {
                    try {
                        const existing = await kvGet('salesDocProduct', []) ?? [];
                        const products = [];

                        (res?.data?.inventory_package_item ?? []).forEach((product) => {
                            const dataSN = [];
                            (product?.inventory_package_item_sn ?? []).forEach((sn) => {
                                if (parseInt(sn?.qty) !== 0) dataSN.push({ serialNumber: sn.serial_number, select: 0 });
                            });
                            products.push({
                                inventoryPackageId: product.inventory_package_id,
                                inventoryPackageItemId: product.id,
                                purchaseOrderId: product?.purchase_order_detail?.purchase_order_id,
                                purchaseOrderDetailId: product?.purchase_order_detail_id,
                                isParent: product?.is_parent,
                                directOutbound: product?.direct_outbound,
                                qty: product?.qty,
                                qtySelect: 0,
                                productId: product?.product_id,
                                material: product?.purchase_order_detail?.material,
                                poItemDesc: product?.purchase_order_detail?.po_item_desc,
                                prodHierarchyDesc: product?.purchase_order_detail?.prod_hierarchy_desc,
                                salesDoc: product?.purchase_order_detail?.sales_doc,
                                purcDoc: res?.data?.purchase_order?.purc_doc,
                                number: res?.data?.number,
                                reffNumber: res?.data?.reff_number,
                                loc: `${res?.data?.storage?.raw ?? ''}-${res?.data?.storage?.area ?? ''}-${res?.data?.storage?.rak ?? ''}-${res?.data?.storage?.bin ?? ''}`,
                                storageId: res?.data?.storage?.id,
                                disable: 0,
                                item: product.purchase_order_detail?.item,
                                dataSN: dataSN,
                                serialNumber: [],
                            });
                        });

                        // keep previously added rows, append the new ones
                        await kvSet('salesDocProduct', existing.concat(products));
                        await viewProductPacking();
                    } catch (e) {
                        console.error('Gagal proses pilihSalesDoc:', e);
                        Swal.fire({ title: 'Error', text: 'Gagal memproses data', icon: 'error' });
                    }
                },
                error: () => Swal.fire({ title: 'Error', text: 'Request gagal', icon: 'error' })
            });
        };

        async function viewProductPacking() {
            const products = await kvGet('salesDocProduct', []) ?? [];
            let html = '';

            products.forEach((item, index) => {
                if (parseInt(item?.disable) === 0) {
                    html += `
                        <tr>
                            <td>
                                ${ parseInt(item?.directOutbound) === 1 ? '<div><span class="badge bg-danger"> Cross Docking </span></div>' : '' }
                                <div>${item?.material ?? ''}</div>
                                <div>${item?.poItemDesc ?? ''}</div>
                                <div>${item?.prodHierarchyDesc ?? ''}</div>
                            </td>
                            <td>${item.item ?? ''}</td>
                            <td class="text-center">
                                ${parseInt(item?.isParent) === 1
                                    ? '<span class="badge bg-danger-subtle text-danger">Parent</span>'
                                    : '<span class="badge bg-secondary-subtle text-secondary">Child</span>'}
                            </td>
                            <td>
                                <div><b>PA: </b>${item?.number ?? ''}</div>
                                <div><b>Box: </b>${item?.reffNumber ?? ''}</div>
                                <div><b>Loc: </b>${item?.loc ?? ''}</div>
                            </td>
                            <td>${item?.salesDoc ?? ''}</td>
                            <td class="text-center fw-bold">${item?.qty ?? 0}</td>
                            <td><input type="text" class="form-control" onchange="changeField(${index}, 'partNumber', this.value)" value="${item?.partNumber ?? ''}"></td>
                            <td><input type="text" class="form-control" onchange="changeField(${index}, 'hsCode', this.value)" value="${item?.hsCode ?? ''}"></td>
                            <td><input type="number" step="0.01" min="0" class="form-control" onchange="changeField(${index}, 'grossWeight', this.value)" value="${item?.grossWeight ?? ''}"></td>
                            <td><input type="text" class="form-control" placeholder="P x L x T" onchange="changeField(${index}, 'measurement', this.value)" value="${item?.measurement ?? ''}"></td>
                            <td><input type="number" class="form-control" onchange="changeQtySelect(${index}, this.value)" value="${item?.qtySelect ?? 0}"></td>
                            <td class="text-center">
                                <a class="btn ${parseInt(item?.qtySelect || 0) > 0 && (item?.serialNumber || []).length === parseInt(item?.qtySelect) ? 'btn-success' : 'btn-info'} btn-sm"
                                    onclick="openSerialNumberModal(${index})">SN (${(item?.serialNumber || []).length}/${item?.qtySelect || 0})</a>
                            </td>
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
                        await Swal.fire({ icon: 'error', title: 'Qty tidak valid', text: 'Qty yang dipilih melebihi stok tersedia' });
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

        async function pilihSN(index, snIndex) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            const product = products[index];
            if (!product) return;
            const sn = product.dataSN?.[snIndex];
            if (!sn || sn.select) return;
            product.dataSN[snIndex].select = 1;
            product.serialNumber.push(sn.serialNumber);
            await kvSet('salesDocProduct', products);
            await renderSerialNumber(index);
        }

        async function hapusSN(index, snIndex) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            const product = products[index];
            if (!product) return;
            const sn = product.serialNumber?.[snIndex];
            product.serialNumber.splice(snIndex, 1);
            const found = (product.dataSN ?? []).find(d => d.serialNumber === sn);
            if (found) found.select = 0;
            await kvSet('salesDocProduct', products);
            await renderSerialNumber(index);
        }

        async function renderSerialNumber(index) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            const product = products[index];
            if (!product) return;

            document.getElementById('sn-qty').textContent = product.qtySelect || 0;
            document.getElementById('sn-picked').textContent = (product.serialNumber ?? []).length;
            document.getElementById('searchSN').value = '';
            document.getElementById('snSearch').value = '';

            let available = '';
            (product.dataSN ?? []).forEach((sn, snIndex) => {
                if (sn.select) return;
                available += `
                    <tr>
                        <td>${sn.serialNumber ?? ''}</td>
                        <td><a class="btn btn-info btn-sm" onclick="pilihSN(${index}, ${snIndex})">Pilih</a></td>
                    </tr>`;
            });
            document.getElementById('listDataSN').innerHTML = available || '<tr><td colspan="2" class="text-center text-muted">Tidak ada SN tersedia</td></tr>';

            let picked = '';
            (product.serialNumber ?? []).forEach((sn, snIndex) => {
                picked += `
                    <tr>
                        <td>${sn ?? ''}</td>
                        <td><a class="btn btn-danger btn-sm" onclick="hapusSN(${index}, ${snIndex})">Hapus</a></td>
                    </tr>`;
            });
            document.getElementById('listDataOutboundSN').innerHTML = picked || '<tr><td colspan="2" class="text-center text-muted">Belum ada SN dipilih</td></tr>';
        }

        async function refreshDataSN(index) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            const product = products[index];
            if (!product?.inventoryPackageId) return;
            await new Promise((resolve) => {
                $.ajax({
                    url: '{{ route('outbound.sales-doc') }}',
                    method: 'GET',
                    data: { id: product.inventoryPackageId },
                    success: async (res) => {
                        const item = (res?.data?.inventory_package_item ?? [])
                            .find(i => parseInt(i.id) === parseInt(product.inventoryPackageItemId));
                        const picked = product.serialNumber ?? [];
                        product.dataSN = (item?.inventory_package_item_sn ?? [])
                            .filter(sn => parseInt(sn?.qty) !== 0)
                            .map(sn => ({ serialNumber: sn.serial_number, select: picked.includes(sn.serial_number) ? 1 : 0 }));
                        products[index] = product;
                        await kvSet('salesDocProduct', products);
                        resolve();
                    },
                    error: () => resolve()
                });
            });
        }

        window.openSerialNumberModal = async function openSerialNumberModal(index) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            if (!products[index]) return;
            document.getElementById('snIndex').value = index;
            if (!products[index].serialNumber) products[index].serialNumber = [];
            if (!Array.isArray(products[index].dataSN) || !products[index].dataSN.length) {
                await refreshDataSN(index);
            }
            await renderSerialNumber(index);
            new bootstrap.Modal(document.getElementById('serialNumberModal')).show();
        };

        window.filterSN = function filterSN() {
            const q = (document.getElementById('searchSN').value || '').toLowerCase();
            document.querySelectorAll('#listDataSN tr').forEach(tr => {
                tr.style.display = (tr.textContent || '').toLowerCase().includes(q) ? '' : 'none';
            });
        };

        window.filterPickedSN = function filterPickedSN() {
            const q = (document.getElementById('snSearch').value || '').toLowerCase();
            document.querySelectorAll('#listDataOutboundSN tr').forEach(tr => {
                tr.style.display = (tr.textContent || '').toLowerCase().includes(q) ? '' : 'none';
            });
        };

        window.pilihSemuaSN = async function pilihSemuaSN() {
            const index = parseInt(document.getElementById('snIndex').value);
            const products = await kvGet('salesDocProduct', []) ?? [];
            const product = products[index];
            if (!product) return;
            const target = parseInt(product.qtySelect || 0);

            (product.dataSN ?? []).forEach(sn => {
                if (product.serialNumber.length >= target) return;
                if (sn.select) return;
                sn.select = 1;
                product.serialNumber.push(sn.serialNumber);
            });
            await kvSet('salesDocProduct', products);
            await renderSerialNumber(index);
        };

        window.pilihSNPaste = async function pilihSNPaste() {
            const index = parseInt(document.getElementById('snIndex').value);
            const products = await kvGet('salesDocProduct', []) ?? [];
            const product = products[index];
            if (!product) return;
            const target = parseInt(product.qtySelect || 0);
            const list = (document.getElementById('pasteSNList').value || '')
                .split('\n').map(v => v.trim()).filter(v => v);
            let notFound = [];

            list.forEach(value => {
                if (product.serialNumber.length >= target) return;
                const sn = (product.dataSN ?? []).find(d => d.serialNumber === value);
                if (!sn || sn.select) { notFound.push(value); return; }
                sn.select = 1;
                product.serialNumber.push(sn.serialNumber);
            });
            await kvSet('salesDocProduct', products);
            document.getElementById('pasteSNList').value = '';
            await renderSerialNumber(index);
            if (notFound.length) {
                Swal.fire({ title: 'SN tidak cocok', text: notFound.join(', '), icon: 'warning' });
            }
        };

        window.togglePasteSN = function togglePasteSN() {
            document.getElementById('pasteSNArea').classList.toggle('d-none');
        };

        window.changeQtySelect = async function changeQtySelect(index, value) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            if (!products[index]) return;
            const newVal = parseInt(value || 0);
            if (newVal > parseInt(products[index]?.qty || 0)) {
                Swal.fire({ title: 'Warning!', text: 'QTY packing melebihi qty inventory', icon: 'warning' });
                await viewProductPacking();
                return;
            }
            products[index].qtySelect = newVal;
            products[index].serialNumber = (products[index].serialNumber ?? []).slice(0, newVal);
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

            const payload = products.map(p => {
                const { dataSN, serialNumber, ...rest } = p;
                return { ...rest, serialNumbers: serialNumber ?? [] };
            });

            const data = {
                _token: '{{ csrf_token() }}',
                products: payload,
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
                url: isEdit ?
                    '{{ route('outbound.packaging-list.update') }}' :
                    '{{ route('outbound.packaging-list.store') }}',
                method: 'POST',
                data: data,
                success: (res) => {
                    if (res?.status) {
                        Swal.fire({
                                title: 'Success',
                                text: res?.message || 'Saved successfully',
                                icon: 'success'
                            })
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