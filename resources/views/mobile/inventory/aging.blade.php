<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inbound</title>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .mobile-header {
            padding-top: 8px;
            padding-bottom: 8px;
            background-color: #39BBBD;
        }

        .card-partial {
            border-left: 4px solid #4b38b3;
            font-size: 12px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        .card-partial .badge {
            font-size: 10px;
            padding: 4px 6px;
            border-radius: 6px;
        }

        .card-partial .info-row {
            margin-bottom: 2px;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        @media (max-width: 576px) {
            .card-partial {
                margin-bottom: 8px;
            }
        }

    </style>
</head>
<body>

    <div class="mobile-header">
        <div class="row">
            <div class="col-2 d-flex align-items-center">
                <a href="{{ route('dashboardMobile') }}" class="ps-3">
                    <i class="mdi mdi-arrow-left-thin text-white" style="font-size: 32px"></i>
                </a>
            </div>
            <div class="col-8 d-flex justify-content-center align-items-center">
                <h5 class="mb-0 text-center text-white">Aging</h5>
            </div>
            <div class="col-2">

            </div>
        </div>
    </div>

    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
                <div class="card p-1">
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
                                <a href="{{ route('dashboard.mobile.aging.detail', ['type' => 1]) }}" class="text-black">{{ number_format($agingType1->qty) }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td>91 - 180 Day</td>
                            <td>$ {{ number_format($agingType2->total, 2) }}</td>
                            <td class="text-center fw-bold">
                                <a href="{{ route('dashboard.mobile.aging.detail', ['type' => 2]) }}" class="text-black">{{ number_format($agingType2->qty) }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td>181 - 365 Day</td>
                            <td>$ {{ number_format($agingType3->total, 2) }}</td>
                            <td class="text-center fw-bold">
                                <a href="{{ route('dashboard.mobile.aging.detail', ['type' => 3]) }}" class="text-black">{{ number_format($agingType3->qty) }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td>> 365 Day</td>
                            <td>$ {{ number_format($agingType4->total, 2) }}</td>
                            <td class="text-center fw-bold">
                                <a href="{{ route('dashboard.mobile.aging.detail', ['type' => 4]) }}" class="text-black">{{ number_format($agingType4->qty) }}</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12">
                <div class="card p-2">
                    <h6 class="mb-0">Aging By QTY</h6>
                    <div id="simple_pie_chart" data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]' class="apex-charts" dir="ltr"></div>
                </div>
            </div>
            <div class="col-12" style="margin-bottom: 80px">
                <div class="card p-2">
                    <h6 class="mb-0">Aging By Total Price</h6>
                    <div id="agingByTotalPrice" data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]' class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div>
    </div>

    @include('mobile.layout.menu')
    @include('mobile.layout.js')
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
</body>
</html>
