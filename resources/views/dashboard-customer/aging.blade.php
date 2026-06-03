@extends('layout.index')
@section('title', 'Dashboard Aging')

@section('content')
    {{-- Modern Nav Pills --}}
    @include('dashboard-customer.partials.nav-pills', ['navActive' => 'aging'])

    {{-- Stats Cards --}}
    <div class="row" id="agingStats">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left:4px solid #f5576c!important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1">1-90 Day (QTY)</p>
                            <h3 class="mb-0 fw-bold text-danger" id="aging-qty-1">0</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background:rgba(245,87,108,0.12);color:#f5576c;">
                                <i class="bx bx-check-shield"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">Newest inventory</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left:4px solid #f97316!important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1">91-180 Day (QTY)</p>
                            <h3 class="mb-0 fw-bold" style="color:#f97316;" id="aging-qty-2">0</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background:rgba(249,115,22,0.12);color:#f97316;">
                                <i class="bx bx-time"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">Medium age</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left:4px solid #fddb92!important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1">181-365 Day (QTY)</p>
                            <h3 class="mb-0 fw-bold text-warning" id="aging-qty-3">0</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background:rgba(253,219,146,0.2);color:#d4a017;">
                                <i class="bx bx-time-five"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">Old inventory</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left:4px solid #a855f7!important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-12 mb-1">&gt;365 Day (QTY)</p>
                            <h3 class="mb-0 fw-bold" style="color:#a855f7;" id="aging-qty-4">0</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background:rgba(168,85,247,0.12);color:#a855f7;">
                                <i class="bx bx-alarm-exclamation"></i>
                            </span>
                        </div>
                    </div>
                    <small class="text-muted">Requires attention</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts: QTY Donut + Price Donut --}}
    <div class="row">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-doughnut-chart fs-18 text-primary"></i>
                        </div>
                        <h5 class="card-title mb-0">Aging by QTY</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-aging-qty" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-doughnut-chart fs-18 text-danger"></i>
                        </div>
                        <h5 class="card-title mb-0">Aging by Total Price</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-aging-price" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inventory by Storage Location --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-layer fs-18 text-secondary"></i>
                        </div>
                        <h5 class="card-title mb-0">Inventory by Storage Location</h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Storage Location</th>
                                    <th class="text-end">1 - 90 Day ($)</th>
                                    <th class="text-end">91 - 180 Day ($)</th>
                                    <th class="text-end">181 - 365 Day ($)</th>
                                    <th class="text-end">> 365 Day ($)</th>
                                </tr>
                            </thead>
                            <tbody id="aging-table-body">
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading data...
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td class="ps-3 fw-bold">Total All Locations</td>
                                    <td class="text-end fw-bold" id="aging-total-1">$0</td>
                                    <td class="text-end fw-bold" id="aging-total-2">$0</td>
                                    <td class="text-end fw-bold" id="aging-total-3">$0</td>
                                    <td class="text-end fw-bold" id="aging-total-4">$0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function formatUSD(value) {
            return '$ ' + Number(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function formatCompact(value) {
            return Number(value).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        const chartColors = ['#4facfe', '#f97316', '#fddb92', '#f5576c'];

        // 1. Aging by QTY Chart
        $.ajax({
            url: '{{ route('customer.aging.chart.qty') }}',
            method: 'GET',
            success: (res) => {
                const series = (Array.isArray(res) ? res : [0,0,0,0]).map(v => parseInt(v) || 0);

                // Update stat cards
                document.getElementById('aging-qty-1').innerText = formatCompact(series[0]);
                document.getElementById('aging-qty-2').innerText = formatCompact(series[1]);
                document.getElementById('aging-qty-3').innerText = formatCompact(series[2]);
                document.getElementById('aging-qty-4').innerText = formatCompact(series[3]);

                // QTY Donut
                const qtyOptions = {
                    series: series,
                    chart: { height: 300, type: 'donut', toolbar: { show: false } },
                    labels: ['1-90 Day', '91-180 Day', '181-365 Day', '> 365 Day'],
                    colors: chartColors,
                    legend: { position: 'bottom' },
                    dataLabels: { enabled: true, formatter: v => v.toFixed(1) + '%', style: { fontSize: '12px' } },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '62%',
                                labels: {
                                    show: true,
                                    name: { show: true, fontSize: '14px' },
                                    value: { show: true, fontSize: '15px', formatter: v => parseInt(v).toLocaleString() },
                                    total: { show: true, label: 'Total', formatter: () => series.reduce((a,b) => a+b, 0).toLocaleString() }
                                }
                            }
                        }
                    },
                    tooltip: { y: { formatter: v => v.toLocaleString() + ' items' } }
                };
                new ApexCharts(document.querySelector('#chart-aging-qty'), qtyOptions).render();
            }
        });

        // 2. Aging by Price Chart
        $.ajax({
            url: '{{ route('customer.aging.chart.price') }}',
            method: 'GET',
            success: (res) => {
                const series = (Array.isArray(res) ? res : [0,0,0,0]).map(v => parseFloat(v) || 0);

                // Price Donut
                const priceOptions = {
                    series: series,
                    chart: { height: 300, type: 'donut', toolbar: { show: false } },
                    labels: ['1-90 Day', '91-180 Day', '181-365 Day', '> 365 Day'],
                    colors: chartColors,
                    legend: { position: 'bottom' },
                    dataLabels: { enabled: true, formatter: v => v.toFixed(1) + '%', style: { fontSize: '12px' } },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '62%',
                                labels: {
                                    show: true,
                                    name: { show: true, fontSize: '14px' },
                                    value: {
                                        show: true,
                                        fontSize: '14px',
                                        formatter: v => '$ ' + parseFloat(v).toLocaleString(undefined, {minimumFractionDigits:0, maximumFractionDigits:0})
                                    },
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        formatter: () => '$ ' + series.reduce((a,b) => a+b, 0).toLocaleString(undefined, {minimumFractionDigits:0, maximumFractionDigits:0})
                                    }
                                }
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: v => '$ ' + parseFloat(v).toLocaleString()
                        }
                    }
                };
                new ApexCharts(document.querySelector('#chart-aging-price'), priceOptions).render();
            }
        });

        // 3. Storage Location Table
        $.ajax({
            url: '{{ route('customer.aging.chart.table') }}',
            method: 'GET',
            success: (res) => {
                const data = res.data || [];
                let html = '';
                let totals = [0, 0, 0, 0];

                if (data.length === 0) {
                    html = '<tr><td colspan="5" class="text-center text-muted py-4">No inventory data found</td></tr>';
                } else {
                    data.forEach((item) => {
                        const vals = [
                            Number(item.aging1) || 0,
                            Number(item.aging2) || 0,
                            Number(item.aging3) || 0,
                            Number(item.aging4) || 0,
                        ];
                        vals.forEach((v, i) => totals[i] += v);

                        // Determine max val for visual indicator
                        const maxVal = Math.max(...vals, 1);

                        html += `<tr>
                            <td class="ps-3 fw-medium">${item.raw} - ${item.area} - ${item.rak} - ${item.bin}</td>
                            <td class="text-end">
                                ${formatUSD(vals[0])}
                                <div class="progress progress-sm mt-1" style="height:3px;">
                                    <div class="progress-bar bg-primary" style="width:${(vals[0]/maxVal*100).toFixed(0)}%"></div>
                                </div>
                            </td>
                            <td class="text-end">
                                ${formatUSD(vals[1])}
                                <div class="progress progress-sm mt-1" style="height:3px;">
                                    <div class="progress-bar" style="background:#f97316;width:${(vals[1]/maxVal*100).toFixed(0)}%"></div>
                                </div>
                            </td>
                            <td class="text-end">
                                ${formatUSD(vals[2])}
                                <div class="progress progress-sm mt-1" style="height:3px;">
                                    <div class="progress-bar bg-warning" style="width:${(vals[2]/maxVal*100).toFixed(0)}%"></div>
                                </div>
                            </td>
                            <td class="text-end">
                                ${formatUSD(vals[3])}
                                <div class="progress progress-sm mt-1" style="height:3px;">
                                    <div class="progress-bar bg-danger" style="width:${(vals[3]/maxVal*100).toFixed(0)}%"></div>
                                </div>
                            </td>
                        </tr>`;
                    });
                }

                document.getElementById('aging-table-body').innerHTML = html;
                document.getElementById('aging-total-1').innerText = formatUSD(totals[0]);
                document.getElementById('aging-total-2').innerText = formatUSD(totals[1]);
                document.getElementById('aging-total-3').innerText = formatUSD(totals[2]);
                document.getElementById('aging-total-4').innerText = formatUSD(totals[3]);
            },
            error: () => {
                document.getElementById('aging-table-body').innerHTML =
                    '<tr><td colspan="5" class="text-center text-muted py-4">Failed to load data</td></tr>';
            }
        });
    </script>
@endsection
