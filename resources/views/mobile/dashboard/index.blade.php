<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Mobile</title>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body {
            background-color: #FFFFFF;
        }

        .header-image {
            width: 100%;
            height: auto;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        .custom-card {
            background: linear-gradient(135deg, #f8f9fa, #eaeaea);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 150px;
        }

        .custom-card img {
            max-width: 100px;
            max-height: 100px;
            margin-bottom: 10px;
        }

        .menu-title {
            font-weight: bold;
            font-size: 14px;
            color: #0a0a0a;
        }

        .mobile-footer {
            color: black;
            text-align: center;
            padding: 5px 0;
            font-size: 13px;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-weight: normal;
        }

    </style>
</head>
<body>
    <img src="{{ asset('assets/mobile/img/menu.jpg') }}" class="header-image" alt="...">

    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-6">
                <a href="{{ route('inbound.index.mobile') }}">
                    <div class="card custom-card text-center">
                        <img src="{{ asset('assets/mobile/img/inbound.png') }}" alt="Inbound">
                        <span class="menu-title">Inbound</span>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('outbound.index.mobile') }}">
                    <div class="card custom-card text-center">
                        <img src="{{ asset('assets/mobile/img/outbound.png') }}" alt="Inbound">
                        <span class="menu-title">Outbound</span>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('inventory.index.mobile') }}">
                    <div class="card custom-card text-center">
                        <img src="{{ asset('assets/mobile/img/inventory.png') }}" alt="Inbound">
                        <span class="menu-title">Inventory</span>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('inventory.aging.mobile') }}">
                    <div class="card custom-card text-center">
                        <img src="{{ asset('assets/mobile/img/aging.png') }}" alt="Inbound">
                        <span class="menu-title">Aging</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <footer class="mobile-footer">
        <p>Trans Kargo Solusindo - <span>{{ date('Y') }}</span></p>
    </footer>

    @include('mobile.layout.js')
</body>
</html>
