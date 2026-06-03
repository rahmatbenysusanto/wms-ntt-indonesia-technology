@extends('layout.index')
@section('title', 'Dashboard Outbound')

@section('content')
    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #4facfe !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1 text-truncate">Total Outbound</p>
                            <h2 class="mb-0 fw-bold text-primary">{{ number_format($totalOutbound) }}</h2>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(79,172,254,0.12); color:#4facfe;">
                                <i class="bx bx-log-out-circle"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">All time shipments</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #f5576c !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1 text-truncate">Total QTY Shipped</p>
                            <h2 class="mb-0 fw-bold text-danger">{{ number_format($totalOutboundQty) }}</h2>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(245,87,108,0.12); color:#f5576c;">
                                <i class="bx bx-package"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">Total items shipped</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #43e97b !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1 text-truncate">This Month</p>
                            <h2 class="mb-0 fw-bold text-success">{{ number_format($outboundThisMonth) }}</h2>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(67,233,123,0.12); color:#43e97b;">
                                <i class="bx bx-calendar-check"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">Orders this month</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #a855f7 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1 text-truncate">Value This Month</p>
                            <h2 class="mb-0 fw-bold" style="color:#a855f7;">$ {{ number_format($outboundValueThisMonth, 0) }}</h2>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(168,85,247,0.12); color:#a855f7;">
                                <i class="bx bx-dollar"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">Estimated value</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row: Monthly Trend + Destination --}}
    <div class="row">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-bar-chart-alt-2 fs-18 text-primary"></i>
                        </div>
                        <h5 class="card-title mb-0">Monthly Outbound Trend</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartMonthlyOutbound" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-doughnut-chart fs-18 text-success"></i>
                        </div>
                        <h5 class="card-title mb-0">By Destination</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartByDestination" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Customers by Outbound --}}
    @if($byCustomer->isNotEmpty())
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-group fs-18 text-info"></i>
                        </div>
                        <h5 class="card-title mb-0">Top Customers by Outbound Volume</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartOutboundByCustomer" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                    {{-- Search Form --}}
                    <form action="{{ url()->current() }}" method="GET" class="mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fs-12 mb-1">Purc Doc</label>
                                <input type="text" class="form-control form-control-sm" name="purcDoc" value="{{ request()->get('purcDoc') }}" placeholder="Search...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-12 mb-1">Sales Doc</label>
                                <input type="text" class="form-control form-control-sm" name="salesDoc" value="{{ request()->get('salesDoc') }}" placeholder="Search...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fs-12 mb-1">Delivery Note</label>
                                <input type="text" class="form-control form-control-sm" name="number" value="{{ request()->get('number') }}" placeholder="Search...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fs-12 mb-1">Customer</label>
                                <select class="form-control form-control-sm select2Customer" name="customer">
                                    <option value="">-- All --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ request()->get('customer') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fs-12 mb-1">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm px-3"><i class="ri-search-line me-1"></i>Search</button>
                                    <a href="{{ url()->current() }}" class="btn btn-soft-danger btn-sm px-3"><i class="ri-close-line me-1"></i></a>
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
                                    <th>Sales Doc</th>
                                    <th>Deliv Note</th>
                                    <th>Destination</th>
                                    <th>Location</th>
                                    <th>Customer</th>
                                    <th class="text-center">QTY</th>
                                    <th>Total Price</th>
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
                                                @default<span class="badge bg-soft-dark">{{ $item->deliv_dest }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $item->deliv_loc ?? '-' }}</td>
                                        <td><span class="badge bg-soft-secondary text-dark">{{ $item->customer->name ?? '-' }}</span></td>
                                        <td class="text-center fw-semibold">{{ number_format($item->qty_item ?? 0) }}</td>
                                        <td class="fw-semibold">$ {{ number_format($item->price ?? 0, 2) }}</td>
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

@section('js')
    <script>
        $('.select2Customer').select2({
            placeholder: '-- Select Customer --',
            allowClear: true,
            width: '100%'
        });

        // 1. Monthly Outbound Trend
        const monthlyTrend = @json($monthlyTrend);
        if (monthlyTrend && monthlyTrend.length > 0) {
            const labels = monthlyTrend.map(i => i.month);
            const qtyData = monthlyTrend.map(i => i.total_qty || 0);
            const orderData = monthlyTrend.map(i => i.total_orders || 0);

            const options = {
                series: [
                    { name: 'QTY Shipped', type: 'column', data: qtyData },
                    { name: 'Orders', type: 'line', data: orderData }
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
                xaxis: { categories: labels, labels: { style: { fontSize: '11px' } } },
                yaxis: [
                    { title: { text: 'QTY Shipped' }, labels: { formatter: v => v.toLocaleString() } },
                    { opposite: true, title: { text: 'Orders' }, labels: { formatter: v => v.toLocaleString() } }
                ],
                legend: { position: 'top', horizontalAlign: 'right' },
                grid: { borderColor: '#f1f5f9' },
                tooltip: {
                    shared: true,
                    y: { formatter: (v, {seriesIndex}) => seriesIndex === 0 ? v.toLocaleString() + ' units' : v + ' orders' }
                }
            };

            new ApexCharts(document.querySelector('#chartMonthlyOutbound'), options).render();
        }

        // 2. By Destination Donut
        const byDestination = @json($byDestination);
        if (byDestination && byDestination.length > 0) {
            const labels = byDestination.map(i => {
                const map = { 'client': 'Client', 'general room': 'General Room', 'pm room': 'PM Room', 'spare room': 'Spare Room' };
                return map[i.deliv_dest] || i.deliv_dest;
            });
            const values = byDestination.map(i => parseInt(i.total) || 0);
            const destColors = {
                'client': '#4facfe',
                'general room': '#43e97b',
                'pm room': '#f97316',
                'spare room': '#a855f7'
            };
            const colors = byDestination.map(i => destColors[i.deliv_dest] || '#06b6d4');

            const options = {
                series: values,
                chart: { height: 300, type: 'donut', toolbar: { show: false } },
                labels: labels,
                colors: colors,
                legend: { position: 'bottom', fontSize: '12px' },
                dataLabels: { enabled: true, formatter: v => v.toFixed(1) + '%', style: { fontSize: '11px' } },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '60%',
                            labels: {
                                show: true,
                                name: { show: true, fontSize: '13px' },
                                value: { show: true, fontSize: '14px', formatter: v => parseInt(v).toLocaleString() },
                                total: { show: true, label: 'Total', formatter: () => values.reduce((a,b) => a+b, 0).toLocaleString() }
                            }
                        }
                    }
                },
                tooltip: { y: { formatter: v => v + ' shipments' } },
                responsive: [{
                    breakpoint: 480,
                    options: { chart: { height: 280 }, legend: { position: 'bottom' } }
                }]
            };

            new ApexCharts(document.querySelector('#chartByDestination'), options).render();
        }

        // 3. Top Customers by Outbound
        const byCustomer = @json($byCustomer);
        if (byCustomer && byCustomer.length > 0) {
            const names = byCustomer.map(i => {
                const n = i.name || 'Unknown';
                return n.length > 18 ? n.substring(0, 17) + '..' : n;
            });
            const orders = byCustomer.map(i => parseInt(i.total_orders) || 0);
            const qty = byCustomer.map(i => parseInt(i.total_qty) || 0);

            const options = {
                series: [
                    { name: 'Orders', data: orders },
                    { name: 'QTY Shipped', data: qty }
                ],
                chart: {
                    height: 280,
                    type: 'bar',
                    toolbar: { show: false },
                    stacked: false
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '60%',
                        borderRadius: 3,
                        borderRadiusApplication: 'around'
                    }
                },
                dataLabels: { enabled: false },
                colors: ['#4facfe', '#43e97b'],
                xaxis: { categories: names, labels: { style: { fontSize: '11px' } } },
                yaxis: { labels: { style: { fontSize: '11px' } } },
                legend: { position: 'top', horizontalAlign: 'right' },
                grid: { borderColor: '#f1f5f9' },
                tooltip: {
                    y: { formatter: (v, {seriesIndex}) => seriesIndex === 0 ? v + ' orders' : v.toLocaleString() + ' units' }
                }
            };

            new ApexCharts(document.querySelector('#chartOutboundByCustomer'), options).render();
        }
    </script>
@endsection
