@extends('layout.index')
@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card p-2">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex gap-2">
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-info">Main Dashboard</a>
                            <a href="{{ route('customer.inbound') }}" class="btn btn-dark">Inbound</a>
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

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Parent</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-primary" id="totalPO">

                                    </h4>
                                    @if(in_array(Auth::user()->role, ['admin', 'warehouse', 'mobile']))
                                        <a href="{{ route('inventory.index') }}" class="text-decoration-underline text-primary fw-medium">View Detail</a>
                                    @endif
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded fs-3" style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: white;">
                                        <i class="bx bx-news"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Sales Doc (SO)</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-success" id="totalSO">

                                    </h4>
                                    @if(in_array(Auth::user()->role, ['admin', 'warehouse', 'mobile']))
                                        <a href="{{ route('inbound.purchase-order') }}" class="text-decoration-underline text-success fw-medium">View Detail</a>
                                    @endif
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded fs-3" style="background: linear-gradient(135deg, #43e97b, #38f9d7); color: white;">
                                        <i class="bx bx-list-ul"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Stock Item</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-warning" id="totalStock">

                                    </h4>
                                    @if(in_array(Auth::user()->role, ['admin', 'warehouse', 'mobile']))
                                        <a href="{{ route('inventory.index') }}" class="text-decoration-underline text-warning fw-medium">View Detail</a>
                                    @endif
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded fs-3" style="background: linear-gradient(135deg, #fddb92, #d1fdff); color: #795548;">
                                        <i class="bx bx-package"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Value</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-danger" id="totalPrice">

                                    </h4>
                                    @if(in_array(Auth::user()->role, ['admin', 'warehouse', 'mobile']))
                                        <a href="{{ route('inventory.index') }}" class="text-decoration-underline text-danger fw-medium">View Detail</a>
                                    @endif
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded fs-3" style="background: linear-gradient(135deg, #f5576c, #f093fb); color: white;">
                                        <i class="bx bx-dollar-circle"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title text-center mb-0"> ~ Monthly Stock Flow – Inbound vs Outbound ~ </h4>
                        </div>
                        <div class="card-body">
                            <div id="chartStockFlow" data-colors='["--vz-primary", "--vz-success"]' class="apex-charts" dir="ltr"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        loadDataCard();

        function loadDataCard() {
            $.ajax({
                url: '{{ route('customer.card.json') }}',
                method: 'GET',
                data: {
                    customer: document.getElementById('customer').value
                },
                success: (res) => {
                    const data = res.data;

                    document.getElementById('totalPO').innerHTML = `<span class="counter-value" data-target="${data.totalParent}">${data.totalParent}</span>`;
                    document.getElementById('totalSO').innerHTML = `<span class="counter-value" data-target="${new Intl.NumberFormat('en-US').format(data.totalSO)}">${new Intl.NumberFormat('en-US').format(data.totalSO)}</span>`;
                    document.getElementById('totalStock').innerHTML = `<span class="counter-value" data-target="${new Intl.NumberFormat('en-US').format(data.totalStock)}">${new Intl.NumberFormat('en-US').format(data.totalStock)}</span>`;
                    document.getElementById('totalPrice').innerHTML = `$<span class="counter-value" data-target="${new Intl.NumberFormat('en-US').format(data.totalPrice)}">${new Intl.NumberFormat('en-US').format(data.totalPrice)}</span>`;
                }
            });
        }

        function changeCustomer() {
            if (document.getElementById('customer').value === '' || document.getElementById('customer').value === null) {
                localStorage.setItem('customer', '');
            } else {
                localStorage.setItem('customer', document.getElementById('customer').value);
            }

            loadDataCard();
        }

        $.ajax({
            url: '{{ route('customer.monthly.stock') }}',
            method: 'GET',
            success: (res) => {
                const labels = res.data.map(i => i.date_key + "-01");
                // contoh: 2025-01-01 → VALID datetime

                const inbound = res.data.map(i => i.inbound);
                const outbound = res.data.map(i => i.outbound);

                function getChartColorsArray(id) {
                    const el = document.getElementById(id);
                    if (!el) return null;

                    let colors = el.getAttribute("data-colors");
                    colors = JSON.parse(colors);

                    return colors.map((value) => {
                        const clean = value.replace(" ", "");
                        if (!clean.includes(",")) {
                            return getComputedStyle(document.documentElement)
                                .getPropertyValue(clean) || clean;
                        }
                        const parts = clean.split(",");
                        if (parts.length === 2) {
                            const color = getComputedStyle(document.documentElement)
                                .getPropertyValue(parts[0]);
                            return `rgba(${color},${parts[1]})`;
                        }
                        return clean;
                    });
                }

                const chartColors = getChartColorsArray("chartStockFlow");

                const options = {
                    series: [
                        {name: "Inbound", type: "column", data: inbound},
                        {name: "Outbound", type: "line", data: outbound}
                    ],
                    chart: {
                        height: 500,
                        type: "line",
                        toolbar: {show: false}
                    },
                    stroke: {width: [0, 4]},
                    plotOptions: {
                        bar: {
                            columnWidth: '35%',
                            borderRadius: 4
                        }
                    },
                    title: {
                        text: res.data[res.data.length - 1].label, // contoh: "Nov 25"
                        style: {fontWeight: 600, fontSize: "16px"}
                    },
                    dataLabels: {
                        enabled: true,
                        enabledOnSeries: [1]
                    },
                    labels: labels,
                    xaxis: {type: "datetime"},
                    yaxis: [
                        {title: {text: "Inbound"}},
                        {opposite: true, title: {text: "Outbound"}}
                    ],
                    colors: chartColors
                };

                new ApexCharts(
                    document.querySelector("#chartStockFlow"),
                    options
                ).render();
            }
        });

    </script>
@endsection
