@extends('layout.index')
@section('title', 'Detail Put Away')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Detail Put Away</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item active">Detail Put Away</li>
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
                                <th class="text-center">Item</th>
                                <th>Sales Doc</th>
                                <th class="text-center">Type</th>
                                <th>Material</th>
                                <th>Po Item Desc</th>
                                <th>Prod Hierarchy Desc</th>
                                <th class="text-center">QTY</th>
                                <th class="text-center">Serial Number</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $index => $product)
                                @foreach($product->productPackageItem as $item)
                                    <tr>
                                        <td class="text-center">{{ $item->purchaseOrderDetail->item }}</td>
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
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Default Modals -->
    <div id="detailSerialNumberModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
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
                        <tbody id="listSN">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function detailSerialNumber(id) {
            const data = @json($products[0]);
            const product = data.product_package_item;
            const find = product.find(i => parseInt(i.id) === parseInt(id));
            const serialNumber = find.product_package_item_sn;
            let html = '';

            serialNumber.forEach((sn) => {
                html += `<tr><td>${sn.serial_number}</td></tr>`;
            });

            document.getElementById('listSN').innerHTML = html;
            $('#detailSerialNumberModal').modal('show');
        }
    </script>
@endsection
