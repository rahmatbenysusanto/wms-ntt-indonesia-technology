@extends('layout.index')
@section('title', 'Dashboard Outbound')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card p-2">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex gap-2">
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-dark">Main Dashboard</a>
                            <a href="{{ route('customer.inbound') }}" class="btn btn-dark">Inbound</a>
                            <a href="{{ route('customer.aging') }}" class="btn btn-dark">Aging</a>
                            <a href="{{ route('customer.outbound') }}" class="btn btn-info">Outbound</a>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="d-flex justify-content-end">
                            <select class="form-control" id="customer" onchange="changeCustomer(this.value)">
                                <option value="">-- All Customer --</option>
                                @foreach($customer as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">

                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Purc Doc</th>
                            <th>Sales Doc</th>
                            <th>Deliv Note Number</th>
                            <th class="text-center">Deliv Dest</th>
                            <th>Deliv Loc</th>
                            <th>Customer</th>
                            <th class="text-center">QTY</th>
                            <th class="text-center">Total Price</th>
                            <th>Deliv Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($outbound as $index => $item)
                            <tr>
                                <td>{{ $outbound->firstItem() + $index }}</td>
                                <td>{{ $item->purc_doc }}</td>
                                <td>
                                    @foreach(json_decode($item->sales_docs) as $salesDoc)
                                        <div>{{ $salesDoc }}</div>
                                    @endforeach
                                </td>
                                <td>{{ $item->delivery_note_number }}</td>
                                <td class="text-center">
                                    @switch($item->deliv_dest)
                                        @case('client')
                                            <div>Client</div>
                                            @break
                                        @case('general room')
                                            <div>General Room</div>
                                            @break
                                        @case('pm room')
                                            <div>PM Room</div>
                                            @break
                                        @case('spare room')
                                            <div>Spare Room</div>
                                            @break
                                    @endswitch
                                </td>
                                <td>{{ $item->deliv_loc }}</td>
                                <td>{{ $item->customer->name }}</td>
                                <td class="text-center fw-bold">{{ number_format($item->qty_item) }}</td>
                                <td class="text-center">${{ number_format($item->price) }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->delivery_date)->translatedFormat('d F Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('dashboard.outbound.detail', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
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
