@extends('layout.index')
@section('title', 'Cycle Count')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Product List</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Product List</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Cycle Count Product</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Purc Doc</th>
                                <th>Sales Doc</th>
                                <th>Item</th>
                                <th>Material</th>
                                <th>Desc</th>
                                <th>Hierarchy Desc</th>
                                <th class="text-center">QTY</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($cycleCount as $index => $item)
                            <tr>
                                <td>{{ $cycleCount->firstItem() + $index }}</td>
                                <td>{{ $item->purc_doc }}</td>
                                <td>{{ $item->sales_doc }}</td>
                                <td>{{ $item->item }}</td>
                                <td>{{ $item->material }}</td>
                                <td>{{ $item->po_item_desc }}</td>
                                <td>{{ $item->prod_hierarchy_desc }}</td>
                                <td class="text-center fw-bold">{{ number_format($item->qty) }}</td>
                                <td>
                                    @if($item->type == 'outbound')
                                        <span class="badge bg-danger-subtle text-danger">Outbound</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success">Inbound</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
