@extends('layout.index')
@section('title', 'General Room')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">General Room</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">General Room</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List General Room</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('general-room.download-pdf') }}" class="btn btn-pdf btn-sm" target="_blank">Download Report PDF</a>
                            <a href="{{ route('general-room.download-excel') }}" class="btn btn-success btn-sm" target="_blank">Download Report Excel</a>
                            <a href="{{ route('general-room.create-box') }}" class="btn btn-primary btn-sm">Create Box</a>
                        </div>
                    </div>
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
                                <label class="form-label">Client</label>
                                <select name="client" class="form-control">
                                    <option value="">-- Select Client --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ request()->get('client') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
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
                                    <th>Number</th>
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th>Client</th>
                                    <th class="text-center">QTY Item</th>
                                    <th class="text-center">QTY</th>
                                    <th>Created At</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($generalRoom as $index => $gr)
                                    <tr>
                                        <td>{{ $generalRoom->firstItem() + $index }}</td>
                                        <td>
                                            <div>{{ $gr->number }}</div>
                                            <div><b>Box :</b> {{ $gr->reff_number ?? '-' }}</div>
                                        </td>
                                        <td>{{ $gr->purchaseOrder->purc_doc }}</td>
                                        <td>
                                            @foreach(json_decode($gr->sales_docs) ?? [] as $salesDoc)
                                                <div>{{ $salesDoc }}</div>
                                            @endforeach
                                        </td>
                                        <td>{{ $gr->purchaseOrder->customer->name }}</td>
                                        <td class="text-center fw-bold">{{ $gr->qty_item }}</td>
                                        <td class="text-center fw-bold">{{ $gr->qty }}</td>
                                        <td>{{ \Carbon\Carbon::parse($gr->created_at)->translatedFormat('d F Y H:i') }}</td>
                                        <td>{{ $gr->user->name }}</td>
                                        <td>
                                            <a href="{{ route('general-room.detail', ['id' => $gr->id]) }}" class="btn btn-info btn-sm">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        @if ($generalRoom->hasPages())
                            <ul class="pagination">
                                @if ($generalRoom->onFirstPage())
                                    <li class="disabled"><span>&laquo; Previous</span></li>
                                @else
                                    <li><a href="{{ $generalRoom->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                @endif

                                @foreach ($generalRoom->links()->elements as $element)
                                    @if (is_string($element))
                                        <li class="disabled"><span>{{ $element }}</span></li>
                                    @endif

                                    @if (is_array($element))
                                        @foreach ($element as $page => $url)
                                            @if ($page == $generalRoom->currentPage())
                                                <li class="active"><span>{{ $page }}</span></li>
                                            @else
                                                <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @if ($generalRoom->hasMorePages())
                                    <li><a href="{{ $generalRoom->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
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

    </script>
@endsection
