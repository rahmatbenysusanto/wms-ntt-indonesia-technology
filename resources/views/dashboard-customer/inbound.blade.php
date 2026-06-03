@extends('layout.index')
@section('title', 'Dashboard Inbound')

@section('content')
    {{-- Modern Nav Pills --}}
    @include('dashboard-customer.partials.nav-pills', ['navActive' => 'inbound'])

    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left:4px solid #4facfe!important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1">Total PO</p>
                            <h3 class="mb-0 fw-bold text-primary">{{ number_format($purchaseOrder->total()) }}</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background:rgba(79,172,254,0.12);color:#4facfe;">
                                <i class="bx bx-cart"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left:4px solid #43e97b!important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1">Total Material QTY</p>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($purchaseOrder->sum('material_qty')) }}</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background:rgba(67,233,123,0.12);color:#43e97b;">
                                <i class="bx bx-package"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left:4px solid #fddb92!important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1">Total Item QTY</p>
                            <h3 class="mb-0 fw-bold text-warning">{{ number_format($purchaseOrder->sum('item_qty')) }}</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background:rgba(253,219,146,0.2);color:#d4a017;">
                                <i class="bx bx-box"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left:4px solid #a855f7!important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1">Customers</p>
                            <h3 class="mb-0 fw-bold" style="color:#a855f7;">{{ number_format($customer->count()) }}</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background:rgba(168,85,247,0.12);color:#a855f7;">
                                <i class="bx bx-group"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inbound Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                <i class="bx bx-package-down fs-18 text-secondary"></i>
                            </div>
                            <h5 class="card-title mb-0">Inbound Purchase Orders</h5>
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $purchaseOrder->total() }} Records</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
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
                                @forelse($purchaseOrder as $index => $item)
                                    <tr>
                                        <td class="ps-3 text-muted">{{ $purchaseOrder->firstItem() + $index }}</td>
                                        <td>
                                            <a href="{{ route('customer.inbound.detail', ['id' => $item->id]) }}" class="fw-medium text-primary">
                                                {{ $item->purc_doc }}
                                            </a>
                                        </td>
                                        <td><span class="badge bg-soft-secondary text-dark">{{ $item->customer->name ?? '-' }}</span></td>
                                        <td>{{ $item->vendor->name ?? '-' }}</td>
                                        <td class="text-center fw-semibold">{{ number_format($item->material_qty) }}</td>
                                        <td class="text-center fw-semibold">{{ number_format($item->item_qty) }}</td>
                                        <td class="text-center">
                                            @switch($item->status)
                                                @case('new')
                                                    <span class="badge bg-info-subtle text-info">New</span>
                                                    @break
                                                @case('open')
                                                    <span class="badge bg-primary-subtle text-primary">Open</span>
                                                    @break
                                                @case('process')
                                                    <span class="badge bg-warning-subtle text-warning">Process</span>
                                                    @break
                                                @case('done')
                                                    <span class="badge bg-success-subtle text-success">Done</span>
                                                    @break
                                                @case('cancel')
                                                    <span class="badge bg-danger-subtle text-danger">Cancel</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary-subtle text-secondary">{{ $item->status }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $item->user->name ?? '-' }}</td>
                                        <td class="text-muted fs-13">{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">No purchase orders found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">Showing {{ $purchaseOrder->firstItem() ?? 0 }} to {{ $purchaseOrder->lastItem() ?? 0 }} of {{ $purchaseOrder->total() }} entries</small>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                @if ($purchaseOrder->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $purchaseOrder->previousPageUrl() }}&per_page={{ request('per_page', 10) }}">&laquo;</a></li>
                                @endif

                                @foreach ($purchaseOrder->getUrlRange(max(1, $purchaseOrder->currentPage() - 2), min($purchaseOrder->lastPage(), $purchaseOrder->currentPage() + 2)) as $page => $url)
                                    <li class="page-item {{ $page == $purchaseOrder->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a>
                                    </li>
                                @endforeach

                                @if ($purchaseOrder->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $purchaseOrder->nextPageUrl() }}&per_page={{ request('per_page', 10) }}">&raquo;</a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
