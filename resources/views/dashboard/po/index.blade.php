@extends('layout.index')
@section('title', 'Dashboard PO')

@section('content')
    {{-- Stats Cards Row --}}
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #4facfe !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1 text-truncate">Total Purchase Orders</p>
                            <h2 class="mb-0 fw-bold text-primary">{{ number_format($totalPO) }}</h2>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(79,172,254,0.12); color: #4facfe;">
                                <i class="bx bx-cart"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">All statuses</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #43e97b !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1 text-truncate">Total QTY (QC Passed)</p>
                            <h2 class="mb-0 fw-bold text-success">{{ number_format($totalQtyPO) }}</h2>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(67,233,123,0.12); color: #43e97b;">
                                <i class="bx bx-check-double"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">From {{ number_format($totalQtyPOAll) }} ordered</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #fddb92 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1 text-truncate">Stock in Warehouse</p>
                            <h2 class="mb-0 fw-bold text-warning">{{ number_format($totalStockAll) }}</h2>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(253,219,146,0.2); color: #d4a017;">
                                <i class="bx bx-package"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">Items in inventory</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #a855f7 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1 text-truncate">Total Customers</p>
                            <h2 class="mb-0 fw-bold" style="color:#a855f7;">{{ number_format($customer->count()) }}</h2>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(168,85,247,0.12); color: #a855f7;">
                                <i class="bx bx-group"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">Active clients</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Row: PO by Customer --}}
    @if($poByCustomer->isNotEmpty())
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-bar-chart-alt fs-18 text-primary"></i>
                        </div>
                        <h5 class="card-title mb-0">Purchase Orders by Customer</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartPoByCustomer" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- PO Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                <i class="bx bx-list-ul fs-18 text-secondary"></i>
                            </div>
                            <h5 class="card-title mb-0">List Purchase Order</h5>
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $listPO->total() }} Records</span>
                    </div>
                </div>
                <div class="card-body p-3">
                    {{-- Filter Form --}}
                    <form action="{{ url()->current() }}" method="GET" class="mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fs-12 mb-1">Purc Doc</label>
                                <input type="text" class="form-control form-control-sm" name="purcDoc" value="{{ request()->get('purcDoc') }}" placeholder="Search Purc Doc...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-12 mb-1">Customer</label>
                                <select class="form-control form-control-sm select2Customer" name="client">
                                    <option value="">-- All Customers --</option>
                                    @foreach($customer as $item)
                                        <option {{ request()->get('client') == $item->name ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-12 mb-1">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm px-3"><i class="ri-search-line me-1"></i>Search</button>
                                    <a href="{{ url()->current() }}" class="btn btn-soft-danger btn-sm px-3"><i class="ri-close-line me-1"></i>Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Purc Doc</th>
                                    <th>Client</th>
                                    <th class="text-center">SO</th>
                                    <th class="text-center">QTY PO</th>
                                    <th class="text-center">Inbound</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-center">Outbound</th>
                                    <th>Created</th>
                                    <th>By</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($listPO as $index => $item)
                                    <tr>
                                        <td class="ps-3 text-muted">{{ $listPO->firstItem() + $index }}</td>
                                        <td class="fw-medium">{{ $item->purc_doc }}</td>
                                        <td><span class="badge bg-soft-secondary text-dark">{{ $item->customer->name }}</span></td>
                                        <td class="text-center fw-semibold">
                                            <span class="badge bg-soft-primary text-primary">{{ number_format($item->sales_doc_qty) }}</span>
                                        </td>
                                        <td class="text-center fw-semibold">{{ number_format($item->item_qty) }}</td>
                                        <td class="text-center fw-semibold text-success">{{ number_format($item->qty_po) }}</td>
                                        <td class="text-center">
                                            @php
                                                $pct = $item->item_qty > 0 ? round(($item->stock / $item->item_qty) * 100) : 0;
                                                $barColor = $pct >= 75 ? 'bg-success' : ($pct >= 40 ? 'bg-warning' : 'bg-danger');
                                            @endphp
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-semibold" style="min-width:60px;">{{ number_format($item->stock) }}</span>
                                                <div class="progress progress-sm flex-grow-1" style="height:6px;min-width:60px;">
                                                    <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $pct }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $pct }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-center fw-semibold text-info">{{ number_format($item->qty_outbound) }}</td>
                                        <td class="text-muted fs-13">{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}</td>
                                        <td>{{ $item->user->name ?? '-' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('dashboard.po.detail', ['id' => $item->id]) }}" class="btn btn-soft-info btn-sm px-2">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">No purchase orders found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">Showing {{ $listPO->firstItem() ?? 0 }} to {{ $listPO->lastItem() ?? 0 }} of {{ $listPO->total() }} entries</small>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                @if ($listPO->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $listPO->previousPageUrl() }}&per_page={{ request('per_page', 10) }}">&laquo;</a></li>
                                @endif

                                @foreach ($listPO->getUrlRange(max(1, $listPO->currentPage() - 2), min($listPO->lastPage(), $listPO->currentPage() + 2)) as $page => $url)
                                    <li class="page-item {{ $page == $listPO->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a>
                                    </li>
                                @endforeach

                                @if ($listPO->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $listPO->nextPageUrl() }}&per_page={{ request('per_page', 10) }}">&raquo;</a></li>
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

@section('js')
    <script>
        $('.select2Customer').select2({
            placeholder: '-- Select Customer --',
            allowClear: true,
            width: '100%'
        });

        // PO by Customer chart
        const poByCustomer = @json($poByCustomer);
        if (poByCustomer && poByCustomer.length > 0) {
            const names = poByCustomer.map(i => i.name || 'Unknown');
            const poCounts = poByCustomer.map(i => parseInt(i.total_po) || 0);
            const qtyCounts = poByCustomer.map(i => parseInt(i.total_qty) || 0);

            const options = {
                series: [
                    { name: 'Total PO', type: 'column', data: poCounts },
                    { name: 'Total QTY', type: 'line', data: qtyCounts }
                ],
                chart: {
                    height: 300,
                    type: 'line',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                stroke: { width: [0, 3], curve: 'smooth' },
                plotOptions: {
                    bar: { columnWidth: '45%', borderRadius: 4, borderRadiusApplication: 'around' }
                },
                dataLabels: {
                    enabled: true,
                    enabledOnSeries: [1],
                    style: { fontSize: '11px' }
                },
                colors: ['#4facfe', '#43e97b'],
                xaxis: { categories: names, labels: { style: { fontSize: '11px' } } },
                yaxis: [
                    { title: { text: 'Total PO' }, labels: { formatter: v => v.toLocaleString() } },
                    { opposite: true, title: { text: 'Total QTY' }, labels: { formatter: v => v.toLocaleString() } }
                ],
                legend: { position: 'top', horizontalAlign: 'right' },
                grid: { borderColor: '#f1f5f9' },
                tooltip: {
                    shared: true,
                    y: { formatter: v => v.toLocaleString() }
                }
            };

            new ApexCharts(document.querySelector('#chartPoByCustomer'), options).render();
        }
    </script>
@endsection
