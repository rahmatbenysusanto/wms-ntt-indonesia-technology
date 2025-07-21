@extends('layout.index')
@section('title', 'Detail Inventory')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Inventory Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item">Product List</li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Inventory Detail</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <table>
                                <tr>
                                    <td class="fw-bold">Purc Doc</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $inventory->purc_doc }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Sales Doc</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $inventory->sales_doc }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Storage Location</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $inventory->storage->raw }} - {{ $inventory->storage->area }} - {{ $inventory->storage->rak }} - {{ $inventory->storage->bin }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4">
                            <table>
                                <tr>
                                    <td class="fw-bold">Material</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $inventory->product->material }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">PO Item Desc</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $inventory->product->po_item_desc }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Prod Hierarchy Desc</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $inventory->product->prod_hierarchy_desc }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4">
                            <table>
                                <tr>
                                    <td class="fw-bold">Vendor</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $inventory->purchaseOrder->vendor->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Customer Name</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ $inventory->purchaseOrder->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Stock</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">{{ number_format($inventory->stock) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Detail Serial Number</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>PA Number</th>
                                    <th>Box</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">QTY</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detail as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->number }}</td>
                                        <td>{{ $item->reff_number }}</td>
                                        <td class="text-center">
                                            @if($item->is_parent == 1)
                                                <span class="badge bg-danger-subtle text-danger"> Parent </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary"> Child </span>
                                            @endif
                                        </td>
                                        <td class="text-center fw-bold">{{ number_format($item->qty) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
