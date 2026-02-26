@extends('layout.index')
@section('title', 'Create Order')
@section('sizeBarSize', 'sm')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Create Order</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Outbound</a></li>
                        <li class="breadcrumb-item">Order List</li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Data Order</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Customer</label>
                            <select class="form-control" id="customerId">
                                <option value="">-- Select Customer --</option>
                                @foreach ($customer as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Location</label>
                            <input type="text" class="form-control" id="delivLocation">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Destination</label>
                            <select class="form-control" id="deliveryDest">
                                <option value="client">Client</option>
                                <option value="general room">General Room</option>
                                <option value="pm room">PM Room</option>
                                <option value="spare room">Spare Room</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Date</label>
                            <input type="datetime-local" class="form-control" id="deliveryDate">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Delivery Note Number</label>
                            <input type="text" class="form-control" id="deliveryNoteNumber"
                                placeholder="Delivery Note Number">
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
                        <h4 class="card-title mb-0">List Product Order</h4>
                        <a class="btn btn-primary" onclick="createOrder()">Create Order</a>
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
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4</option>
                                        <option>5</option>
                                        <option>6</option>
                                        <option>7</option>
                                        <option>8</option>
                                        <option>9</option>
                                        <option>10</option>
                                        <option>11</option>
                                        <option>12</option>
                                        <option>13</option>
                                        <option>14</option>
                                        <option>15</option>
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
                                <th>QTY Outbound</th>
                                <th>Serial Number</th>
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

    <!-- Serial Number Modals -->
    <div id="serialNumberModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Serial Number Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idModal">
                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-2">Data Serial Number</h4>
                                <a class="btn btn-danger btn-sm" onclick="pilihSemuaSN()">Pilih Semua SN</a>
                            </div>
                            <div class="mb-2">
                                <input type="text" class="form-control" id="searchSN" placeholder="Search Serial Number"
                                    onkeyup="filterSN()">
                            </div>
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Serial Number</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="listDataSN">

                                </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <h4 class="card-title mb-2">Data Outbound Serial Number</h4>
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Serial Number</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="listDataOutboundSN">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Dexie (IndexedDB) -->
    <script src="https://unpkg.com/dexie@3/dist/dexie.min.js"></script>

    <script>
        // ====================== IndexedDB (Dexie) ======================
        const db = new Dexie('OutboundDB');
        db.version(1).stores({
            kv: 'key'
        }); // simple key-value store

        async function kvSet(key, value) {
            return db.kv.put({
                key,
                value
            });
        }
        async function kvGet(key, fallback = null) {
            const row = await db.kv.get(key);
            return row ? row.value : fallback;
        }
        async function kvDelete(key) {
            return db.kv.delete(key);
        }

        // ====================== Helpers ======================
        // Normalisasi sales_docs agar SELALU array of string
        function normalizeSalesDocs(raw) {
            if (raw == null) return []; // null/undefined
            if (Array.isArray(raw)) return raw.filter(v => v != null && v !== '').map(String);

            // jika object biasa → coba ambil values
            if (typeof raw === 'object') {
                try {
                    return Object.values(raw).filter(v => v != null && v !== '').map(String);
                } catch {
                    return [];
                }
            }

            // jika number/boolean → jadikan array dgn 1 elemen string
            if (typeof raw === 'number' || typeof raw === 'boolean') {
                return [String(raw)];
            }

            // sekarang pasti string
            let s = String(raw).trim();
            if (!s) return [];

            // kalau string tampak seperti JSON array → parse aman
            if ((s.startsWith('[') && s.endsWith(']')) || (s.startsWith('"') && s.endsWith('"'))) {
                try {
                    const parsed = JSON.parse(s);
                    if (Array.isArray(parsed)) return parsed.filter(v => v != null && v !== '').map(String);
                    return [String(parsed)];
                } catch {
                    // lanjut ke pola lain
                }
            }

            // kalau string mengandung koma → split
            if (s.includes(',')) {
                return s.split(',').map(x => x.trim()).filter(v => v).map(String);
            }

            // fallback: satu string saja
            return [s];
        }

        function reinitSalesDocTable() {
            const $tbl = $('#tabelSalesDoc');
            if ($.fn.DataTable && $.fn.DataTable.isDataTable($tbl)) {
                $tbl.DataTable().destroy();
            }
            if ($.fn.DataTable) {
                $tbl.DataTable({
                    pageLength: 10,
                    responsive: true,
                    autoWidth: false
                });
            }
        }

        // ====================== Boot ======================
        $(document).ready(async function() {
            // simpan data server ke IndexedDB
            try {
                const salesDocServer = @json($salesDoc);
                await kvSet('salesDoc', salesDocServer);
            } catch (e) {
                console.error('Gagal simpan salesDoc ke IndexedDB:', e);
            }
            await viewSalesDoc(); // render awal
        });

        // ====================== VIEW: SalesDoc ======================
        async function viewSalesDoc() {
            const salesDoc = await kvGet('salesDoc', []) ?? [];
            let html = '';
            let number = 1;

            salesDoc.forEach((item) => {
                const docs = normalizeSalesDocs(item?.sales_docs);
                let salesDocHtml = '';
                docs.forEach((detail) => {
                    salesDocHtml += `<div>${detail}</div>`;
                });

                let storage = '';
                if (item?.storage) {
                    storage =
                        `${item.storage.raw ?? ''} - ${item.storage.area ?? ''} - ${item.storage.rak ?? ''} - ${item.storage.bin ?? ''}`;
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

            // sinkronkan DataTables
            reinitSalesDocTable();
        }

        // ====================== ACTIONS: Products & SN ======================
        window.pilihSalesDoc = function pilihSalesDoc(id) {
            $.ajax({
                url: '{{ route('outbound.sales-doc') }}',
                method: 'GET',
                data: {
                    id
                },
                success: async (res) => {
                    try {
                        const products = [];
                        (res?.data?.inventory_package_item ?? []).forEach((product) => {
                            const dataSN = [];
                            (product?.inventory_package_item_sn ?? []).forEach((sn) => {
                                if (parseInt(sn?.qty) !== 0) {
                                    dataSN.push({
                                        id: sn.id,
                                        serialNumber: sn.serial_number,
                                        select: 0
                                    });
                                }
                            });

                            products.push({
                                inventoryPackageId: product.inventory_package_id,
                                inventoryPackageItemId: product.id,
                                purchaseOrderId: product?.purchase_order_detail
                                    ?.purchase_order_id,
                                purchaseOrderDetailId: product?.purchase_order_detail_id,
                                isParent: product?.is_parent,
                                directOutbound: product?.direct_outbound,
                                qty: product?.qty,
                                qtySelect: 0,
                                productId: product?.product_id,
                                material: product?.purchase_order_detail?.material,
                                poItemDesc: product?.purchase_order_detail?.po_item_desc,
                                prodHierarchyDesc: product?.purchase_order_detail
                                    ?.prod_hierarchy_desc,
                                salesDoc: product?.purchase_order_detail?.sales_doc,
                                purcDoc: res?.data?.purchase_order?.purc_doc,
                                dataSN: dataSN,
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
                    } catch (e) {
                        console.error('Gagal proses pilihSalesDoc:', e);
                        Swal.fire({
                            title: 'Error',
                            text: 'Gagal memproses data',
                            icon: 'error'
                        });
                    }
                },
                error: () => Swal.fire({
                    title: 'Error',
                    text: 'Request gagal',
                    icon: 'error'
                })
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
                                ${ parseInt(item?.directOutbound) === 1 ? '<div><span class="badge bg-danger"> Cross Docking </span></div>' : '' }
                                <div>${item?.material ?? ''}</div>
                                <div>${item?.poItemDesc ?? ''}</div>
                                <div>${item?.prodHierarchyDesc ?? ''}</div>
                            </td>
                            <td>${item.item}</td>
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
                            <td><input type="number" class="form-control" onchange="changeQtySelect(${index}, this.value)" value="${item?.qtySelect ?? 0}"></td>
                            <td>
                                <a class="btn ${(parseInt(item?.qtySelect || 0) > 0 && (item?.serialNumber || []).length === parseInt(item?.qtySelect)) ? 'btn-success' : 'btn-info'} btn-sm" onclick="openSerialNumberModal(${index})">Serial Number</a>
                            </td>
                            <td><a class="btn btn-danger btn-sm" onclick="deleteProduct(${index})">Delete</a></td>
                        </tr>
                    `;
                }
            });

            const el = document.getElementById('listProductOutbound');
            if (el) {
                el.innerHTML = html;
            }
        }

        async function changeMassQTY(value) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            const qtyValue = parseInt(value);

            for (const item of products) {
                if (parseInt(item?.disable) === 0) {

                    if (qtyValue > parseInt(item.qty)) {
                        await Swal.fire({
                            icon: 'error',
                            title: 'Qty tidak valid',
                            text: 'Qty yang dipilih melebihi stok tersedia',
                        });
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
                Swal.fire({
                    title: 'Warning!',
                    text: 'QTY outbound melebihi qty diinventory',
                    icon: 'warning'
                });
                await viewProductOutbound();
                return;
            }

            products[index].qtySelect = newVal;
            await kvSet('salesDocProduct', products);
            await viewProductOutbound();
        };

        window.openSerialNumberModal = async function openSerialNumberModal(index) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            const product = products[index];
            if (!product) return;

            let dataSN = '';
            (product?.dataSN ?? []).forEach((item, indexSN) => {
                const canPick = parseInt(item?.select) === 0;
                dataSN += `
                    <tr>
                        <td>${item?.serialNumber ?? ''}</td>
                        <td>${ canPick ? `<a class="btn btn-info btn-sm" onclick="pilihSN(${index}, ${indexSN})">Pilih SN</a>` : ''}</td>
                    </tr>
                `;
            });

            let serialNumber = '';
            (product?.serialNumber ?? []).forEach((item, indexSN) => {
                serialNumber += `
                    <tr>
                        <td>${item?.serialNumber ?? ''}</td>
                        <td><a class="btn btn-info btn-sm" onclick="deleteSN(${index}, ${indexSN})">Delete</a></td>
                    </tr>
                `;
            });

            const elSel = document.getElementById('listDataOutboundSN');
            const elAvail = document.getElementById('listDataSN');
            if (elSel) elSel.innerHTML = serialNumber;
            if (elAvail) elAvail.innerHTML = dataSN;

            const idModal = document.getElementById('idModal');
            if (idModal) idModal.value = index;

            if (typeof $ !== 'undefined' && $('#serialNumberModal').modal) {
                $('#serialNumberModal').modal('show');
            }

            const searchInput = document.getElementById('searchSN');
            if (searchInput) {
                searchInput.value = '';
                filterSN();
            }
        };

        window.pilihSN = async function pilihSN(index, indexSN) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            const product = products[index];
            if (!product) return;

            product.dataSN[indexSN].select = 1;
            product.serialNumber = product.serialNumber || [];
            product.serialNumber.push(product.dataSN[indexSN]);

            await kvSet('salesDocProduct', products);
            await viewSerialNumberReload(index);
        };

        window.deleteSN = async function deleteSN(index, indexSN) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            const product = products[index];
            if (!product) return;

            const findSN = product.serialNumber?.[indexSN];
            if (!findSN) return;

            const findDataSN = (product.dataSN ?? []).find(item => parseInt(item?.id) === parseInt(findSN?.id));
            if (findDataSN) findDataSN.select = 0;
            product.serialNumber.splice(indexSN, 1);

            await kvSet('salesDocProduct', products);
            await viewSerialNumberReload(index);
        };

        async function viewSerialNumberReload(index) {
            const products = await kvGet('salesDocProduct', []) ?? [];
            const product = products[index];
            if (!product) return;

            let dataSN = '';
            (product?.dataSN ?? []).forEach((item, indexSN) => {
                const canPick = parseInt(item?.select) === 0;
                dataSN += `
                    <tr>
                        <td>${item?.serialNumber ?? ''}</td>
                        <td>${ canPick ? `<a class="btn btn-info btn-sm" onclick="pilihSN(${index}, ${indexSN})">Pilih SN</a>` : ''}</td>
                    </tr>
                `;
            });

            let serialNumber = '';
            (product?.serialNumber ?? []).forEach((item, indexSN) => {
                serialNumber += `
                    <tr>
                        <td>${item?.serialNumber ?? ''}</td>
                        <td><a class="btn btn-info btn-sm" onclick="deleteSN(${index}, ${indexSN})">Delete</a></td>
                    </tr>
                `;
            });

            const elSel = document.getElementById('listDataOutboundSN');
            const elAvail = document.getElementById('listDataSN');
            if (elSel) {
                elSel.innerHTML = serialNumber;
            }
            if (elAvail) {
                elAvail.innerHTML = dataSN;
            }
            filterSN();
            await viewProductOutbound();
        }

        window.filterSN = function filterSN() {
            const input = document.getElementById('searchSN');
            if (!input) return;
            const filter = input.value.toUpperCase();
            const tbody = document.getElementById("listDataSN");
            const tr = tbody.getElementsByTagName("tr");
            for (let i = 0; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        };

        window.pilihSemuaSN = async function pilihSemuaSN() {
            const index = document.getElementById('idModal')?.value;
            if (index == null) return;

            const products = await kvGet('salesDocProduct', []) ?? [];
            const product = products[index];
            if (!product) return;

            product.serialNumber = product.serialNumber || [];

            const searchInput = document.getElementById('searchSN');
            const filter = searchInput ? searchInput.value.toUpperCase() : '';

            (product?.dataSN ?? []).forEach((item) => {
                // If filter is active and item does not match, skip it
                if (filter && item.serialNumber.toUpperCase().indexOf(filter) === -1) {
                    return;
                }

                // Add to selection if not currently selected
                if (parseInt(item.select) === 0) {
                    item.select = 1;
                    product.serialNumber.push(item);
                }
            });

            await kvSet('salesDocProduct', products);
            await viewSerialNumberReload(index);
        };

        window.createOrder = async function createOrder() {
            const t = await Swal.fire({
                title: "Are you sure?",
                text: "Create Order",
                icon: "warning",
                showCancelButton: true,
                customClass: {
                    confirmButton: "btn btn-primary w-xs me-2 mt-2",
                    cancelButton: "btn btn-danger w-xs mt-2"
                },
                confirmButtonText: "Yes, Create it!",
                buttonsStyling: false,
                showCloseButton: true
            });
            if (!t.value) return;

            const products = await kvGet('salesDocProduct', []) ?? [];
            for (const product of products) {
                const q = parseInt(product?.qtySelect || 0);
                if (q !== 0 && q !== parseInt((product?.serialNumber || []).length)) {
                    await Swal.fire({
                        title: 'Warning!',
                        text: 'Select serial number as many as QTY Out',
                        icon: "warning",
                    });
                    return;
                }
            }

            $.ajax({
                url: '{{ route('outbound.store') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    products: products,
                    delivLocation: document.getElementById('delivLocation')?.value,
                    customerId: document.getElementById('customerId')?.value,
                    deliveryDest: document.getElementById('deliveryDest')?.value,
                    deliveryDate: document.getElementById('deliveryDate')?.value ?? '',
                    deliveryNoteNumber: document.getElementById('deliveryNoteNumber')?.value ?? '',
                },
                success: async (res) => {
                    if (res?.status) {
                        await Swal.fire({
                            title: 'Success',
                            text: 'Create Order Successfully',
                            icon: 'success'
                        });
                        window.location.href = '{{ route('outbound.index') }}';
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Create Order Failed',
                            icon: 'error'
                        });
                    }
                },
                error: () => Swal.fire({
                    title: 'Error',
                    text: 'Request gagal',
                    icon: 'error'
                })
            });
        };
    </script>
@endsection
