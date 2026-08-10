@php
    $isEdit = isset($pendingOutbound);
@endphp
@extends('layout.index')
@section('title', $isEdit ? 'Edit Pending Outbound' : 'Create Pending Outbound')
@section('sizeBarSize', 'sm')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $isEdit ? 'Edit Pending Outbound' : 'Create Pending Outbound' }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Outbound</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('outbound.pending.index') }}">Pending List</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Data Pending Outbound</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Customer</label>
                            <select class="form-control select2Customer" id="customerId">
                                <option value="">-- Select Customer --</option>
                                @foreach ($customer as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $isEdit && $pendingOutbound->customer_id == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Location</label>
                            <input type="text" class="form-control" id="delivLocation"
                                value="{{ $isEdit ? $pendingOutbound->deliv_loc : '' }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Destination</label>
                            <select class="form-control" id="deliveryDest">
                                <option value="client" {{ $isEdit && $pendingOutbound->deliv_dest == 'client' ? 'selected' : '' }}>Client</option>
                                <option value="general room" {{ $isEdit && $pendingOutbound->deliv_dest == 'general room' ? 'selected' : '' }}>General Room</option>
                                <option value="pm room" {{ $isEdit && $pendingOutbound->deliv_dest == 'pm room' ? 'selected' : '' }}>PM Room</option>
                                <option value="spare room" {{ $isEdit && $pendingOutbound->deliv_dest == 'spare room' ? 'selected' : '' }}>Spare Room</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Date</label>
                            <input type="datetime-local" class="form-control" id="deliveryDate"
                                value="{{ $isEdit && $pendingOutbound->delivery_date ? $pendingOutbound->delivery_date->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Note Number</label>
                            <input type="text" class="form-control" id="deliveryNoteNumber"
                                placeholder="Delivery Note Number"
                                value="{{ $isEdit ? $pendingOutbound->delivery_note_number : '' }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">NTT DN</label>
                            <input type="text" class="form-control" id="nttDn" placeholder="NTT DN - Optional"
                                value="{{ $isEdit ? $pendingOutbound->ntt_dn : '' }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Jumlah Koli</label>
                            <input type="number" class="form-control" id="koli" placeholder="Jumlah Koli - Optional"
                                value="{{ $isEdit ? $pendingOutbound->koli : '' }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" id="note" rows="3" placeholder="Note - Optional">{{ $isEdit ? $pendingOutbound->note : '' }}</textarea>
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
                                <input type="hidden" id="pendingId" value="{{ $pendingOutbound->id }}">
                            @endif
                            <a class="btn btn-warning btn-sm me-1" onclick="savePending()">
                                <i class="mdi mdi-content-save"></i>
                                {{ $isEdit ? 'Update Pending' : 'Save as Pending' }}
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
                                <th>
                                    <select class="form-control" id="changeMassQTY" onchange="changeMassQTY(this.value)">
                                        <option value="0">-- Choose QTY --</option>
                                        <option>1</option><option>2</option><option>3</option><option>4</option><option>5</option>
                                        <option>6</option><option>7</option><option>8</option><option>9</option><option>10</option>
                                        <option>11</option><option>12</option><option>13</option><option>14</option><option>15</option>
                                    </select>
                                </th>
                                <th></th>
                            </tr>
                            <tr>
                                <th>Material</th>
                                <th>Item</th>
                                <th class="text-center">Type</th>
                                <th>Box</th>
                                <th>Sales Doc</th>
                                <th class="text-center">QTY</th>
                                <th>QTY Pending</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="listProductOutbound">
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
        // ====================== IndexedDB ======================
        const db = new Dexie('PendingOutboundDB');
        db.version(1).stores({ kv: 'key' });

        async function kvSet(key, value) { return db.kv.put({ key, value }); }
        async function kvGet(key, fallback = null) {
            const row = await db.kv.get(key);
            return row ? row.value : fallback;
        }
        async function kvDelete(key) { return db.kv.delete(key); }

        function normalizeSalesDocs(raw) {
            if (raw == null) return [];
            if (Array.isArray(raw)) return raw.filter(v => v != null && v !== '').map(String);
            if (typeof raw === 'object') {
                try { return Object.values(raw).filter(v => v != null && v !== '').map(String); } catch { return []; }
            }
            if (typeof raw === 'number' || typeof raw === 'boolean') return [String(raw)];
            let s = String(raw).trim();
            if (!s) return [];
            if ((s.startsWith('[') && s.endsWith(']')) || (s.startsWith('"') && s.endsWith('"'))) {
                try { const p = JSON.parse(s); return Array.isArray(p) ? p.filter(v => v != null && v !== '').map(String) : [String(p)]; } catch {}
            }
            if (s.startsWith('{') && s.endsWith('}')) {
                try { const p = JSON.parse(s); if (typeof p === 'object' && !Array.isArray(p)) return Object.values(p).filter(v => v != null && v !== '').map(String); return [String(p)]; } catch {}
            }
            if (s.includes(',')) return s.split(',').map(x => x.trim()).filter(v => v).map(String);
            return [s];
        }

        function reinitSalesDocTable() {
            const $tbl = $('#tabelSalesDoc');
            if ($.fn.DataTable && $.fn.DataTable.isDataTable($tbl)) $tbl.DataTable().destroy();
            if ($.fn.DataTable) $tbl.DataTable({ pageLength: 10, responsive: true, autoWidth: false });
        }

        $(document).ready(async function() {
            $('.select2Customer').select2({ placeholder: '-- Select Customer --', allowClear: true, width: '100%' });
            try {
                await kvSet('salesDoc', @json($salesDoc));
            } catch (e) { console.error('Gagal simpan salesDoc:', e); }
            await viewSalesDoc();

            @if ($isEdit)
                // Pre-fill products from existing pending data
                try {
                    const existingProducts = @json($pendingOutbound->details);
                    const products = [];
                    existingProducts.forEach((detail) => {
                        products.push({
                            inventoryPackageId: null,
                            inventoryPackageItemId: detail.inventory_package_item_id,
                            purchaseOrderId: detail.purchase_order_detail?.purchase_order_id ?? null,
                            purchaseOrderDetailId: detail.purchase_order_detail_id,
                            isParent: 0,
                            directOutbound: 0,
                            qty: detail.qty,
                            qtySelect: detail.qty,
                            productId: detail.product_id,
                            material: detail.material,
                            poItemDesc: detail.po_item_desc,
                            prodHierarchyDesc: '',
                            salesDoc: detail.sales_doc,
                            purcDoc: '{{ $pendingOutbound->purc_doc }}',
                            serialNumber: [],
                            number: '-',
                            reffNumber: '-',
                            loc: '-',
                            storageId: 1,
                            disable: 0,
                            item: detail.item
                        });
                    });
                    await kvSet('salesDocProduct', products);
                    await viewProductOutbound();
                    console.log('Pre-filled ' + products.length + ' products from pending #{{ $pendingOutbound->id }}');
                } catch (e) {
                    console.error('Failed to pre-fill products:', e);
                }
            @endif
        });

        async function viewSalesDoc() {
            const salesDoc = await kvGet('salesDoc', []) ?? [];
            let html = '', number = 1;
            salesDoc.forEach((item) => {
                const docs = normalizeSalesDocs(item?.sales_docs);
                let salesDocHtml = docs.map(d => `<div>${d}</div>`).join('');
                let storage = item?.storage ? `${item.storage.raw ?? ''} - ${item.storage.area ?? ''} - ${item.storage.rak ?? ''} - ${item.storage.bin ?? ''}` : '';
                if (parseInt(item?.storage?.id) === 1) storage = 'Cross Docking';
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
                    </tr>`;
                number++;
            });
            $('#listSalesDoc').html(html);
            reinitSalesDocTable();
        }

        window.pilihSalesDoc = function pilihSalesDoc(id) {
            $.ajax({
                url: '{{ route('outbound.sales-doc') }}',
                method: 'GET',
                data: { id },
                success: async (res) => {
                    try {
                        const products = [];
                        (res?.data?.inventory_package_item ?? []).forEach((product) => {
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
                                serialNumber: [],
                                number: res?.data?.number,
                                reffNumber: res?.data?.reff_number,
                                loc: `${res?.data?.storage?.raw ?? ''}-${res?.data?.storage?.area ?? ''}-${res?.data?.storage?.rak ?? ''}-${res?.data?.storage?.bin ?? ''}`,
                                storageId: res?.data?.storage?.id,
                                disable: 0,
                                item: product.purchase_order_detail.item
                            });
                        });
                        await kvSet('salesDocProduct', products);
                        await viewProductOutbound();
                    } catch (e) { console.error('Gagal proses pilihSalesDoc:', e); }
                },
                error: () => Swal.fire({ title: 'Error', text: 'Request gagal', icon: 'error' })
            });
        };

        async function viewProductOutbound() {
            const products = await kvGet('salesDocProduct', []) ?? [];
            let html = '';
            products.forEach((item, index) => {
                if (parseInt(item?.disable) === 0) {
                    html += `
                        <tr>
                            <td>
                                ${parseInt(item?.directOutbound) === 1 ? '<div><span class="badge bg-danger"> Cross Docking </span></div>' : ''}
                                <div>${item?.material ?? ''}</div>
                                <div>${item?.poItemDesc ?? ''}</div>
                                <div>${item?.prodHierarchyDesc ?? ''}</div>
                            </td>
                            <td>${item.item}</td>
                            <td class="text-center">
                                ${parseInt(item?.isParent) === 1 ? '<span class="badge bg-danger-subtle text-danger">Parent</span>' : '<span class="badge bg-secondary-subtle text-secondary">Child</span>'}
                            </td>
                            <td>
                                <div><b>PA: </b>${item?.number ?? ''}</div>
                                <div><b>Box: </b>${item?.reffNumber ?? ''}</div>
                                <div><b>Loc: </b>${item?.loc ?? ''}</div>
                            </td>
                            <td>${item?.salesDoc ?? ''}</td>
                            <td class="text-center fw-bold">${item?.qty ?? 0}</td>
                            <td><input type="number" class="form-control" onchange="changeQtySelect(${index}, this.value)" value="${item?.qtySelect ?? 0}"></td>
                            <td><a class="btn btn-danger btn-sm" onclick="deleteProduct(${index})">Delete</a></td>
                        </tr>`;
                }
            });
            $('#listProductOutbound').html(html);
        }

        async function changeMassQTY(value) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            const qtyValue = parseInt(value);
            for (const item of products) {
                if (parseInt(item?.disable) === 0) {
                    if (qtyValue > parseInt(item.qty)) {
                        Swal.fire({ icon: 'error', title: 'Qty tidak valid', text: 'Qty yang dipilih melebihi stok tersedia' });
                        return;
                    }
                    item.qtySelect = qtyValue;
                }
            }
            await kvSet('salesDocProduct', products);
            await viewProductOutbound();
        }

        window.deleteProduct = async function deleteProduct(index) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            if (!products[index]) return;
            products[index].disable = 1;
            await kvSet('salesDocProduct', products);
            await viewProductOutbound();
        };

        window.changeQtySelect = async function changeQtySelect(index, value) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            if (!products[index]) return;
            const newVal = parseInt(value || 0);
            if (newVal > parseInt(products[index]?.qty || 0)) {
                Swal.fire({ title: 'Warning!', text: 'QTY pending melebihi qty di inventory', icon: 'warning' });
                await viewProductOutbound();
                return;
            }
            products[index].qtySelect = newVal;
            await kvSet('salesDocProduct', products);
            await viewProductOutbound();
        };

        window.savePending = async function savePending() {
            const products = await kvGet('salesDocProduct', []) ?? [];
            const hasProduct = products.some(p => parseInt(p?.disable) === 0 && parseInt(p?.qtySelect || 0) > 0);
            if (!hasProduct) {
                Swal.fire({ title: 'Warning!', text: 'Please select at least one product with qty > 0', icon: 'warning' });
                return;
            }

            const isEdit = $('#pendingId').length > 0;
            const t = await Swal.fire({
                title: isEdit ? "Update Pending?" : "Save as Pending?",
                text: isEdit ? "Pending outbound data will be updated." : "Data will be saved to the pending outbound list",
                icon: "info",
                showCancelButton: true,
                confirmButtonText: isEdit ? "Yes, Update it!" : "Yes, Save it!",
                cancelButtonText: "No",
                buttonsStyling: false,
                showCloseButton: true
            });
            if (!t.value) return;

            const payload = products.map(p => {
                const { dataSN, serialNumber, ...rest } = p;
                return rest;
            });

            const url = isEdit
                ? '{{ route('outbound.pending.update') }}'
                : '{{ route('outbound.pending.store') }}';
            const data = {
                _token: '{{ csrf_token() }}',
                products: payload,
                delivLocation: $('#delivLocation').val(),
                customerId: $('#customerId').val(),
                deliveryDest: $('#deliveryDest').val(),
                deliveryDate: $('#deliveryDate').val() ?? '',
                deliveryNoteNumber: $('#deliveryNoteNumber').val() ?? '',
                nttDn: $('#nttDn').val() ?? '',
                koli: $('#koli').val() ?? '',
                note: $('#note').val() ?? '',
            };
            if (isEdit) {
                data.pending_id = $('#pendingId').val();
            }

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: (res) => {
                    if (res?.status) {
                        Swal.fire({
                            title: 'Success',
                            text: isEdit ? 'Pending outbound updated successfully' : 'Pending outbound saved successfully',
                            icon: 'success'
                        }).then(() => { window.location.href = '{{ route('outbound.pending.index') }}'; });
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
