@extends('layout.index')
@section('title', 'Cycle Count Detail')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Cycle Count Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Cycle Count Detail</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Product</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <table>
                                <tr>
                                    <td class="fw-bold">Purc Doc</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $cycleCount->purchaseOrder->purc_doc }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Sales Doc</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $cycleCount->purchaseOrderDetail->sales_doc }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Item</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $cycleCount->purchaseOrderDetail->item }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Material</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $cycleCount->purchaseOrderDetail->material }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">PO Item Desc</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $cycleCount->purchaseOrderDetail->po_item_desc }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Prod Hierarchy Desc</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $cycleCount->purchaseOrderDetail->prod_hierarchy_desc }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4">
                            <table>
                                <tr>
                                    <td class="fw-bold">QTY</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $cycleCount->qty }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Storage Loc</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">
                                        @if(in_array($cycleCount->inventoryPackageItem->inventoryPackage->storage->id, [2,3,4]))
                                            <b>{{ $cycleCount->inventoryPackageItem->inventoryPackage->storage->raw }}</b>
                                        @else
                                            <b>{{ $cycleCount->inventoryPackageItem->inventoryPackage->storage->raw }}</b> -
                                            <b>{{ $cycleCount->inventoryPackageItem->inventoryPackage->storage->area }}</b> -
                                            <b>{{ $cycleCount->inventoryPackageItem->inventoryPackage->storage->rak }}</b> -
                                            <b>{{ $cycleCount->inventoryPackageItem->inventoryPackage->storage->bin }}</b>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Type</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">
                                        @if($cycleCount->type == 'outbound')
                                            <span class="badge bg-danger-subtle text-danger">Outbound</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success">Inbound</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created By</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $cycleCount->user->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created At</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ \Carbon\Carbon::parse($cycleCount->created_at)->translatedFormat('d F Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4">
                            @if($cycleCount->type == 'outbound')
                                <table>
                                    <tr>
                                        <td class="fw-bold">Delivery Note Number</td>
                                        <td class="fw-bold ps-3">:</td>
                                        <td class="ps-1">{{ $cycleCount->outbound->delivery_note_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Deliv Loc</td>
                                        <td class="fw-bold ps-3">:</td>
                                        <td class="ps-1">{{ $cycleCount->outbound->deliv_loc }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Deliv Dest</td>
                                        <td class="fw-bold ps-3">:</td>
                                        <td class="ps-1">{{ $cycleCount->outbound->deliv_dest }}</td>
                                    </tr>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Serial Number</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Serial Number</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach(json_decode($cycleCount->serial_number) ?? [] as $serialNumber)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $serialNumber }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
