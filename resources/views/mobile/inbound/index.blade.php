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
                        <div class="card p-2 mb-3">
                            <div class="row">
                                <div class="col-9">
                                    <div><b>Purc Doc: </b>{{ $item->purc_doc }}</div>
                                    <div><b>Customer: </b>{{ $item->customer->name }}</div>
                                    <div><b>SO#: </b>{{ $item->sales_doc_qty }}</div>
                                    <div><b>QTY Item: </b>{{ number_format($item->item_qty) }}</div>
                                    <div><b>Date: </b>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</div>
                                </div>
                                <div class="col-3 d-flex flex-column">
                                    @switch($item->status)
                                        @case('new')
                                            <span class="badge bg-success-subtle text-success">New</span>
                                            @break
                                        @case('open')
                                            <span class="badge bg-info-subtle text-info">Open</span>
                                            @break
                                        @case('process')
                                            <span class="badge bg-primary-subtle text-primary">In Process</span>
                                            @break
                                        @case('done')
                                            <span class="badge bg-secondary-subtle text-secondary">Done</span>
                                            @break
                                        @case('cancel')
                                            <span class="badge bg-danger-subtle text-danger">Cancel</span>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

</body>
</html>
