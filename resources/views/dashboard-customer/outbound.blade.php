@extends('layout.index')
@section('title', 'Dashboard Outbound')

@section('content')
    {{-- Modern Nav Pills --}}
    @include('dashboard-customer.partials.nav-pills', ['navActive' => 'outbound'])

    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left:4px solid #4facfe!important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1">Total Outbound</p>
                            <h3 class="mb-0 fw-bold text-primary">{{ number_format($outbound->total()) }}</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background:rgba(79,172,254,0.12);color:#4facfe;">
                                <i class="bx bx-log-out-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left:4px solid #f5576c!important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1">Total QTY</p>
                            <h3 class="mb-0 fw-bold text-danger">{{ number_format($outbound->sum('qty_item')) }}</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background:rgba(245,87,108,0.12);color:#f5576c;">
                                <i class="bx bx-package"></i>
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
                            <p class="text-muted fs-12 mb-1">Customers</p>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($customer->count()) }}</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background:rgba(67,233,123,0.12);color:#43e97b;">
                                <i class="bx bx-group"></i>
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
                            <p class="text-muted fs-12 mb-1">Total Destinations</p>
                            <h3 class="mb-0 fw-bold" style="color:#a855f7;">{{ number_format($outbound->groupBy('deliv_dest')->count()) }}</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background:rgba(168,85,247,0.12);color:#a855f7;">
                                <i class="bx bx-map"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Outbound Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                <i class="bx bx-list-ul fs-18 text-secondary"></i>
                            </div>
                            <h5 class="card-title mb-0">Outbound Shipments</h5>
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $outbound->total() }} Records</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th>Deliv Note</th>
                                    <th>Destination</th>
                                    <th>Location</th>
                                    <th>Customer</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-end">Total Price</th>
                                    <th>Deliv Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outbound as $index => $item)
                                    <tr>
                                        <td class="ps-3 text-muted">{{ $outbound->firstItem() + $index }}</td>
                                        <td class="fw-medium">{{ $item->purc_doc }}</td>
                                        <td>
                                            @foreach(json_decode($item->sales_docs ?? '[]') as $salesDoc)
                                                <span class="badge bg-soft-secondary text-dark me-1">{{ $salesDoc }}</span>
                                            @endforeach
                                        </td>
                                        <td><span class="badge bg-soft-info text-dark">{{ $item->delivery_note_number ?? '-' }}</span></td>
                                        <td>
                                            @switch($item->deliv_dest)
                                                @case('client')<span class="badge bg-soft-primary text-primary">Client</span>@break
                                                @case('general room')<span class="badge bg-soft-success text-success">General Room</span>@break
                                                @case('pm room')<span class="badge bg-soft-warning text-warning">PM Room</span>@break
                                                @case('spare room')<span class="badge bg-soft-secondary">Spare Room</span>@break
                                                @default<span class="badge bg-soft-dark">{{ $item->deliv_dest ?? '-' }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $item->deliv_loc ?? '-' }}</td>
                                        <td><span class="badge bg-soft-secondary text-dark">{{ $item->customer->name ?? '-' }}</span></td>
                                        <td class="text-center fw-semibold">{{ number_format($item->qty_item ?? 0) }}</td>
                                        <td class="text-end fw-semibold">${{ number_format($item->price ?? 0, 2) }}</td>
                                        <td class="text-muted fs-13">{{ $item->delivery_date ? \Carbon\Carbon::parse($item->delivery_date)->translatedFormat('d M Y') : '-' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('dashboard.outbound.detail', ['id' => $item->id]) }}" class="btn btn-soft-info btn-sm px-2">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">No outbound shipments found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">Showing {{ $outbound->firstItem() ?? 0 }} to {{ $outbound->lastItem() ?? 0 }} of {{ $outbound->total() }} entries</small>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                @if ($outbound->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $outbound->previousPageUrl() }}&per_page={{ request('per_page', 10) }}">&laquo;</a></li>
                                @endif

                                @foreach ($outbound->getUrlRange(max(1, $outbound->currentPage() - 2), min($outbound->lastPage(), $outbound->currentPage() + 2)) as $page => $url)
                                    <li class="page-item {{ $page == $outbound->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a>
                                    </li>
                                @endforeach

                                @if ($outbound->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $outbound->nextPageUrl() }}&per_page={{ request('per_page', 10) }}">&raquo;</a></li>
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
