@extends('layout.index')
@section('title', 'Dashboard')

@section('content')
    {{-- Modern Nav Pills --}}
    @include('dashboard-customer.partials.nav-pills', ['navActive' => 'dashboard'])

    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #4facfe !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-13 mb-1 text-truncate">Purchase Orders</p>
                            <h3 class="mb-0 fw-bold text-primary" id="totalPO">0</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(79,172,254,0.12); color: #4facfe;">
                                <i class="bx bx-news"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('inbound.purchase-order') }}" class="text-primary text-decoration-none fs-12">View Detail <i class="ri-arrow-right-s-line"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #43e97b !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-13 mb-1 text-truncate">Total Parent</p>
                            <h3 class="mb-0 fw-bold text-success" id="totalSO">0</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(67,233,123,0.12); color: #43e97b;">
                                <i class="bx bx-list-ul"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('inventory.index') }}" class="text-success text-decoration-none fs-12">View Detail <i class="ri-arrow-right-s-line"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #fddb92 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-13 mb-1 text-truncate">Stock Items</p>
                            <h3 class="mb-0 fw-bold text-warning" id="totalStock">0</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(253,219,146,0.2); color: #d4a017;">
                                <i class="bx bx-package"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('inventory.index') }}" class="text-warning text-decoration-none fs-12">View Detail <i class="ri-arrow-right-s-line"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #f5576c !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-13 mb-1 text-truncate">Total Value</p>
                            <h3 class="mb-0 fw-bold text-danger" id="totalPrice">$0</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(245,87,108,0.12); color: #f5576c;">
                                <i class="bx bx-dollar-circle"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('inventory.index') }}" class="text-danger text-decoration-none fs-12">View Detail <i class="ri-arrow-right-s-line"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #a855f7 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-13 mb-1 text-truncate">Customers</p>
                            <h3 class="mb-0 fw-bold text-primary" style="color:#a855f7!important;" id="totalCustomers">0</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(168,85,247,0.12); color: #a855f7;">
                                <i class="bx bx-group"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('customer') }}" class="text-decoration-none fs-12" style="color:#a855f7;">Manage <i class="ri-arrow-right-s-line"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-animate border-0 shadow-sm" style="border-left: 4px solid #06b6d4 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted fs-13 mb-1 text-truncate">Storage Bins</p>
                            <h3 class="mb-0 fw-bold" style="color:#06b6d4!important;" id="totalStorage">0</h3>
                        </div>
                        <div class="avatar-xs">
                            <span class="avatar-title rounded-circle fs-5" style="background: rgba(6,182,212,0.12); color: #06b6d4;">
                                <i class="bx bx-layer"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('storage') }}" class="text-decoration-none fs-12" style="color:#06b6d4;">View <i class="ri-arrow-right-s-line"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1: Monthly Stock Flow --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-line-chart fs-18 text-primary"></i>
                        </div>
                        <h5 class="card-title mb-0">Monthly Stock Flow — Inbound vs Outbound</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartStockFlow" data-colors='["#4facfe", "#43e97b"]' class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2: Top Customers + Aging Summary --}}
    <div class="row">
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-bar-chart-alt-2 fs-18 text-success"></i>
                        </div>
                        <h5 class="card-title mb-0">Top Customers by Inventory Value</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartTopCustomers" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <i class="bx bx-doughnut-chart fs-18 text-warning"></i>
                        </div>
                        <h5 class="card-title mb-0">Aging Summary</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartAgingSummary" class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Outbound Activity --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                <i class="bx bx-log-out-circle fs-18 text-info"></i>
                            </div>
                            <h5 class="card-title mb-0">Recent Outbound Activity</h5>
                        </div>
                        <a href="{{ route('dashboard.outbound') }}" class="btn btn-soft-info btn-sm">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="recentOutboundTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th>Delivery Note</th>
                                    <th>Customer</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-center">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading recent activity...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            loadCardData();
            loadStockFlowChart();
            loadTopCustomers();
            loadAgingSummary();
            loadRecentOutbound();
        });

        function changeCustomer(val) {
            localStorage.setItem('customer', val || '');
            loadCardData();
            loadStockFlowChart();
            loadTopCustomers();
            loadAgingSummary();
        }

        // ============ STAT CARDS ============
        function loadCardData() {
            $.ajax({
                url: '{{ route('customer.card.json') }}',
                method: 'GET',
                data: { customer: document.getElementById('customer').value },
                success: (res) => {
                    const d = res.data;
                    animateNumber('totalPO', d.totalPO);
                    animateNumber('totalSO', new Intl.NumberFormat('en-US').format(d.totalParent));
                    animateNumber('totalStock', new Intl.NumberFormat('en-US').format(d.totalStock));
                    animatePrice('totalPrice', d.totalPrice);
                    animateNumber('totalCustomers', d.totalCustomers || 0);
                    animateNumber('totalStorage', d.totalStorageBins || 0);
                }
            });
        }

        function animateNumber(el, val) {
            const elm = document.getElementById(el);
            if (!elm) return;
            elm.innerHTML = val;
        }

        function animatePrice(el, val) {
            const elm = document.getElementById(el);
            if (!elm) return;
            elm.innerHTML = '$ ' + new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(val);
        }

        // ============ MONTHLY STOCK FLOW CHART ============
        function loadStockFlowChart() {
            $.ajax({
                url: '{{ route('customer.monthly.stock') }}',
                method: 'GET',
                success: (res) => {
                    const labels = res.data.map(i => i.date_key + '-01');
                    const inbound = res.data.map(i => i.inbound);
                    const outbound = res.data.map(i => i.outbound);

                    const options = {
                        series: [
                            { name: 'Inbound', type: 'column', data: inbound },
                            { name: 'Outbound', type: 'line', data: outbound }
                        ],
                        chart: {
                            height: 380,
                            type: 'line',
                            toolbar: { show: false },
                            zoom: { enabled: false }
                        },
                        stroke: { width: [0, 3], curve: 'smooth' },
                        plotOptions: {
                            bar: { columnWidth: '40%', borderRadius: 4, borderRadiusApplication: 'around' }
                        },
                        dataLabels: {
                            enabled: true,
                            enabledOnSeries: [1],
                            style: { fontSize: '11px' }
                        },
                        labels: labels,
                        xaxis: {
                            type: 'datetime',
                            labels: { format: 'MMM yy' }
                        },
                        yaxis: [
                            { title: { text: 'Inbound' }, labels: { formatter: v => v.toLocaleString() } },
                            { opposite: true, title: { text: 'Outbound' }, labels: { formatter: v => v.toLocaleString() } }
                        ],
                        colors: ['#4facfe', '#43e97b'],
                        legend: { position: 'top', horizontalAlign: 'right' },
                        grid: { borderColor: '#f1f5f9' },
                        tooltip: {
                            shared: true,
                            y: { formatter: v => v.toLocaleString() }
                        }
                    };

                    const el = document.querySelector('#chartStockFlow');
                    if (el) {
                        el.innerHTML = '';
                        new ApexCharts(el, options).render();
                    }
                }
            });
        }

        // ============ TOP CUSTOMERS CHART ============
        function loadTopCustomers() {
            $.ajax({
                url: '{{ route('customer.top.customers') }}',
                method: 'GET',
                success: (res) => {
                    const data = res.data || [];
                    const names = data.map(i => i.name || 'Unknown');
                    const values = data.map(i => parseFloat(i.total_value) || 0);

                    const colors = ['#4facfe', '#43e97b', '#fddb92', '#f5576c', '#a855f7',
                                   '#06b6d4', '#f97316', '#84cc16', '#ec4899', '#14b8a6'];

                    const options = {
                        series: [{ name: 'Inventory Value ($)', data: values }],
                        chart: {
                            height: 350,
                            type: 'bar',
                            toolbar: { show: false },
                            zoom: { enabled: false }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                borderRadiusApplication: 'end',
                                horizontal: true,
                                barHeight: '70%',
                                distributed: true
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: v => '$ ' + (v / 1000).toFixed(0) + 'k',
                            style: { fontSize: '11px', colors: ['#333'] }
                        },
                        colors: colors,
                        xaxis: {
                            categories: names,
                            labels: { formatter: v => '$ ' + (v / 1000).toFixed(0) + 'k' }
                        },
                        yaxis: {
                            labels: { style: { fontSize: '11px' } }
                        },
                        legend: { show: false },
                        grid: { borderColor: '#f1f5f9' },
                        tooltip: {
                            y: { formatter: v => '$ ' + v.toLocaleString() }
                        }
                    };

                    const el = document.querySelector('#chartTopCustomers');
                    if (el) {
                        el.innerHTML = '';
                        new ApexCharts(el, options).render();
                    }
                }
            });
        }

        // ============ AGING SUMMARY DONUT ============
        function loadAgingSummary() {
            $.ajax({
                url: '{{ route('customer.aging.chart.qty') }}',
                method: 'GET',
                success: (res) => {
                    const series = Array.isArray(res) ? res : [0, 0, 0, 0];
                    const options = {
                        series: series.map(v => parseInt(v) || 0),
                        chart: {
                            height: 320,
                            type: 'donut',
                            toolbar: { show: false }
                        },
                        labels: ['1-90 Day', '91-180 Day', '181-365 Day', '> 365 Day'],
                        colors: ['#4facfe', '#fddb92', '#f97316', '#f5576c'],
                        legend: { position: 'bottom', fontSize: '12px' },
                        dataLabels: {
                            enabled: true,
                            formatter: function (val) { return val.toFixed(1) + '%'; },
                            style: { fontSize: '12px' }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '65%',
                                    labels: {
                                        show: true,
                                        name: { show: true, fontSize: '14px' },
                                        value: {
                                            show: true,
                                            fontSize: '16px',
                                            formatter: v => parseInt(v).toLocaleString()
                                        },
                                        total: {
                                            show: true,
                                            label: 'Total Items',
                                            formatter: () => series.reduce((a, b) => a + b, 0).toLocaleString()
                                        }
                                    }
                                }
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: v => v.toLocaleString() + ' items'
                            }
                        },
                        responsive: [{
                            breakpoint: 480,
                            options: { chart: { height: 280 }, legend: { position: 'bottom' } }
                        }]
                    };

                    const el = document.querySelector('#chartAgingSummary');
                    if (el) {
                        el.innerHTML = '';
                        new ApexCharts(el, options).render();
                    }
                }
            });
        }

        // ============ RECENT OUTBOUND TABLE ============
        function loadRecentOutbound() {
            $.ajax({
                url: '{{ route('customer.recent.outbound') }}',
                method: 'GET',
                success: (res) => {
                    const data = res.data || [];
                    const tbody = document.querySelector('#recentOutboundTable tbody');
                    if (!tbody) return;

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No recent outbound activity</td></tr>';
                        return;
                    }

                    let html = '';
                    data.forEach(item => {
                        const date = item.delivery_date
                            ? new Date(item.delivery_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
                            : '-';
                        html += `<tr>
                            <td class="ps-3 fw-medium">${item.purc_doc || '-'}</td>
                            <td>${item.sales_doc || '-'}</td>
                            <td><span class="badge bg-soft-info text-dark">${item.delivery_note_number || '-'}</span></td>
                            <td>${item.customer?.name || '-'}</td>
                            <td class="text-center fw-semibold">${(item.qty || 0).toLocaleString()}</td>
                            <td class="text-center text-muted fs-13">${date}</td>
                        </tr>`;
                    });
                    tbody.innerHTML = html;
                },
                error: () => {
                    const tbody = document.querySelector('#recentOutboundTable tbody');
                    if (tbody) {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Failed to load data</td></tr>';
                    }
                }
            });
        }

        // Restore customer from localStorage
        (function() {
            const saved = localStorage.getItem('customer');
            if (saved) {
                const sel = document.getElementById('customer');
                if (sel) sel.value = saved;
            }
        })();
    </script>
@endsection
