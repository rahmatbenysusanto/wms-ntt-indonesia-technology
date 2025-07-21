@extends('layout.index')
@section('title', 'Put Away')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Put Away</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item active">Put Away</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Put Away</h4>
                </div>
                <div class="card-header">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row">
                            <div class="col-2">
                                <label class="form-label">Purc Doc</label>
                                <input type="text" class="form-control" value="{{ request()->get('purcDoc', null) }}" name="purcDoc">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Sales Doc</label>
                                <input type="text" class="form-control" value="{{ request()->get('salesDoc', null) }}" name="salesDoc">
                            </div>
                            <div class="col-2">
                                <label class="form-label text-white">-</label>
                                <div>
                                    <button type="submit" class="btn btn-info">Search</button>
                                    <a href="{{ route('inbound.put-away') }}" class="btn btn-danger">Clear</a>
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
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th>Parent Material</th>
                                    <th>Parent Item Desc</th>
                                    <th class="text-center">QTY Item</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-center">Status</th>
                                    <th>QC Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($putAway as $index => $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->purchaseOrder->purc_doc }}</td>
                                    <td>
                                        @foreach($item->sales_doc as $salesDoc)
                                            <div class="mb-1">{{ $salesDoc }}</div>
                                        @endforeach
                                    </td>
                                    <td>{{ $item->product->product->material }}</td>
                                    <td>{{ $item->product->product->po_item_desc }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->qty_item) }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->qty) }}</td>
                                    <td class="text-center">
                                        @if($item->status == 'open')
                                            <span class="badge bg-warning-subtle text-warning">Put Away</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success">Done</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @if($item->status == 'open')
                                                <a href="{{ route('inbound.put-away-detail.open', ['id' => $item->id]) }}" class="btn btn-primary btn-sm">Detail</a>
                                                <a href="{{ route('inbound.put-away-process', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Put Away</a>
                                            @else
                                                <a href="{{ route('inbound.put-away-detail', ['id' => $item->id]) }}" class="btn btn-primary btn-sm">Detail</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        @if ($putAway->hasPages())
                            <ul class="pagination">
                                @if ($putAway->onFirstPage())
                                    <li class="disabled"><span>&laquo; Previous</span></li>
                                @else
                                    <li><a href="{{ $putAway->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                @endif

                                @foreach ($putAway->links()->elements as $element)
                                    @if (is_string($element))
                                        <li class="disabled"><span>{{ $element }}</span></li>
                                    @endif

                                    @if (is_array($element))
                                        @foreach ($element as $page => $url)
                                            @if ($page == $putAway->currentPage())
                                                <li class="active"><span>{{ $page }}</span></li>
                                            @else
                                                <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @if ($putAway->hasMorePages())
                                    <li><a href="{{ $putAway->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
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
