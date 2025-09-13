@extends('layout.index')
@section('title', 'PM Outbound')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">PM Room Outbound</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">PM Room</a></li>
                        <li class="breadcrumb-item active">Outbound</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Outbound</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('pm-room.create') }}" class="btn btn-primary">Create Outbound</a>
                            <a href="{{ route('pm-room.return') }}" class="btn btn-info">Create Return</a>
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
                                <label class="form-label">Type</label>
                                <select name="type" class="form-control">
                                    <option value="">-- Select Type --</option>
                                    <option>Return</option>
                                    <option>Outbound</option>
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
                                    <th>Deliv Loc</th>
                                    <th class="text-center">Deliv Dest</th>
                                    <th class="text-center">QTY Item</th>
                                    <th class="text-center">Type</th>
                                    <th>Order Date</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($pmRoom as $index => $item)
                                <tr>
                                    <td>{{ $pmRoom->firstItem() + $index }}</td>
                                    <td>{{ $item->number }}</td>
                                    <td>{{ $item->purc_doc }}</td>
                                    <td>
                                        @foreach(json_decode($item->sales_docs) as $detail)
                                            <div>{{ $detail }}</div>
                                        @endforeach
                                    </td>
                                    <td>{{ $item->customer->name }}</td>
                                    <td>{{ $item->deliv_loc }}</td>
                                    <td class="text-center">{{ $item->deliv_dest }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->qty_item) }}</td>
                                    <td class="text-center">
                                        @if($item->status == 'outbound')
                                            <span class="badge bg-info-subtle text-info">Outbound</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">Return</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>
                                        <a href="{{ route('outbound.detail', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        @if ($pmRoom->hasPages())
                            <ul class="pagination">
                                @if ($pmRoom->onFirstPage())
                                    <li class="disabled"><span>&laquo; Previous</span></li>
                                @else
                                    <li><a href="{{ $pmRoom->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                @endif

                                @foreach ($pmRoom->links()->elements as $element)
                                    @if (is_string($element))
                                        <li class="disabled"><span>{{ $element }}</span></li>
                                    @endif

                                    @if (is_array($element))
                                        @foreach ($element as $page => $url)
                                            @if ($page == $pmRoom->currentPage())
                                                <li class="active"><span>{{ $page }}</span></li>
                                            @else
                                                <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @if ($pmRoom->hasMorePages())
                                    <li><a href="{{ $pmRoom->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
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
