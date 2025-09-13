@extends('layout.index')
@section('title', 'Aging Detail')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Aging Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Aging Detail</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Aging Detail {{ $text }}</h4>
                </div>
                <div class="card-header">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row">
                            <div class="col-2">
                                <label class="form-label">Purc Doc</label>
                                <input type="text" class="form-control" name="purcDoc" value="{{ request()->get('purcDoc') }}" placeholder="Purc Doc ...">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Sales Doc</label>
                                <input type="text" class="form-control" name="salesDoc" value="{{ request()->get('salesDoc') }}" placeholder="Sales Doc ...">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Material</label>
                                <select class="form-control select2" name="material">
                                    <option value="">-- Select Material --</option>
                                    @foreach($material as $item)
                                        <option {{ request()->get('material') == $item->material ? 'selected' : '' }}>{{ $item->material }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <label class="form-label text-white">-</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <a href="{{ url()->current() }}" class="btn btn-danger">Clear</a>
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
                                    <th>Material</th>
                                    <th>PO Item Desc</th>
                                    <th>Prod Hierarchy Desc</th>
                                    <th class="text-center">QTY</th>
                                    <th>Total Price</th>
                                    <th>Inbound Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventoryDetail as $index => $detail)
                                    <tr>
                                        <td>{{ $inventoryDetail->firstItem() + $index }}</td>
                                        <td>{{ $detail->purc_doc }}</td>
                                        <td>{{ $detail->sales_doc }}</td>
                                        <td>{{ $detail->material }}</td>
                                        <td>{{ $detail->po_item_desc }}</td>
                                        <td>{{ $detail->prod_hierarchy_desc }}</td>
                                        <td class="text-center fw-bold">{{ number_format($detail->qty) }}</td>
                                        <td>$ {{ number_format($detail->total) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($detail->aging_date)->translatedFormat('d F Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        @if ($inventoryDetail->hasPages())
                            <ul class="pagination">
                                @if ($inventoryDetail->onFirstPage())
                                    <li class="disabled"><span>&laquo; Previous</span></li>
                                @else
                                    <li><a href="{{ $inventoryDetail->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                @endif

                                @foreach ($inventoryDetail->links()->elements as $element)
                                    @if (is_string($element))
                                        <li class="disabled"><span>{{ $element }}</span></li>
                                    @endif

                                    @if (is_array($element))
                                        @foreach ($element as $page => $url)
                                            @if ($page == $inventoryDetail->currentPage())
                                                <li class="active"><span>{{ $page }}</span></li>
                                            @else
                                                <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @if ($inventoryDetail->hasMorePages())
                                    <li><a href="{{ $inventoryDetail->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
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

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "-- Select Material --",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection
