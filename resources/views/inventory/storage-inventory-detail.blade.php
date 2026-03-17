@extends('layout.index')
@section('title', 'Storage Inventory Detail')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Storage Inventory Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('inventory.storage-inventory') }}">Storage Inventory</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Storage Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Raw</label>
                            <p>{{ $storage->raw }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Area</label>
                            <p>{{ $storage->area ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Rak</label>
                            <p>{{ $storage->rak ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Bin</label>
                            <p>{{ $storage->bin ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Product List in Storage</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="storage-detail-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th>Material</th>
                                    <th>PO Item Desc</th>
                                    <th>Box Number</th>
                                    <th class="text-center">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventory as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->purchaseOrderDetail->purchaseOrder->customer->name ?? '-' }}</td>
                                        <td>{{ $item->purchaseOrderDetail->purchaseOrder->purc_doc ?? '-' }}</td>
                                        <td>{{ $item->sales_doc }}</td>
                                        <td>{{ $item->purchaseOrderDetail->material }}</td>
                                        <td>{{ $item->purchaseOrderDetail->po_item_desc }}</td>
                                        <td>{{ $item->inventoryPackageItem->inventoryPackage->number ?? '-' }}</td>
                                        <td class="text-center fw-bold">{{ number_format($item->qty) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('inventory.storage-inventory') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#storage-detail-table').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
        });
    });
</script>
@endsection
