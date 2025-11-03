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
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 150px;               /* tinggi kartu konsisten */
        }

        .icon-slot {
            height: 90px;                /* slot gambar seragam */
            width: 100%;
            display: flex;
            align-items: center;         /* vertikal center */
            justify-content: center;     /* horizontal center */
        }

        .icon-slot img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;         /* jaga proporsi */
            display: block;
        }

        .menu-title {
            margin-top: auto;            /* nempel di bawah */
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
            margin-bottom: 10px;
        }

        .btn-info {
            background-color: #39BBBD!important;
            border: none!important;
        }
    </style>
</head>
<body>
    <img src="{{ asset('assets/mobile/img/menu.jpg') }}" class="header-image" alt="...">

    <div class="container-fluid mt-3">
        <div class="row">
            @if(Session::get('userHasMenu')->contains('Mobile Inbound'))
                <div class="col-6">
                    <a href="{{ route('inbound.index.mobile') }}">
                        <div class="card custom-card text-center">
                            <div class="icon-slot">
                                <img src="{{ asset('assets/mobile/img/inbound.png') }}" alt="Inbound">
                            </div>
                            <span class="menu-title">Inbound</span>
                        </div>
                    </a>
                </div>
            @endif

            @if(Session::get('userHasMenu')->contains('Mobile Outbound'))
                <div class="col-6">
                    <a href="{{ route('outbound.index.mobile') }}">
                        <div class="card custom-card text-center">
                            <div class="icon-slot">
                                <img src="{{ asset('assets/mobile/img/outbound.png') }}" alt="Inbound">
                            </div>
                            <span class="menu-title">Outbound</span>
                        </div>
                    </a>
                </div>
            @endif

            @if(Session::get('userHasMenu')->contains('Mobile Inventory'))
                <div class="col-6">
                    <a href="{{ route('inventory.index.mobile') }}">
                        <div class="card custom-card text-center">
                            <div class="icon-slot">
                                <img src="{{ asset('assets/mobile/img/inventory.png') }}" alt="Inbound">
                            </div>
                            <span class="menu-title">Inventory</span>
                        </div>
                    </a>
                </div>
            @endif

            @if(Session::get('userHasMenu')->contains('Mobile Aging'))
                <div class="col-6">
                    <a href="{{ route('inventory.aging.mobile') }}">
                        <div class="card custom-card text-center">
                            <div class="icon-slot">
                                <img src="{{ asset('assets/mobile/img/aging2.png') }}" alt="Inbound">
                            </div>
                            <span class="menu-title">Aging</span>
                        </div>
                    </a>
                </div>
            @endif

            @if(Session::get('userHasMenu')->contains('Mobile General Room'))
                <div class="col-6">
                    <a href="{{ route('gr.index.mobile') }}">
                        <div class="card custom-card text-center">
                            <div class="icon-slot">
                                <img src="{{ asset('assets/mobile/img/gr.png') }}" alt="Inbound">
                            </div>
                            <span class="menu-title">General Room</span>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <footer class="mobile-footer">
        <div class="container-fluid">
            <a href="{{ route('logout') }}" class="btn btn-info w-100">Logout</a>
        </div>
    </footer>

    @include('mobile.layout.js')
</body>
</html>
