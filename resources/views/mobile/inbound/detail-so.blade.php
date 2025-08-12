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
                    <a href="{{ route('inbound.indexDetail.mobile', ['id' => $purchaseOrderId]) }}" class="ps-3">
                        <i class="mdi mdi-arrow-left-thin text-white" style="font-size: 32px"></i>
                    </a>
                </div>
                <div class="col-8 d-flex justify-content-center align-items-center">
                    <h5 class="mb-0 text-center text-white">Inbound Detail SO</h5>
                </div>
                <div class="col-2">

                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <h6 class="mb-2 fw-bold">List Item SO {{ request()->get('so') }}</h6>
                @foreach($purchaseOrderDetail as $detail)
                    <div class="card item-card mb-2">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="info-row"><b>{{ $detail->sales_doc }}</b></div>
                                @if($detail->po_item_qty == $detail->qty_qc)
                                    <span class="badge bg-success-subtle text-success">Complete</span>
                                @else
                                    <span class="badge bg-info-subtle text-info">Partial</span>
                                @endif
                            </div>
                            <div class="info-row"><b>Item </b>{{ $detail->item }}</div>
                            <div class="info-row"><b>{{ $detail->material }}</b></div>
                            <div class="info-row">{{ $detail->po_item_desc }}</div>
                            <div class="info-row">{{ $detail->prod_hierarchy_desc }}</div>
                            <div class="info-row"><b>QTY PO: </b>{{ number_format($detail->po_item_qty) }}</div>
                            <div class="info-row"><b>QTY QC: </b>{{ number_format($detail->qty_qc) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
