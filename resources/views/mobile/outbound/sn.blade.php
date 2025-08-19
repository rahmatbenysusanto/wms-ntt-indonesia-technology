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
            padding-top: 6px;
            padding-bottom: 6px;
            background-color: #39BBBD;
        }

        .po-card,
        .item-card {
            font-size: 12px;
            line-height: 1.4;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .info-row {
            margin-bottom: 2px;
        }

        h6 {
            font-size: 14px;
            margin-top: 10px;
        }

        b {
            font-weight: 600;
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 6px 8px;
            }
        }

        .card-complete {
            border-left: 4px solid ;
        }

        .card-partial {
            border-left: 4px solid ;
        }
    </style>
</head>
<body>

<div class="mobile-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2 d-flex align-items-center">
                <a href="{{ route('outbound.indexDetail.mobile', ['id' => $outboundId]) }}" class="ps-3">
                    <i class="mdi mdi-arrow-left-thin text-white" style="font-size: 32px"></i>
                </a>
            </div>
            <div class="col-8 d-flex justify-content-center align-items-center">
                <h5 class="mb-0 text-center text-white">Outbound Detail SN</h5>
            </div>
            <div class="col-2">

            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt-2" style="margin-bottom: 80px">
    <div class="row">
        <div class="col-12">
            <h6 class="mb-2 fw-bold">List Item Serial Number</h6>
            @foreach($serialNumber as $detail)
                <div class="card item-card mb-2">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="info-row"><b>{{ $detail->serial_number }}</b></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@include('mobile.layout.menu')
</body>
</html>
