@extends('layout.index')
@section('title', 'List Box Inventory')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Inventory Box List</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Box List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Box</h4>
                        <a href="{{ route('inventory.box.report-excel') }}" class="btn btn-info btn-sm">Download Report Excel</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row">
                            <div class="col-2">
                                <label class="form-label">PA Number</label>
                                <input type="text" class="form-control" name="paNumber" placeholder="Pa Number">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Purc Doc</label>
                                <input type="text" class="form-control" name="purcDoc" placeholder="Purchase Order">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Sales Doc</label>
                                <input type="text" class="form-control" name="salesDoc" placeholder="Sales Doc">
                            </div>
                            <div class="col-2">
                                <label class="form-label text-white">-</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-info">Search</button>
                                    <a href="{{ route('inventory.box') }}" class="btn btn-danger">Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Number</th>
                                    <th>Reff</th>
                                    <th>Storage</th>
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th class="text-center">QTY Item</th>
                                    <th class="text-center">QTY</th>
                                    <th>Created By</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($box as $index => $item)
                                    <tr>
                                        <td>{{ $box->firstItem() + $index }}</td>
                                        <td>
                                            <div>{{ $item->number }}</div>
                                            @if($item->return == 1)
                                                @switch($item->return_from)
                                                    @case('gr')
                                                        <span class="badge bg-danger">Return From General Room</span>
                                                        @break
                                                    @case('pm')
                                                        <span class="badge bg-danger">Return From PM Room</span>
                                                        @break
                                                    @case('spare')
                                                        <span class="badge bg-danger">Return From Spare Room</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-danger">Return From Outbound</span>
                                                @endswitch
                                            @endif
                                        </td>
                                        <td>{{ $item->reff_number }}</td>
                                        <td>{{ $item->storage->raw.' - '.$item->storage->area.' - '.$item->storage->rak.' - '.$item->storage->bin }}</td>
                                        <td>{{ $item->purchaseOrder->purc_doc }}</td>
                                        <td>
                                            @foreach(json_decode($item->sales_docs) ?? [] as $salesDoc)
                                                <div>{{ $salesDoc }}</div>
                                            @endforeach
                                        </td>
                                        <td class="text-center fw-bold">{{ number_format($item->qty_item) }}</td>
                                        <td class="text-center fw-bold">{{ number_format($item->qty) }}</td>
                                        <td>{{ $item->user->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('inventory.box.detail', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        @if ($box->hasPages())
                            <ul class="pagination">
                                @if ($box->onFirstPage())
                                    <li class="disabled"><span>&laquo; Previous</span></li>
                                @else
                                    <li><a href="{{ $box->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                @endif

                                @foreach ($box->links()->elements as $element)
                                    @if (is_string($element))
                                        <li class="disabled"><span>{{ $element }}</span></li>
                                    @endif

                                    @if (is_array($element))
                                        @foreach ($element as $page => $url)
                                            @if ($page == $box->currentPage())
                                                <li class="active"><span>{{ $page }}</span></li>
                                            @else
                                                <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @if ($box->hasMorePages())
                                    <li><a href="{{ $box->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
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
