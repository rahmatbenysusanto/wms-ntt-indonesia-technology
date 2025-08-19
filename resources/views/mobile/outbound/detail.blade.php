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

        .inventory-card {
            font-size: 12px;
            line-height: 1.4;
            border-left: 4px solid #7F56D8;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .inventory-card .badge {
            font-size: 11px;
            padding: 4px 8px;
        }

        .info-row {
            margin-bottom: 2px;
        }

        @media (max-width: 576px) {
            .inventory-card .card-body {
                padding: 8px;
            }
        }

        .pagination-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #fff; /* putih biar jelas */
            border-top: 1px solid #ddd;
            padding: 6px 0;
            z-index: 1000;
        }

        .pagination .page-link {
            font-size: 12px;
            padding: 4px 8px;
        }

        body {
            padding-bottom: 50px; /* kasih ruang supaya konten tidak ketutup footer */
        }
    </style>
</head>
<body>

<div class="mobile-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2 d-flex align-items-center">
                <a href="{{ route('inbound.index.mobile') }}" class="ps-3">
                    <i class="mdi mdi-arrow-left-thin text-white" style="font-size: 32px"></i>
                </a>
            </div>
            <div class="col-8 d-flex justify-content-center align-items-center">
                <h5 class="mb-0 text-center text-white">Outbound Detail</h5>
            </div>
            <div class="col-2">

            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-12 mb-2">
            <div class="card po-card mb-1">
                <div class="card-body p-2">
                    <div class="info-row"><b>Deliv Note Number:</b> {{ $outbound->delivery_note_number }}</div>
                    <div class="info-row"><b>Customer:</b> {{ $outbound->customer->name }}</div>
                    <div class="info-row"><b>Date:</b> {{ \Carbon\Carbon::parse($outbound->delivery_date)->translatedFormat('d F Y H:i') }}</div>
                </div>
            </div>
        </div>

        <div class="col-12" style="margin-bottom: 80px">
            <h6 class="mb-2 fw-bold">List Item</h6>
            @foreach($outboundDetail as $detail)
                <a href="{{ route('outbound.indexDetailSN.mobile', ['id' => $detail->id, 'outbound' => $detail->outbound_id]) }}">
                    <div class="card inventory-card mb-2">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <div class="fw-bold">{{ $detail->inventoryPackageItem->purchaseOrderDetail->purchaseOrder->purc_doc }}</div>
                                    <small class="text-muted">SO#: {{ $detail->inventoryPackageItem->purchaseOrderDetail->sales_doc }}</small>
                                </div>
                                <span class="badge bg-primary">{{ number_format($detail->qty) }}</span>
                            </div>
                            <div class="info-row"><b>Nominal:</b> Rp {{ number_format($detail->qty * $detail->inventoryPackageItem->purchaseOrderDetail->net_order_price) }}</div>
                            <div class="info-row"><b>Material:</b> {{ $detail->inventoryPackageItem->purchaseOrderDetail->material }}</div>
                            <div class="info-row">{{ $detail->inventoryPackageItem->purchaseOrderDetail->po_item_desc }}</div>
                            <div class="info-row">{{ $detail->inventoryPackageItem->purchaseOrderDetail->prod_hierarchy_desc }}</div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>

@include('mobile.layout.menu')

</body>
</html>
