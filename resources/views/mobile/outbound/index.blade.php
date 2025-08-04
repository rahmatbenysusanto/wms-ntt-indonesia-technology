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
        body {
            background-color: #FFFFFF;
        }

        .mobile-header {
            padding-top: 12px;
            padding-bottom: 12px;
            background-color: #222163;
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
            <h5 class="mb-0 text-center text-white">Outbound</h5>
        </div>
        <div class="col-2">

        </div>
    </div>
</div>

</body>
</html>
