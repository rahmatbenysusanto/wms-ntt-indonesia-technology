@extends('layout.index')
@section('title', 'Dashboard Aging')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Dashboard Aging</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a>Dashboard Aging</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Data Products Aging</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Aging Date</th>
                                <th>Total Price</th>
                                <th class="text-center">Total QTY</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1 - 90 Day</td>
                                <td>$ {{ number_format($agingType1->total, 2) }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('dashboard.aging.detail', ['type' => 1]) }}" class="text-black">{{ number_format($agingType1->qty) }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td>91 - 180 Day</td>
                                <td>$ {{ number_format($agingType2->total, 2) }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('dashboard.aging.detail', ['type' => 2]) }}" class="text-black">{{ number_format($agingType2->qty) }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td>181 - 365 Day</td>
                                <td>$ {{ number_format($agingType3->total, 2) }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('dashboard.aging.detail', ['type' => 3]) }}" class="text-black">{{ number_format($agingType3->qty) }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td>> 365 Day</td>
                                <td>$ {{ number_format($agingType4->total, 2) }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('dashboard.aging.detail', ['type' => 4]) }}" class="text-black">{{ number_format($agingType4->qty) }}</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Aging By QTY</h4>
                </div>
                <div class="card-body">
                    <div id="simple_pie_chart" data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]' class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Aging By Total Price</h4>
                </div>
                <div class="card-body">
                    <div id="agingByTotalPrice" data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]' class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function getChartColorsArray(id) {
            const el = document.getElementById(id);
            if (!el) return null;

            let colors = el.getAttribute("data-colors");
            colors = JSON.parse(colors);

            return colors.map(function (value) {
                let t = value.replace(" ", "");
                if (t.indexOf(",") === -1) {
                    return getComputedStyle(document.documentElement).getPropertyValue(t) || t;
                } else {
                    let parts = value.split(",");
                    if (parts.length === 2) {
                        return "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(parts[0]) + "," + parts[1] + ")";
                    }
                    return t;
                }
            });
        }

        let chartPieBasicColors = getChartColorsArray("simple_pie_chart");

        if (chartPieBasicColors) {
            let options = {
                series: [
                    Number('{{ $agingType1->qty }}'),
                    Number('{{ $agingType2->qty }}'),
                    Number('{{ $agingType3->qty }}'),
                    Number('{{ $agingType4->qty }}')
                ],
                chart: {
                    height: 235,
                    type: "pie"
                },
                labels: ["1 - 90 Day", "91 - 180 Day", "181 - 360 Day", "> 360 Day"],
                legend: {
                    position: "bottom"
                },
                dataLabels: {
                    dropShadow: {
                        enabled: false
                    }
                },
                colors: chartPieBasicColors
            };

            let chart = new ApexCharts(document.querySelector("#simple_pie_chart"), options);
            chart.render();
        }

        let chartPieTotalPrice = getChartColorsArray("agingByTotalPrice");

        if (chartPieTotalPrice) {
            let options = {
                series: [
                    Number('{{ $agingType1->total }}'),
                    Number('{{ $agingType2->total }}'),
                    Number('{{ $agingType3->total }}'),
                    Number('{{ $agingType4->total }}')
                ],
                chart: {
                    height: 235,
                    type: "pie"
                },
                labels: ["1 - 90 Day", "91 - 180 Day", "181 - 360 Day", "> 360 Day"],
                legend: {
                    position: "bottom"
                },
                dataLabels: {
                    dropShadow: {
                        enabled: false
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return value.toLocaleString();
                        }
                    }
                },
                colors: chartPieBasicColors
            };

            let chart = new ApexCharts(document.querySelector("#agingByTotalPrice"), options);
            chart.render();
        }
    </script>

    <script>
        function getChartAgingPrice(id) {
            const el = document.getElementById(id);
            if (!el) return null;

            let colors = el.getAttribute("data-colors");
            colors = JSON.parse(colors);

            return colors.map(function (value) {
                let t = value.replace(" ", "");
                if (t.indexOf(",") === -1) {
                    return getComputedStyle(document.documentElement).getPropertyValue(t) || t;
                } else {
                    let parts = value.split(",");
                    if (parts.length === 2) {
                        return "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(parts[0]) + "," + parts[1] + ")";
                    }
                    return t;
                }
            });
        }


    </script>
@endsection
