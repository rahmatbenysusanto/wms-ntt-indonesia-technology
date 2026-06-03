@extends('layout.index')
@section('title', 'Dashboard Aging')

@section('content')
    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #f5576c !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1 text-truncate">Total Aging Value</p>
                            <h2 class="mb-0 fw-bold text-danger">$ {{ number_format($totalAgingValue, 2) }}</h2>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(245,87,108,0.12); color:#f5576c;">
                                <i class="bx bx-dollar-circle"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">Across all aging buckets</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #4facfe !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1 text-truncate">Total Items</p>
                            <h2 class="mb-0 fw-bold text-primary">{{ number_format($totalAgingQty) }}</h2>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(79,172,254,0.12); color:#4facfe;">
                                <i class="bx bx-cube"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">Items with aging tracking</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            @php
                $oldestPct = $totalAgingQty > 0 ? round(($agingType4->qty / $totalAgingQty) * 100, 1) : 0;
            @endphp
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #f97316 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1 text-truncate">Items > 365 Days</p>
                            <h2 class="mb-0 fw-bold" style="color:#f97316;">{{ number_format($agingType4->qty) }} <small class="fs-13 text-muted">({{ $oldestPct }}%)</small></h2>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(249,115,22,0.12); color:#f97316;">
                                <i class="bx bx-time"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">Requires immediate attention</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            @php
                $youngPct = $totalAgingQty > 0 ? round(($agingType1->qty / $totalAgingQty) * 100, 1) : 0;
            @endphp
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #43e97b !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1 text-truncate">Items 0-90 Days</p>
                            <h2 class="mb-0 fw-bold text-success">{{ number_format($agingType1->qty) }} <small class="fs-13 text-muted">({{ $youngPct }}%)</small></h2>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(67,233,123,0.12); color:#43e97b;">
                                <i class="bx bx-check-shield"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">Recently added inventory</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Aging Trend Line Chart --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-trending-up fs-18 text-danger"></i>
                        </div>
                        <h5 class="card-title mb-0">Aging Trend (Last 12 Months)</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartAgingTrend" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Donut Charts + Aging Table --}}
    <div class="row">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fs-14"><i class="bx bx-doughnut-chart text-primary me-1"></i> Aging by QTY</h5>
                </div>
                <div class="card-body">
                    <div id="chartAgingQty" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fs-14"><i class="bx bx-doughnut-chart text-danger me-1"></i> Aging by Total Price</h5>
                </div>
                <div class="card-body">
                    <div id="chartAgingPrice" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fs-14"><i class="bx bx-table text-info me-1"></i> Aging Summary Table</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light fs-12">
                            <tr>
                                <th class="ps-3">Bucket</th>
                                <th class="text-end">Price ($)</th>
                                <th class="text-center">QTY</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-3"><span class="badge bg-soft-primary text-primary">1 - 90 Day</span></td>
                                <td class="text-end fw-semibold">$ {{ number_format($agingType1->total ?? 0, 2) }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('dashboard.aging.detail', ['type' => 1]) }}" class="text-primary text-decoration-none">{{ number_format($agingType1->qty) }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3"><span class="badge bg-soft-warning text-warning">91 - 180 Day</span></td>
                                <td class="text-end fw-semibold">$ {{ number_format($agingType2->total ?? 0, 2) }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('dashboard.aging.detail', ['type' => 2]) }}" class="text-warning text-decoration-none">{{ number_format($agingType2->qty) }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3"><span class="badge bg-soft-orange text-orange" style="color:#f97316!important;">181 - 365 Day</span></td>
                                <td class="text-end fw-semibold">$ {{ number_format($agingType3->total ?? 0, 2) }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('dashboard.aging.detail', ['type' => 3]) }}" class="text-decoration-none" style="color:#f97316;">{{ number_format($agingType3->qty) }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3"><span class="badge bg-soft-danger text-danger">> 365 Day</span></td>
                                <td class="text-end fw-semibold">$ {{ number_format($agingType4->total ?? 0, 2) }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('dashboard.aging.detail', ['type' => 4]) }}" class="text-danger text-decoration-none">{{ number_format($agingType4->qty) }}</a>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td class="ps-3 fw-bold">Total</td>
                                <td class="text-end fw-bold">$ {{ number_format($totalAgingValue, 2) }}</td>
                                <td class="text-center fw-bold">{{ number_format($totalAgingQty) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Storage Aging Breakdown --}}
    @if($storageAging->isNotEmpty())
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                <i class="bx bx-layer fs-18 text-secondary"></i>
                            </div>
                            <h5 class="card-title mb-0">Aging by Storage Location</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartStorageAging" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Action Buttons --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <span class="fw-medium me-2">Export:</span>
                        <a href="{{ route('dashboard.aging.download.pdf', ['type' => 1]) }}" class="btn btn-soft-danger btn-sm" target="_blank">
                            <i class="ri-file-pdf-line me-1"></i> PDF (1-90 Day)
                        </a>
                        <a href="{{ route('dashboard.aging.download.pdf', ['type' => 2]) }}" class="btn btn-soft-danger btn-sm" target="_blank">
                            <i class="ri-file-pdf-line me-1"></i> PDF (91-180 Day)
                        </a>
                        <a href="{{ route('dashboard.aging.download.pdf', ['type' => 3]) }}" class="btn btn-soft-danger btn-sm" target="_blank">
                            <i class="ri-file-pdf-line me-1"></i> PDF (181-365 Day)
                        </a>
                        <a href="{{ route('dashboard.aging.download.pdf', ['type' => 4]) }}" class="btn btn-soft-danger btn-sm" target="_blank">
                            <i class="ri-file-pdf-line me-1"></i> PDF (>365 Day)
                        </a>
                        <a href="{{ route('dashboard.aging.download.excel', ['type' => 1]) }}" class="btn btn-soft-success btn-sm">
                            <i class="ri-file-excel-line me-1"></i> Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // Colors helper
        function getChartColors(id) {
            const el = document.getElementById(id);
            if (!el) return ['#4facfe','#43e97b','#f97316','#f5576c'];
            const colors = el.getAttribute('data-colors');
            try {
                return JSON.parse(colors).map(c => getComputedStyle(document.documentElement).getPropertyValue(c.trim()) || c.trim());
            } catch(e) {
                return ['#4facfe','#43e97b','#f97316','#f5576c'];
            }
        }

        // 1. Aging Trend Chart
        const agingTrend = @json($agingTrend);
        if (agingTrend && agingTrend.length > 0) {
            const months = agingTrend.map(i => {
                const [y, m] = i.month.split('-');
                const date = new Date(y, parseInt(m)-1);
                return date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
            });
            const qtyData = agingTrend.map(i => parseInt(i.total_qty) || 0);
            const valueData = agingTrend.map(i => parseFloat(i.total_value) || 0);

            const options = {
                series: [
                    { name: 'Items', type: 'column', data: qtyData },
                    { name: 'Value ($)', type: 'line', data: valueData }
                ],
                chart: {
                    height: 320,
                    type: 'line',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                stroke: { width: [0, 3], curve: 'smooth' },
                plotOptions: {
                    bar: { columnWidth: '50%', borderRadius: 4, borderRadiusApplication: 'around' }
                },
                dataLabels: { enabled: false },
                colors: ['#4facfe', '#f5576c'],
                xaxis: { categories: months, labels: { style: { fontSize: '11px' } } },
                yaxis: [
                    { title: { text: 'Items' }, labels: { formatter: v => v.toLocaleString() } },
                    { opposite: true, title: { text: 'Value ($)' }, labels: { formatter: v => '$ ' + (v/1000).toFixed(0) + 'k' } }
                ],
                legend: { position: 'top', horizontalAlign: 'right' },
                grid: { borderColor: '#f1f5f9' },
                tooltip: {
                    shared: true,
                    y: { formatter: (v, {seriesIndex}) => seriesIndex === 0 ? v.toLocaleString() + ' items' : '$ ' + v.toLocaleString() }
                }
            };

            new ApexCharts(document.querySelector('#chartAgingTrend'), options).render();
        }

        // 2. Aging by QTY Donut
        const agingQtyOptions = {
            series: [
                parseInt('{{ $agingType1->qty ?? 0 }}') || 0,
                parseInt('{{ $agingType2->qty ?? 0 }}') || 0,
                parseInt('{{ $agingType3->qty ?? 0 }}') || 0,
                parseInt('{{ $agingType4->qty ?? 0 }}') || 0
            ],
            chart: { height: 280, type: 'donut', toolbar: { show: false } },
            labels: ['1-90 Day', '91-180 Day', '181-365 Day', '> 365 Day'],
            colors: ['#4facfe', '#43e97b', '#f97316', '#f5576c'],
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
                            total: { show: true, label: 'Total', formatter: () => '{{ number_format($totalAgingQty) }}' }
                        }
                    }
                }
            },
            tooltip: { y: { formatter: v => v.toLocaleString() + ' items' } }
        };
        new ApexCharts(document.querySelector('#chartAgingQty'), agingQtyOptions).render();

        // 3. Aging by Price Donut
        const agingPriceOptions = {
            series: [
                parseFloat('{{ $agingType1->total ?? 0 }}') || 0,
                parseFloat('{{ $agingType2->total ?? 0 }}') || 0,
                parseFloat('{{ $agingType3->total ?? 0 }}') || 0,
                parseFloat('{{ $agingType4->total ?? 0 }}') || 0
            ],
            chart: { height: 280, type: 'donut', toolbar: { show: false } },
            labels: ['1-90 Day', '91-180 Day', '181-365 Day', '> 365 Day'],
            colors: ['#4facfe', '#43e97b', '#f97316', '#f5576c'],
            legend: { position: 'bottom', fontSize: '12px' },
            dataLabels: { enabled: true, formatter: v => v.toFixed(1) + '%', style: { fontSize: '11px' } },
            plotOptions: {
                pie: {
                    donut: {
                        size: '60%',
                        labels: {
                            show: true,
                            name: { show: true, fontSize: '13px' },
                            value: {
                                show: true,
                                fontSize: '14px',
                                formatter: v => '$ ' + parseFloat(v).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})
                            },
                            total: { show: true, label: 'Total', formatter: () => '$ {{ number_format($totalAgingValue, 0) }}' }
                        }
                    }
                }
            },
            tooltip: { y: { formatter: v => '$ ' + parseFloat(v).toLocaleString() } }
        };
        new ApexCharts(document.querySelector('#chartAgingPrice'), agingPriceOptions).render();

        // 4. Storage Aging Breakdown (horizontal bar)
        const storageAging = @json($storageAging);
        if (storageAging && storageAging.length > 0) {
            const names = storageAging.map(i => {
                const s = i.storage_name || '';
                return s.length > 25 ? s.substring(0, 24) + '..' : s;
            });
            const qtyVals = storageAging.map(i => parseInt(i.total_qty) || 0);
            const valVals = storageAging.map(i => parseFloat(i.total_value) || 0);

            const options = {
                series: [
                    { name: 'QTY', data: qtyVals },
                    { name: 'Value ($)', data: valVals }
                ],
                chart: {
                    height: 320,
                    type: 'bar',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '60%',
                        borderRadius: 3,
                        borderRadiusApplication: 'around'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                colors: ['#4facfe', '#f5576c'],
                xaxis: {
                    categories: names,
                    labels: { style: { fontSize: '10px' } }
                },
                yaxis: {
                    labels: { style: { fontSize: '10px' } }
                },
                legend: { position: 'top', horizontalAlign: 'right' },
                grid: { borderColor: '#f1f5f9' },
                tooltip: {
                    y: {
                        formatter: (v, {seriesIndex}) =>
                            seriesIndex === 0 ? v.toLocaleString() + ' items' : '$ ' + v.toLocaleString()
                    }
                }
            };

            new ApexCharts(document.querySelector('#chartStorageAging'), options).render();
        }
    </script>
@endsection
