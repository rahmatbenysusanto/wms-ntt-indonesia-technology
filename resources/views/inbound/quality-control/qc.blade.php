@extends('layout.index')
@section('title', 'Quality Control')
@section('sizeBarSize', 'sm')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Process Quality Control</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item">Quality Control</li>
                        <li class="breadcrumb-item active">Process</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Purchase Order Detail</h4>
                </div>
                <div class="card-body">
                    <table>
                        <tr>
                            <td class="fw-bold">Purc Doc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $purchaseOrder->purc_doc }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Sales Doc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ request()->get('sales-doc')  }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">List Item</h4>
                                <a class="btn btn-info btn-sm" onclick="pilihSemua()">Pilih Semua</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped align-middle" id="masterMaterial">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-center">Item</th>
                                        <th>Material</th>
                                        <th>Desc</th>
                                        <th>Hierarchy Desc</th>
                                        <th class="text-center">QTY</th>
                                        <th class="text-center">QTY QC</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="listItemMaster">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">Mapping Parent & Child Item</h4>
                                <a class="btn btn-primary btn-sm" onclick="addMappingItem()">Add Mapping Item</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="text-center">Item</th>
                                        <th>Material</th>
                                        <th style="width: 100px">QTY</th>
                                        <th>Parent</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="listMapping">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Quality Control Items</h4>
                        <a class="btn btn-primary" onclick="processQC()">Process Quality Control</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Material</th>
                                    <th class="text-center">Parent</th>
                                    <th>Item</th>
                                    <th>Desc</th>
                                    <th>Hierarchy Desc</th>
                                    <th class="text-center">QTY</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="listQualityControl">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        localStorage.clear();
        loadMasterItem();
        function loadMasterItem() {
            const item = @json($products);
            localStorage.setItem('master', JSON.stringify(item));
            viewListMaster();
        }

        function viewListMaster() {
            const products = JSON.parse(localStorage.getItem('master')) ?? [];
            let html = '';
            let number = 1;

            // Simpan halaman aktif sebelum destroy
            let currentPage = 0;
            if ($.fn.DataTable.isDataTable('#masterMaterial')) {
                currentPage = $('#masterMaterial').DataTable().page();
                $('#masterMaterial').DataTable().destroy();
            }

            products.forEach((item, index) => {
                let button = '';
                if (parseInt(item.qty) !== parseInt(item.qty_qc)) {
                    button = `<a class="btn btn-info btn-sm" onclick="addToMapping(${index})">Pilih</a>`;
                }

                html += `
            <tr>
                <td>${number}</td>
                <td class="text-center">${item.item}</td>
                <td>${item.sku}</td>
                <td>${item.name}</td>
                <td>${item.type}</td>
                <td class="text-center">${item.qty}</td>
                <td class="text-center">${item.qty_qc}</td>
                <td>
                    <div class="d-flex gap-2">
                        ${button}
                    </div>
                </td>
            </tr>
        `;

                number++;
            });

            document.getElementById('listItemMaster').innerHTML = html;

            // Re-init DataTable dan kembali ke halaman sebelumnya
            const table = new DataTable('#masterMaterial');
            table.page(currentPage).draw('page');
        }

        function pilihSemua() {
            const products = JSON.parse(localStorage.getItem('master')) ?? [];
            let mapping = JSON.parse(localStorage.getItem('mapping')) ?? [];

            products.forEach((item, index) => {
                const product = products[index];

                product.parent = 0;
                product.index = index;

                const existingIndex = mapping.findIndex(m => m.id === product.id);

                if (existingIndex !== -1) {
                    // Jika sudah ada, tambahkan qty
                    mapping[existingIndex].qty = Number(mapping[existingIndex].qty) + 1;
                    products[index].qty_qc = Number(products[index].qty_qc + 1);
                } else {
                    // Jika belum ada, tambahkan produk baru ke mapping
                    mapping.push({
                        id: product.id,
                        index: product.index,
                        item: product.item,
                        name: product.name,
                        parent: product.parent,
                        qty: product.qty - product.qty_qc,
                        sku: product.sku,
                        type: product.type,
                    });
                    products[index].qty_qc += product.qty - product.qty_qc;
                }
            });

            localStorage.setItem('master', JSON.stringify(products));
            localStorage.setItem('mapping', JSON.stringify(mapping));

            viewListMaster();
            viewMappingList();
        }

        function addToMapping(index) {
            const products = JSON.parse(localStorage.getItem('master')) ?? [];
            const product = products[index];

            let mapping = JSON.parse(localStorage.getItem('mapping')) ?? [];

            product.parent = 0;
            product.index = index;

            const existingIndex = mapping.findIndex(m => m.id === product.id);

            if (existingIndex !== -1) {
                // Jika sudah ada, tambahkan qty
                mapping[existingIndex].qty = Number(mapping[existingIndex].qty) + 1;
                products[index].qty_qc = Number(products[index].qty_qc + 1);
            } else {
                // Jika belum ada, tambahkan produk baru ke mapping
                mapping.push({
                    id: product.id,
                    index: product.index,
                    item: product.item,
                    name: product.name,
                    parent: product.parent,
                    qty: product.qty - product.qty_qc,
                    sku: product.sku,
                    type: product.type,
                });
                products[index].qty_qc += product.qty - product.qty_qc;
            }

            localStorage.setItem('master', JSON.stringify(products));
            localStorage.setItem('mapping', JSON.stringify(mapping));

            viewListMaster();
            viewMappingList();
        }

        function viewMappingList() {
            const mapping = JSON.parse(localStorage.getItem('mapping')) ?? [];
            let html = '';
            let number = 1;

            mapping.forEach((item, index) => {
                html += `
                     <tr>
                        <td>${number}</td>
                        <td class="text-center">${item.item}</td>
                        <td>
                            <p class="mb-1">${item.sku}</p>
                            <p class="mb-1">${item.name}</p>
                            <p>${item.type}</p>
                        </td>
                        <td><input type="number" class="form-control" value="${item.qty}" onchange="changeQtyMapping(${item.index}, ${index}, this.value)"></td>
                        <td>
                            <div class="form-check form-switch form-switch-md">
                                <input class="form-check-input" type="checkbox" role="switch" id="parent_${index}" value="${item.parent}" ${item.parent === 1 ? 'checked' : ''} onchange="changeParent(${index}, this.checked)">
                                <label class="form-check-label" for="parent_${index}">Parent Material</label>
                            </div>
                        </td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteMappingItem(${item.index}, ${index})">Delete</a></td>
                    </tr>
                `;

                number++;
            });

            document.getElementById('listMapping').innerHTML = html;
        }

        function changeQtyMapping(itemIndex, index, value) {
            // Change Data Mapping
            const mapping = JSON.parse(localStorage.getItem('mapping')) ?? [];
            mapping[index].qty = value;
            localStorage.setItem('mapping', JSON.stringify(mapping));
            viewMappingList();

            // Calculate QTY QC Master Item
            const products = JSON.parse(localStorage.getItem('master')) ?? [];
            const listItemMapping = mapping.filter(item => parseInt(item.index) === itemIndex);
            let qty = 0;
            listItemMapping.forEach((item) => {
                qty += parseInt(item.qty);
            });
            products[itemIndex].qty_qc = qty;
            localStorage.setItem('master', JSON.stringify(products));
            viewListMaster();
        }

        function changeParent(index, isChecked) {
            const mapping = JSON.parse(localStorage.getItem('mapping')) ?? [];

            if (isChecked) {
                // Cek apakah sudah ada parent lain
                const existingParentIndex = mapping.findIndex(item => item.parent === 1);

                if (existingParentIndex !== -1 && existingParentIndex !== index) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Parent hanya boleh 1',
                        icon: 'warning'
                    });

                    const checkbox = document.getElementById(`parent_${index}`);
                    if (checkbox) checkbox.checked = false;

                    return;
                }

                mapping[index].parent = 1;
            } else {
                mapping[index].parent = 0;
            }

            localStorage.setItem('mapping', JSON.stringify(mapping));
            viewMappingList();
        }

        function deleteMappingItem(index, detailIndex) {
            const mapping = JSON.parse(localStorage.getItem('mapping')) ?? [];
            const products = JSON.parse(localStorage.getItem('master')) ?? [];

            mapping.splice(detailIndex, 1);
            products[index].qty_qc = 0;

            localStorage.setItem('mapping', JSON.stringify(mapping));
            localStorage.setItem('master', JSON.stringify(products));
            viewMappingList();
            viewListMaster();
        }

        function addMappingItem() {
            const mapping = JSON.parse(localStorage.getItem('mapping')) ?? [];
            const qualityControl = JSON.parse(localStorage.getItem('qc')) ?? [];
            const checkParent = mapping.find(item => item.parent === 1);
            if (!checkParent) {
                Swal.fire({
                    title: 'Warning',
                    text: 'Harus ada 1 parent disetiap produk',
                    icon: 'warning'
                });
                return true;
            }

            qualityControl.push({
                id: checkParent.id,
                item: checkParent.item,
                sku: checkParent.sku,
                name: checkParent.name,
                type: checkParent.type,
                qty: checkParent.qty,
                child: mapping.filter(item => item.parent === 0)
            });

            localStorage.setItem('qc', JSON.stringify(qualityControl));
            viewListQC();
            localStorage.setItem('mapping', JSON.stringify([]));
            viewMappingList();
        }

        function viewListQC() {
            const qualityControl = JSON.parse(localStorage.getItem('qc')) ?? [];
            let html = '';
            let number = 1;

            qualityControl.forEach((item, index) => {
                html += `
                    <tr>
                        <td>${number}</td>
                        <td>${item.sku}</td>
                        <td class="text-center"><span class="badge bg-success-subtle text-success">Parent</span></td>
                        <td>${item.item}</td>
                        <td>${item.name}</td>
                        <td>${item.type}</td>
                        <td class="text-center fw-bold">${item.qty}</td>
                        <td><a class="btn btn-danger btn-sm" onclick="deleteQC(${index})">Delete</a></td>
                    </tr>
                `;

                (item.child).forEach((child) => {
                    html += `
                        <tr>
                            <td></td>
                            <td>${child.sku}</td>
                            <td class="text-center"></td>
                            <td>${child.item}</td>
                            <td>${child.name}</td>
                            <td>${child.type}</td>
                            <td class="text-center fw-bold">${child.qty}</td>
                            <td></td>
                        </tr>
                    `;
                });

                number++;
            });

            document.getElementById('listQualityControl').innerHTML = html;
        }

        function deleteQC(index) {
            const qualityControl = JSON.parse(localStorage.getItem('qc')) ?? [];
            const products = JSON.parse(localStorage.getItem('master')) ?? [];
            const data = qualityControl[index];

            const product = products.find((item => item.id === data.id));
            product.qty = data.qty;
            product.qty_qc = 0;

            (data.child).forEach((child) => {
                const product = products.find((item => item.id === child.id));
                product.qty = child.qty;
                product.qty_qc = 0;
            });

            qualityControl.splice(index, 1);
            localStorage.setItem('master', JSON.stringify(products));
            localStorage.setItem('qc', JSON.stringify(qualityControl));
            viewListMaster();
            viewListQC();
        }

        function processQC() {
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

                    $.ajax({
                        url: '{{ route('inbound.quality-control-store-process') }}',
                        method: 'POST',
                        data:{
                            _token: '{{ csrf_token() }}',
                            qualityControl: JSON.parse(localStorage.getItem('qc')) ?? [],
                            purchaseOrderId: '{{ request()->get('po') }}',
                            salesDoc: '{{ request()->get('sales-doc') }}'
                        },
                        success: (res) => {

                        }
                    });

                }
            });
        }
    </script>
@endsection



















































