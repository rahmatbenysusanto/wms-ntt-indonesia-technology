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
                                <th>Material</th>
                                <th>Desc</th>
                                <th>Hierarchy Desc</th>
                                <th class="text-center">QTY</th>
                                <th>Type</th>
                                <th class="text-center">Serial Number</th>
                                <th>Created By</th>
                                <td>Date</td>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($cycleCount as $index => $item)
                            <tr>
                                <td>{{ $cycleCount->firstItem() + $index }}</td>
                                <td>{{ $item->purchaseOrder->purc_doc }}</td>
                                <td>{{ $item->purchaseOrderDetail->sales_doc }}</td>
                                <td>{{ $item->purchaseOrderDetail->material }}</td>
                                <td>{{ $item->purchaseOrderDetail->po_item_desc }}</td>
                                <td>{{ $item->purchaseOrderDetail->prod_hierarchy_desc }}</td>
                                <td class="text-center fw-bold">{{ number_format($item->qty) }}</td>
                                <td>
                                    @if($item->type == 'outbound')
                                        <span class="badge bg-danger-subtle text-danger">Outbound</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success">Inbound</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('inventory.cycle-count-detail', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Detail SN</a>
                                </td>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end mt-2">
                        @if ($cycleCount->hasPages())
                            <ul class="pagination">
                                @if ($cycleCount->onFirstPage())
                                    <li class="disabled"><span>&laquo; Previous</span></li>
                                @else
                                    <li><a href="{{ $cycleCount->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                @endif

                                @foreach ($cycleCount->links()->elements as $element)
                                    @if (is_string($element))
                                        <li class="disabled"><span>{{ $element }}</span></li>
                                    @endif

                                    @if (is_array($element))
                                        @foreach ($element as $page => $url)
                                            @if ($page == $cycleCount->currentPage())
                                                <li class="active"><span>{{ $page }}</span></li>
                                            @else
                                                <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @if ($cycleCount->hasMorePages())
                                    <li><a href="{{ $cycleCount->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
                                @else
                                    <li class="disabled"><span>Next &raquo;</span></li>
                                @endif
                            </ul>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
