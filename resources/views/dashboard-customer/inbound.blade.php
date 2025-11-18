@extends('layout.index')
@section('title', 'Dashboard Inbound')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card p-2">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex gap-2">
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-dark">Main Dashboard</a>
                            <a href="{{ route('customer.inbound') }}" class="btn btn-info">Inbound</a>
                            <a href="{{ route('customer.aging') }}" class="btn btn-dark">Aging</a>
                            <a href="{{ route('customer.outbound') }}" class="btn btn-dark">Outbound</a>
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
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Purchase Doc</th>
                                    <th>Customer</th>
                                    <th>Vendor</th>
                                    <th class="text-center">QTY Material</th>
                                    <th class="text-center">QTY Item</th>
                                    <th class="text-center">Status</th>
                                    <th>Created By</th>
                                    <th>Created Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($purchaseOrder as $index => $item)
                                <tr>
                                    <td>{{ $purchaseOrder->firstItem() + $index }}</td>
                                    <td>
                                        <a href="{{ route('customer.inbound.detail', ['id' => $item->id]) }}">{{ $item->purc_doc }}</a>
                                    </td>
                                    <td>{{ $item->customer->name }}</td>
                                    <td>{{ $item->vendor->name }}</td>
                                    <td class="text-center">{{ number_format($item->material_qty) }}</td>
                                    <td class="text-center">{{ number_format($item->item_qty) }}</td>
                                    <td class="text-center">
                                        @switch($item->status)
                                            @case('new')
                                                <span class="badge bg-success-subtle text-success">New</span>
                                                @break
                                            @case('open')
                                                <span class="badge bg-info-subtle text-info">Open</span>
                                                @break
                                            @case('process')
                                                <span class="badge bg-primary-subtle text-primary">In Process</span>
                                                @break
                                            @case('done')
                                                <span class="badge bg-secondary-subtle text-secondary">Done</span>
                                                @break
                                            @case('cancel')
                                                <span class="badge bg-danger-subtle text-danger">Cancel</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        @if ($purchaseOrder->hasPages())
                            <ul class="pagination">
                                @if ($purchaseOrder->onFirstPage())
                                    <li class="disabled"><span>&laquo; Previous</span></li>
                                @else
                                    <li><a href="{{ $purchaseOrder->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                @endif

                                @foreach ($purchaseOrder->links()->elements as $element)
                                    @if (is_string($element))
                                        <li class="disabled"><span>{{ $element }}</span></li>
                                    @endif

                                    @if (is_array($element))
                                        @foreach ($element as $page => $url)
                                            @if ($page == $purchaseOrder->currentPage())
                                                <li class="active"><span>{{ $page }}</span></li>
                                            @else
                                                <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @if ($purchaseOrder->hasMorePages())
                                    <li><a href="{{ $purchaseOrder->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
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
