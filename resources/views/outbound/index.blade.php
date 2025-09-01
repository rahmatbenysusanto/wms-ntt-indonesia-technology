@extends('layout.index')
@section('title', 'Outbound')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Order List</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Outbound</a></li>
                        <li class="breadcrumb-item active">Order List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Order List</h4>
                        <div class="d-flex gap-2">
                            <a href="" class="btn btn-pdf btn-sm">
                                <span class="mdi mdi-file-pdf-box"></span>
                                Download PDF
                            </a>
                            <a href="" class="btn btn-success btn-sm">
                                <span class="mdi mdi-file-pdf-box"></span>
                                Download Excel
                            </a>
                            <a href="{{ route('outbound.return') }}" class="btn btn-info btn-sm">Return Order</a>
                            <a href="{{ route('outbound.create') }}" class="btn btn-primary btn-sm">Create Order</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
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
                                <select class="form-control" name="client">
                                    <option value="">-- Select Client --</option>
                                    @foreach($customer as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == request()->get('client') ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start" value="{{ request()->get('start', date('Y-m-01')) }}">
                            </div>
                            <div class="col-2">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end" value="{{ request()->get('end', date('Y-m-d')) }}">
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
                                    <th>Deliv Note Number</th>
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th>Client</th>
                                    <th>Deliv Loc</th>
                                    <th class="text-center">Deliv Dest</th>
                                    <th class="text-center">QTY Item</th>
                                    <th class="text-center">Status</th>
                                    <th>Order Date</th>
                                    <th>Created By</th>
                                    <th class="text-center">Download Doc</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($outbound as $index => $out)
                                <tr>
                                    <td>{{ $outbound->firstItem() + $index }}</td>
                                    <td>{{ $out->delivery_note_number }}</td>
                                    <td>{{ $out->purc_doc }}</td>
                                    <td>
                                        @foreach(json_decode($out->sales_docs) as $item)
                                            <div>{{ $item }}</div>
                                        @endforeach
                                    </td>
                                    <td>{{ $out->customer->name }}</td>
                                    <td>{{ $out->deliv_loc }}</td>
                                    <td class="text-center">
                                        @if($out->deliv_dest == 'client')
                                            Client
                                        @elseif($out->deliv_dest == 'pm room')
                                            PM Room
                                        @elseif($out->deliv_dest == 'general room')
                                            GR Room
                                        @elseif($out->deliv_dest == 'spare room')
                                            Spare Room
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold">{{ number_format($out->qty_item) }}</td>
                                    <td class="text-center">
                                        @if($out->status == 'outbound')
                                            <span class="badge bg-success-subtle text-success">Outbound</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">Return</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($out->created_at)->translatedFormat('d F Y H:i') }}</td>
                                    <td>{{ $out->user->name }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('outbound.download-excel', ['id' => $out->id]) }}" class="btn btn-success btn-sm">
                                                <i class="mdi mdi-file-excel" style="font-size: 14px;"></i>
                                            </a>
                                            <a href="{{ route('outbound.download-pdf', ['id' => $out->id]) }}" class="btn btn-pdf btn-sm" target="_blank">
                                                <i class="mdi mdi-file-pdf-box" style="font-size: 14px;"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('outbound.detail', ['id' => $out->id]) }}" class="btn btn-info btn-sm">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        @if ($outbound->hasPages())
                            <ul class="pagination">
                                @if ($outbound->onFirstPage())
                                    <li class="disabled"><span>&laquo; Previous</span></li>
                                @else
                                    <li><a href="{{ $outbound->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                @endif

                                @foreach ($outbound->links()->elements as $element)
                                    @if (is_string($element))
                                        <li class="disabled"><span>{{ $element }}</span></li>
                                    @endif

                                    @if (is_array($element))
                                        @foreach ($element as $page => $url)
                                            @if ($page == $outbound->currentPage())
                                                <li class="active"><span>{{ $page }}</span></li>
                                            @else
                                                <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @if ($outbound->hasMorePages())
                                    <li><a href="{{ $outbound->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
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
