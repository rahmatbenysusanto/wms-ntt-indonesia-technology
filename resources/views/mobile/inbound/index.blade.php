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
                <h5 class="mb-0 text-center text-white">Inbound</h5>
            </div>
            <div class="col-2">

            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                @foreach($purchaseOrder as $index => $item)
                    <a href="{{ route('inbound.indexDetail.mobile', ['id' => $item->id]) }}">
                        <div class="card card-partial mb-2">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="fw-bold">{{ $item->purc_doc }}</div>
                                    @switch($item->status)
                                        @case('new')
                                            <span class="badge bg-success-subtle text-success">New</span>
                                            @break
                                        @case('open')
                                            <span class="badge bg-info-subtle text-info">Open</span>
                                            @break
                                        @case('process')
                                            <span class="badge bg-primary-subtle text-primary">Partial</span>
                                            @break
                                        @case('done')
                                            <span class="badge bg-secondary-subtle text-secondary">Done</span>
                                            @break
                                        @case('cancel')
                                            <span class="badge bg-danger-subtle text-danger">Cancel</span>
                                            @break
                                    @endswitch
                                </div>
                                <small class="text-muted d-block mb-1">{{ $item->customer->name }}</small>
                                <div class="info-row"><b>SO#:</b> {{ $item->sales_doc_qty }}</div>
                                <div class="info-row"><b>QTY Item:</b> {{ number_format($item->item_qty) }}</div>
                                <div class="info-row"><b>Date:</b> {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

</body>
</html>
