@extends('layout.index')
@section('title', 'Create Order')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Order List</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Outbound</a></li>
                        <li class="breadcrumb-item active">Order List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Item</h4>
                        <a class="btn btn-info" onclick="openModalProduct()">Add Product</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Default Modals -->
    <div id="listProductModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">List Products</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Sales Doc</label>
                        <select class="form-control" id="listSalesDoc" onchange="changeSalesDoc(this.value)">

                        </select>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Purc Doc</th>
                                    <th>Item</th>
                                    <th>Material</th>
                                    <th>Desc</th>
                                    <th>Prod Hierarchy Desc</th>
                                    <th class="text-center">QTY</th>
                                    <th>Loc</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="listItemSalesDoc">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        localStorage.clear();

        function openModalProduct() {
            const salesDoc = @json($inventory);
            let html = '<option value="">-- Select Sales Doc --</option>';

            salesDoc.forEach((item) => {
                html += `<option>${item.sales_doc}</option>`;
            });

            document.getElementById('listSalesDoc').innerHTML = html;

            $('#listProductModal').modal('show');
        }

        function changeSalesDoc(value) {
            $.ajax({
                url: '{{ route('outbound.sales-doc') }}',
                method: 'GET',
                data: {
                    salesDoc: value
                },
                success: (res) => {
                    const data = res.data;
                    console.log(data);
                    let html = '';
                    let number = 1;

                    data.forEach((item) => {
                        (item.products).forEach((product) => {
                            html += `
                                <tr>
                                    <td>${number}</td>
                                    <td><span class="badge bg-success-subtle text-success">Parent</span></td>
                                    <td>${product.sales_doc}</td>
                                    <td>${product.purchase_order_detail.item}</td>
                                    <td>${product.purchase_order_detail.material}</td>
                                    <td>${product.purchase_order_detail.po_item_desc}</td>
                                    <td>${product.purchase_order_detail.prod_hierarchy_desc}</td>
                                    <td class="text-center fw-bold">${product.qty}</td>
                                    <td>${item.storage.raw} - ${item.storage.area} - ${item.storage.rak} - ${item.storage.bin}</td>
                                    <td><a class="btn btn-success btn-sm" onclick="pilihParent(${product.id})">Pilih Parent</td>
                                </tr>
                            `;

                            (product.child).forEach((child) => {
                                html += `
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>${child.sales_doc}</td>
                                        <td>${child.purchase_order_detail.item}</td>
                                        <td>${child.purchase_order_detail.material}</td>
                                        <td>${child.purchase_order_detail.po_item_desc}</td>
                                        <td>${child.purchase_order_detail.prod_hierarchy_desc}</td>
                                        <td class="text-center fw-bold">${child.qty}</td>
                                        <td>${item.storage.raw} - ${item.storage.area} - ${item.storage.rak} - ${item.storage.bin}</td>
                                        <td><a class="btn btn-info btn-sm" onclick="pilihChild(${child.id})">Pilih</td>
                                    </tr>
                                `;
                            });

                            number++;
                        });
                    });

                    document.getElementById('listItemSalesDoc').innerHTML = html;
                }
            });
        }

        function pilihParent(id) {
            $.ajax({
                url: '',
                method: 'GET',
                data: {
                    id: id,
                    type: 'parent'
                },
                success: (res) => {

                }
            });
        }

        function pilihChild(id) {

        }
     </script>
@endsection
