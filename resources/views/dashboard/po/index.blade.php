@extends('layout.index')
@section('title', 'Dashboard PO')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Dashboard PO</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a>Dashboard PO</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Purchase Order</h4>
                </div>
                <div class="card-body">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row">
                            <div class="col-2">
                                <div>
                                    <label class="form-label">Purc Doc</label>
                                    <input type="text" class="form-control" name="purcDoc" value="{{ request()->get('purcDoc') }}" placeholder="Purc Doc ...">
                                </div>
                            </div>
                            <div class="col-2">
                                <div>
                                    <label class="form-label">Customer</label>
                                    <select class="form-control select2Customer" name="client">
                                        <option value="">-- Select Customer --</option>
                                        @foreach($customer as $item)
                                            <option {{ request()->get('client') == $item->name ? 'selected' : '' }} >{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-2">
                                <div>
                                    <label class="form-label text-white">-</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                        <a href="{{ url()->current() }}" class="btn btn-danger">Clear</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <table class="table table-responsive align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Purc Doc</th>
                                <th>Client</th>
                                <th class="text-center">Total SO</th>
                                <th class="text-center">Total QTY PO</th>
                                <th class="text-center">QTY Inbound</th>
                                <th class="text-center">Stock Inventory</th>
                                <th class="text-center">QTY Outbound</th>
                                <th>Created Date</th>
                                <th>Created By</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listPO as $index => $item)
                                <tr>
                                    <td>{{ $listPO->firstItem() + $index }}</td>
                                    <td>{{ $item->purc_doc }}</td>
                                    <td>{{ $item->customer->name }}</td>
                                    <td class="text-center fw-bold">
                                        <a href="" class="text-black">{{ number_format($item->sales_doc_qty) }}</a>
                                    </td>
                                    <td class="text-center fw-bold">{{ number_format($item->item_qty) }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->qty_po) }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->stock) }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->qty_outbound) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>
                                        <a href="{{ route('dashboard.po.detail', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end mt-2">
                        @if ($listPO->hasPages())
                            <ul class="pagination">
                                @if ($listPO->onFirstPage())
                                    <li class="disabled"><span>&laquo; Previous</span></li>
                                @else
                                    <li><a href="{{ $listPO->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                @endif

                                @foreach ($listPO->links()->elements as $element)
                                    @if (is_string($element))
                                        <li class="disabled"><span>{{ $element }}</span></li>
                                    @endif

                                    @if (is_array($element))
                                        @foreach ($element as $page => $url)
                                            @if ($page == $listPO->currentPage())
                                                <li class="active"><span>{{ $page }}</span></li>
                                            @else
                                                <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @if ($listPO->hasMorePages())
                                    <li><a href="{{ $listPO->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
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
        $('.select2Customer').select2({
            placeholder: "-- Select Customer --",
            allowClear: true,
            width: '100%'
        });
    </script>
@endsection
