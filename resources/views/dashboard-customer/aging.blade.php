@extends('layout.index')
@section('title', 'Dashboard Aging')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card p-2">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex gap-2">
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-dark">Main Dashboard</a>
                            <a href="{{ route('customer.inbound') }}" class="btn btn-dark">Inbound</a>
                            <a href="{{ route('customer.aging') }}" class="btn btn-info">Aging</a>
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
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title text-center mb-0"> ~ Inventory Aging (QTY) ~ </h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-pie-aging-qty" data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]' class="apex-charts" dir="ltr"></div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-3">
                                    <h5 class="text-center">1 - 90 Day</h5>
                                    <h5 class="text-center fw-bold" id="aging-qty-1">0</h5>
                                </div>
                                <div class="col-3">
                                    <h5 class="text-center">91 - 180 Day</h5>
                                    <h5 class="text-center fw-bold" id="aging-qty-2">0</h5>
                                </div>
                                <div class="col-3">
                                    <h5 class="text-center">181 - 365 Day</h5>
                                    <h5 class="text-center fw-bold" id="aging-qty-3">0</h5>
                                </div>
                                <div class="col-3">
                                    <h5 class="text-center"> > 365 Day</h5>
                                    <h5 class="text-center fw-bold" id="aging-qty-4">0</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title text-center mb-0"> ~ Inventory Aging (Price) ~ </h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-pie-aging-price" data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]' class="apex-charts" dir="ltr"></div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-3">
                                    <h5 class="text-center">1 - 90 Day</h5>
                                    <h5 class="text-center fw-bold" id="aging-price-1">0</h5>
                                </div>
                                <div class="col-3">
                                    <h5 class="text-center">91 - 180 Day</h5>
                                    <h5 class="text-center fw-bold" id="aging-price-2">0</h5>
                                </div>
                                <div class="col-3">
                                    <h5 class="text-center">181 - 365 Day</h5>
                                    <h5 class="text-center fw-bold" id="aging-price-3">0</h5>
                                </div>
                                <div class="col-3">
                                    <h5 class="text-center"> > 365 Day</h5>
                                    <h5 class="text-center fw-bold" id="aging-price-4">0</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-center mb-0"> ~ Inventory in all warehouse ~ </h4>
                </div>
                <div class="card-body">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function getChartColorsArray(id) {
            const el = document.getElementById(id);
            if (!el) return;

            let colors = el.getAttribute("data-colors");
            if (!colors) return;

            colors = JSON.parse(colors);

            return colors.map(function (value) {
                const clean = value.replace(" ", "");
                if (clean.indexOf(",") === -1) {
                    return getComputedStyle(document.documentElement).getPropertyValue(clean) || clean;
                }
                const parts = value.split(",");
                if (parts.length === 2) {
                    return `rgba(${getComputedStyle(document.documentElement).getPropertyValue(parts[0])},${parts[1]})`;
                }
                return clean;
            });
        }

        function formatUSD(value) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(value);
        }

        $.ajax({
            url: '{{ route('customer.aging.chart.qty') }}',
            method: 'GET',
            success: (res) => {
                const chartAgingQtyColors = getChartColorsArray("chart-pie-aging-qty");
                if (chartAgingQtyColors) {
                    const optionsAgingQty = {
                        series: res,
                        chart: { height: 300, type: "pie" },
                        labels: ["1 - 90 Day", "91 - 180 Day", "181 - 365 Day", "> 365 Day"],
                        legend: { position: "bottom" },
                        dataLabels: { dropShadow: { enabled: false } },
                        colors: chartAgingQtyColors
                    };

                    new ApexCharts(
                        document.querySelector("#chart-pie-aging-qty"),
                        optionsAgingQty
                    ).render();
                }

                document.getElementById('aging-qty-1').innerText = res[0];
                document.getElementById('aging-qty-2').innerText = res[1];
                document.getElementById('aging-qty-3').innerText = res[2];
                document.getElementById('aging-qty-4').innerText = res[3];
            }
        });

        $.ajax({
            url: '{{ route('customer.aging.chart.price') }}',
            method: 'GET',
            success: (res) => {
                const chartAgingPriceColors = getChartColorsArray("chart-pie-aging-price");
                if (chartAgingPriceColors) {
                    const optionsAgingPrice = {
                        series: res,
                        chart: { height: 300, type: "pie" },
                        labels: ["1 - 90 Day", "91 - 180 Day", "181 - 365 Day", "> 365 Day"],
                        legend: { position: "bottom" },
                        dataLabels: { dropShadow: { enabled: false } },
                        colors: chartAgingPriceColors
                    };

                    new ApexCharts(
                        document.querySelector("#chart-pie-aging-price"),
                        optionsAgingPrice
                    ).render();
                }

                document.getElementById('aging-price-1').innerText = formatUSD(res[0]);
                document.getElementById('aging-price-2').innerText = formatUSD(res[1]);
                document.getElementById('aging-price-3').innerText = formatUSD(res[2]);
                document.getElementById('aging-price-4').innerText = formatUSD(res[3]);
            }
        });
    </script>
@endsection

