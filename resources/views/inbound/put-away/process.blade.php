@extends('layout.index')
@section('title', 'Put Away')

@section('content')
    <style>
        .material-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border-left: 4px solid transparent;
            margin-bottom: 8px;
        }

        .material-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .material-card.active {
            border-left-color: #405189;
            background-color: #f3f6f9;
        }

        .sn-list-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #e9ebec;
            border-radius: 4px;
        }

        .storage-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f3f6f9;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }

        .storage-item:hover {
            background-color: #f8f9fa;
            color: #405189;
        }

        .storage-item.selected {
            background-color: #e2e5ff;
            border-left: 4px solid #405189;
            font-weight: 600;
            color: #405189;
        }

        .staging-area {
            min-height: 250px;
            background-color: #fafafa;
            border: 2px dashed #e9ebec;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .staging-area.has-items {
            border-style: solid;
            background-color: white;
        }

        .badge-soft-primary {
            background-color: rgba(64, 81, 137, 0.1);
            color: #405189;
        }

        .sn-checkbox-item {
            padding: 10px 15px;
            border-bottom: 1px solid #f3f6f9;
            transition: background 0.2s;
        }

        .sn-checkbox-item:hover {
            background-color: #f8f9fa;
        }

        .sn-checkbox-item:last-child {
            border-bottom: none;
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #878a99;
        }

        .search-box input {
            padding-left: 35px;
        }

        .sticky-top-card {
            position: sticky;
            top: 70px;
            z-index: 10;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Put Away Process</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item active">Put Away Process</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- LEFT COLUMN: Available Materials -->
        <div class="col-xl-5 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header border-0 bg-white pt-4 px-4 pb-0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0 fw-bold">Available Materials</h5>
                        <span class="badge badge-soft-primary px-3 py-2 rounded-pill" id="totalAvailableSN">0 SN
                            Available</span>
                    </div>
                    <div class="search-box mb-3">
                        <input type="text" class="form-control border-light-subtle bg-light" id="searchMaterial"
                            placeholder="Search material, serial, or sales doc...">
                        <i class="ri-search-line"></i>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="materialList" class="px-4 pb-4" style="max-height: calc(100vh - 300px); overflow-y: auto;">
                        <!-- Materials will be loaded here -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Storage & Staging -->
        <div class="col-xl-7 col-lg-6">
            <div class="sticky-top-card">
                <!-- TOP CARD: Storage Selection -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-0 bg-white pt-4 px-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0 fw-bold">1. Select Storage Location</h5>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Raw</label>
                                <select class="form-select form-select-sm border-light-subtle bg-light" id="selectRaw"
                                    onchange="onRawChange()">
                                    <option value="">-- Raw --</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Area</label>
                                <select class="form-select form-select-sm border-light-subtle bg-light" id="selectArea"
                                    onchange="onAreaChange()" disabled>
                                    <option value="">-- Area --</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Rak</label>
                                <select class="form-select form-select-sm border-light-subtle bg-light" id="selectRak"
                                    onchange="onRakChange()" disabled>
                                    <option value="">-- Rak --</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Bin</label>
                                <select class="form-select form-select-sm border-light-subtle bg-light" id="selectBin"
                                    onchange="onBinChange()" disabled>
                                    <option value="">-- Bin --</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 p-2 bg-light rounded text-center" id="storageDisplayInfo">
                            <small class="text-muted"><i class="ri-information-line"></i> Please select all location
                                components</small>
                        </div>

                        <div class="mt-3">
                            <button class="btn btn-success px-4 py-2 fw-medium w-100 shadow-sm" id="btnAssignToStorage"
                                onclick="assignToStorage()" disabled>
                                <i class="ri-download-2-line me-1"></i> Assign to this Bin
                            </button>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM CARD: Put Away Staging -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-0 bg-white pt-4 px-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0 fw-bold">2. Put Away Staging</h5>
                            <div>
                                <button class="btn btn-outline-danger btn-sm border-0" onclick="clearStaging()"><i
                                        class="ri-delete-bin-line"></i> Clear</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div id="stagingList" class="staging-area p-3 h-100">
                            <div class="text-center text-muted py-5" id="emptyStagingMsg">
                                <i class="ri-inbox-archive-line display-4 text-light"></i>
                                <p class="mt-2 mb-0">No items staged yet.</p>
                                <small>Select SNs on the left and a Storage above to begin.</small>
                            </div>
                            <!-- Staged items grouped by Storage will be here -->
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-primary btn-lg w-100 fw-bold shadow transition-all"
                                id="btnConfirmPutAway" onclick="processPutAway()" disabled>
                                <i class="ri-checkbox-circle-line me-1"></i> Confirm Put Away
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SN Selection Modal -->
    <div id="materialSnModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Select Serial Numbers</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                        <div class="overflow-hidden">
                            <h6 class="mb-0 fw-bold text-truncate" id="modalMaterialName">Material Name</h6>
                            <small class="text-muted d-block text-truncate" id="modalMaterialDesc">Description</small>
                        </div>
                        <div class="form-check form-switch ps-5">
                            <input class="form-check-input" type="checkbox" id="selectAllSn">
                            <label class="form-check-label fw-medium" for="selectAllSn">All</label>
                        </div>
                    </div>
                    <div class="p-2 border-bottom">
                        <div class="search-box">
                            <input type="text" class="form-control form-control-sm border-light-subtle bg-light"
                                id="searchSn" placeholder="Search serial number...">
                            <i class="ri-search-line"></i>
                        </div>
                    </div>
                    <div class="sn-list-container" id="modalSnList">
                        <!-- SNs for selected material -->
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-muted fw-medium"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold" onclick="confirmSnSelection()">Set
                        Selected</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Data management
        let masterData = [];
        let storages = [];
        let staging = [];
        let currentMaterialIndex = -1;
        let selectedStorageId = null;

        $(document).ready(function() {
            localStorage.clear();
            initData();
            renderMaterials();
            populateRaw(); // Added for cascading selects
            updateStats();

            // Search listeners
            $('#searchMaterial').on('keyup', function() {
                renderMaterials($(this).val());
            });

            $('#selectAllSn').on('change', function() {
                $('.modal-sn-check:visible').prop('checked', $(this).is(':checked'));
            });

            $('#searchSn').on('keyup', function() {
                renderSnList($(this).val());
            });
        });

        function initData() {
            const products = @json($products);
            const storageItems = @json($storage);

            masterData = products.map((p, index) => {
                const sns = p.product_package_item_sn.map(sn => ({
                    serialNumber: sn.serial_number,
                    status: sn.status ?? 0,
                    selected: false
                }));

                return {
                    id: p.id,
                    purchaseOrderDetailId: p.purchase_order_detail_id,
                    productId: p.product_id,
                    material: p.purchase_order_detail.material,
                    desc: p.purchase_order_detail.po_item_desc,
                    type: p.prod_hierarchy_desc,
                    salesDoc: p.purchase_order_detail.sales_doc,
                    item: p.purchase_order_detail.item,
                    isParent: p.is_parent,
                    totalQty: p.qty,
                    qtyPaDb: p.qty_pa ?? 0,
                    sns: sns,
                    availableQty: sns.filter(s => s.status == 0).length
                };
            });

            storages = storageItems.map(s => ({
                id: s.id,
                raw: s.raw,
                area: s.area,
                rak: s.rak,
                bin: s.bin,
                name: `${s.raw} | ${s.area} | ${s.rak} | ${s.bin}`
            }));
        }

        // --- CASCADING SELECT LOGIC ---
        function populateRaw() {
            const raws = [...new Set(storages.map(s => s.raw))].sort();
            const select = $('#selectRaw');
            raws.forEach(r => select.append(`<option value="${r}">${r}</option>`));
        }

        function onRawChange() {
            const raw = $('#selectRaw').val();
            const selectArea = $('#selectArea');
            const selectRak = $('#selectRak');
            const selectBin = $('#selectBin');

            selectArea.html('<option value="">-- Area --</option>').prop('disabled', !raw);
            selectRak.html('<option value="">-- Rak --</option>').prop('disabled', true);
            selectBin.html('<option value="">-- Bin --</option>').prop('disabled', true);
            $('#btnAssignToStorage').prop('disabled', true);
            $('#storageDisplayInfo').html('<small class="text-muted">Please select Area</small>');

            if (raw) {
                const areas = [...new Set(storages.filter(s => s.raw === raw).map(s => s.area))].sort();
                areas.forEach(a => selectArea.append(`<option value="${a}">${a}</option>`));
            }
        }

        function onAreaChange() {
            const raw = $('#selectRaw').val();
            const area = $('#selectArea').val();
            const selectRak = $('#selectRak');
            const selectBin = $('#selectBin');

            selectRak.html('<option value="">-- Rak --</option>').prop('disabled', !area);
            selectBin.html('<option value="">-- Bin --</option>').prop('disabled', true);
            $('#btnAssignToStorage').prop('disabled', true);
            $('#storageDisplayInfo').html('<small class="text-muted">Please select Rak</small>');

            if (area) {
                const raks = [...new Set(storages.filter(s => s.raw === raw && s.area === area).map(s => s.rak))].sort();
                raks.forEach(r => selectRak.append(`<option value="${r}">${r}</option>`));
            }
        }

        function onRakChange() {
            const raw = $('#selectRaw').val();
            const area = $('#selectArea').val();
            const rak = $('#selectRak').val();
            const selectBin = $('#selectBin');

            selectBin.html('<option value="">-- Bin --</option>').prop('disabled', !rak);
            $('#btnAssignToStorage').prop('disabled', true);
            $('#storageDisplayInfo').html('<small class="text-muted">Please select Bin</small>');

            if (rak) {
                const bins = [...new Set(storages.filter(s => s.raw === raw && s.area === area && s.rak === rak).map(s => s
                    .bin))].sort();
                bins.forEach(b => selectBin.append(`<option value="${b}">${b}</option>`));
            }
        }

        function onBinChange() {
            const raw = $('#selectRaw').val();
            const area = $('#selectArea').val();
            const rak = $('#selectRak').val();
            const bin = $('#selectBin').val();

            if (bin) {
                const storage = storages.find(s => s.raw === raw && s.area === area && s.rak === rak && s.bin === bin);
                if (storage) {
                    selectedStorageId = storage.id;
                    $('#storageDisplayInfo').html(
                        `<div class="text-primary fw-bold small"><i class="ri-check-line"></i> ${storage.name}</div>`);
                    $('#btnAssignToStorage').prop('disabled', false);
                }
            } else {
                $('#btnAssignToStorage').prop('disabled', true);
            }
        }
        // --- END CASCADING SELECT LOGIC ---

        function renderMaterials(filter = '') {
            let html = '';
            masterData.forEach((m, index) => {
                const searchStr = `${m.material} ${m.desc} ${m.salesDoc} ${m.sns.map(s=>s.serialNumber).join(' ')}`
                    .toLowerCase();
                if (filter && !searchStr.includes(filter.toLowerCase())) {
                    return;
                }

                if (m.availableQty <= 0) return;

                const selectedCount = m.sns.filter(s => s.selected).length;

                html += `
                    <div class="card material-card shadow-none border ${currentMaterialIndex === index ? 'active' : ''}" onclick="openSnSelector(${index})">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="mb-1 fw-bold text-primary text-truncate">${m.material}</h6>
                                    <p class="text-muted mb-1 small text-truncate">${m.desc}</p>
                                    <div class="d-flex gap-2 align-items-center">
                                        <span class="badge ${m.isParent ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary'}">${m.isParent ? 'Parent' : 'Child'}</span>
                                        <span class="small text-muted text-truncate"><i class="ri-file-list-3-line"></i> ${m.salesDoc}</span>
                                    </div>
                                </div>
                                <div class="text-end ms-2">
                                    <span class="badge bg-light text-dark border-light-subtle">${m.availableQty} Avail</span>
                                    ${selectedCount > 0 ? `<div class="mt-2"><span class="badge bg-success shadow-sm">${selectedCount} Selected</span></div>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            if (html === '') {
                html = `
                    <div class="text-center py-5">
                        <i class="ri-search-2-line display-6 text-muted-em"></i>
                        <p class="text-muted mt-2">No matching materials found</p>
                    </div>
                `;
            }
            $('#materialList').html(html);
        }

        function openSnSelector(index) {
            currentMaterialIndex = index;
            const m = masterData[index];

            $('#modalMaterialName').text(m.material);
            $('#modalMaterialDesc').text(m.desc);
            $('#searchSn').val(''); // Reset search when opening

            renderSnList();

            $('#materialSnModal').modal('show');
        }

        function renderSnList(filter = '') {
            const m = masterData[currentMaterialIndex];
            let html = '';
            let visibleCount = 0;

            m.sns.forEach((sn, snIndex) => {
                if (sn.status != 0) return;

                if (filter && !sn.serialNumber.toLowerCase().includes(filter.toLowerCase())) {
                    return;
                }

                visibleCount++;
                html += `
                    <div class="sn-checkbox-item">
                        <div class="form-check w-100">
                            <input class="form-check-input modal-sn-check pointer" type="checkbox" value="${snIndex}" id="sn_${currentMaterialIndex}_${snIndex}" ${sn.selected ? 'checked' : ''}>
                            <label class="form-check-label w-100 ps-2 mb-0 pointer" for="sn_${currentMaterialIndex}_${snIndex}">
                                ${sn.serialNumber}
                            </label>
                        </div>
                    </div>
                `;
            });

            if (visibleCount === 0) {
                html = '<div class="p-4 text-center text-muted small">No SN found</div>';
            }

            $('#modalSnList').html(html);
            $('#selectAllSn').prop('checked', m.sns.filter(s => s.status == 0 && !s.selected).length === 0);
        }

        function confirmSnSelection() {
            const m = masterData[currentMaterialIndex];

            m.sns.forEach(s => {
                if (s.status == 0) s.selected = false;
            });

            $('.modal-sn-check:checked').each(function() {
                const snIndex = $(this).val();
                m.sns[snIndex].selected = true;
            });

            renderMaterials($('#searchMaterial').val());
            $('#materialSnModal').modal('hide');
            updateStats();
        }

        function updateStats() {
            let total = 0;
            masterData.forEach(m => {
                total += m.sns.filter(s => s.status == 0).length;
            });
            $('#totalAvailableSN').text(total + ' SN Available');
        }

        function assignToStorage() {
            if (selectedStorageId === null) {
                Swal.fire({
                    title: 'Storage Required',
                    text: 'Please select a storage location using the 4 dropdowns above.',
                    icon: 'warning',
                    confirmButtonColor: '#405189'
                });
                return;
            }

            const selectedItems = [];
            masterData.forEach(m => {
                const selectedSns = m.sns.filter(s => s.selected && s.status == 0).map(s => s.serialNumber);
                if (selectedSns.length > 0) {
                    selectedItems.push({
                        materialIndex: masterData.indexOf(m),
                        material: m.material,
                        desc: m.desc,
                        salesDoc: m.salesDoc,
                        item: m.item,
                        sns: selectedSns,
                        detail: m
                    });
                }
            });

            if (selectedItems.length === 0) {
                Swal.fire({
                    title: 'No Items Selected',
                    text: 'Please select Serial Numbers from the materials list on the left.',
                    icon: 'warning',
                    confirmButtonColor: '#405189'
                });
                return;
            }

            const storage = storages.find(s => s.id === selectedStorageId);
            let stageGroup = staging.find(st => st.storageId === selectedStorageId);

            if (!stageGroup) {
                stageGroup = {
                    storageId: selectedStorageId,
                    storageName: storage.name,
                    items: []
                };
                staging.push(stageGroup);
            }

            selectedItems.forEach(item => {
                let existingItem = stageGroup.items.find(si => si.material === item.material && si.salesDoc === item
                    .salesDoc);
                if (existingItem) {
                    existingItem.sns = Array.from(new Set([...existingItem.sns, ...item.sns]));
                } else {
                    stageGroup.items.push({
                        ...item
                    });
                }

                item.detail.sns.forEach(s => {
                    if (item.sns.includes(s.serialNumber)) {
                        s.status = 2; // Temporary staged status
                        s.selected = false;
                    }
                });
                item.detail.availableQty = item.detail.sns.filter(s => s.status == 0).length;
            });

            renderMaterials($('#searchMaterial').val());
            renderStaging();
            updateStats();

            Toast.fire({
                icon: 'success',
                title: 'Assigned to storage'
            });
        }

        function renderStaging() {
            const btnConfirm = $('#btnConfirmPutAway');
            const stagingList = $('#stagingList');

            if (staging.length === 0) {
                stagingList.removeClass('has-items');
                stagingList.html(`
                    <div class="text-center text-muted py-5" id="emptyStagingMsg">
                        <i class="ri-inbox-archive-line display-4 text-light"></i>
                        <p class="mt-2 mb-0">No items staged yet.</p>
                        <small>Select SNs on the left and a Storage above to begin.</small>
                    </div>
                `);
                btnConfirm.prop('disabled', true);
                return;
            }

            stagingList.addClass('has-items');
            btnConfirm.prop('disabled', false);

            let html = '';
            staging.forEach((group, gIndex) => {
                html += `
                    <div class="card border-0 shadow-sm mb-3 bg-white">
                        <div class="card-header bg-primary-subtle border-0 py-2 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary small"><i class="ri-archive-line me-1"></i> Box/Package - ${group.storageName}</span>
                            <button class="btn btn-link btn-sm text-danger text-decoration-none py-0" onclick="removeStagingGroup(${gIndex})">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small">
                                            <th class="ps-3 py-1">Material</th>
                                            <th class="text-center py-1">QTY</th>
                                            <th class="py-1">SNs</th>
                                            <th class="py-1"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                `;

                group.items.forEach((item, iIndex) => {
                    html += `
                        <tr>
                            <td class="ps-3 py-2">
                                <div class="fw-bold">${item.material}</div>
                                <div class="text-muted smaller">${item.salesDoc}</div>
                            </td>
                            <td class="text-center fw-bold">${item.sns.length}</td>
                            <td class="py-2">
                                <div class="d-flex flex-wrap gap-1" style="max-width: 200px;">
                                    ${item.sns.slice(0, 3).map(sn => `<span class="badge bg-light text-dark border-light-subtle">${sn}</span>`).join('')}
                                    ${item.sns.length > 3 ? `<span class="badge bg-light text-muted border-light-subtle">+${item.sns.length - 3}</span>` : ''}
                                </div>
                            </td>
                            <td class="text-end pe-2">
                                <button class="btn btn-link btn-sm text-muted p-0" onclick="removeFromStaging(${gIndex}, ${iIndex})"><i class="ri-close-circle-line fs-16"></i></button>
                            </td>
                        </tr>
                    `;
                });

                html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            });

            stagingList.html(html);
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });

        function removeStagingGroup(gIndex) {
            const group = staging[gIndex];
            group.items.forEach(item => {
                item.detail.sns.forEach(s => {
                    if (item.sns.includes(s.serialNumber)) {
                        s.status = 0;
                    }
                });
                item.detail.availableQty = item.detail.sns.filter(s => s.status == 0).length;
            });
            staging.splice(gIndex, 1);
            renderMaterials($('#searchMaterial').val());
            renderStaging();
            updateStats();
        }

        function removeFromStaging(gIndex, iIndex) {
            const group = staging[gIndex];
            const item = group.items[iIndex];
            item.detail.sns.forEach(s => {
                if (item.sns.includes(s.serialNumber)) {
                    s.status = 0;
                }
            });
            item.detail.availableQty = item.detail.sns.filter(s => s.status == 0).length;

            group.items.splice(iIndex, 1);
            if (group.items.length === 0) {
                staging.splice(gIndex, 1);
            }

            renderMaterials($('#searchMaterial').val());
            renderStaging();
            updateStats();
        }

        function clearStaging() {
            staging.forEach((group) => {
                group.items.forEach(item => {
                    item.detail.sns.forEach(s => {
                        if (item.sns.includes(s.serialNumber)) {
                            s.status = 0;
                        }
                    });
                    item.detail.availableQty = item.detail.sns.filter(s => s.status == 0).length;
                });
            });
            staging = [];
            renderMaterials($('#searchMaterial').val());
            renderStaging();
            updateStats();
        }

        function processPutAway() {
            if (staging.length === 0) return;

            const boxes = staging.map((group, index) => {
                const parents = [];
                const children = [];

                group.items.forEach(item => {
                    const boxItem = {
                        productId: item.detail.productId,
                        purchaseOrderDetailId: item.detail.purchaseOrderDetailId,
                        productPackageItemId: item.detail.id,
                        qtySelect: item.sns.length,
                        serialNumber: item.sns,
                        salesDoc: item.salesDoc,
                        item: item.item,
                        material: item.material
                    };

                    if (item.detail.isParent) {
                        parents.push(boxItem);
                    } else {
                        children.push(boxItem);
                    }
                });

                return {
                    boxNumber: index + 1,
                    location: group.storageId,
                    parent: parents,
                    child: children
                };
            });

            const productPackageId = new URLSearchParams(window.location.search).get('id');

            Swal.fire({
                title: 'Confirm Put Away',
                text: `You are about to Put Away items into ${staging.length} different storage locations. Continue?`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#405189',
                cancelButtonColor: '#f06548',
                confirmButtonText: 'Yes, Confirm All'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we update inventory',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route('inbound.put-away.store') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            box: boxes,
                            productPackageId: productPackageId
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Inventory has been updated successfully.',
                                    icon: 'success',
                                    confirmButtonColor: '#405189'
                                }).then(() => {
                                    window.location.href = '{{ route('inbound.put-away') }}';
                                });
                            } else {
                                Swal.fire('Error', response.message || 'Failed to process Put Away',
                                    'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Server error occurred during processing', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
