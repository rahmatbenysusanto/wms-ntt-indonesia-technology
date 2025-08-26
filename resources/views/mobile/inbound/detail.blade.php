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
            <div class="col-2 d-flex align-items-start">
                <a href="{{ route('inbound.index.mobile') }}" class="ps-3">
                    <i class="mdi mdi-arrow-left-thin text-white" style="font-size: 32px"></i>
                </a>
            </div>
            <div class="col-8 d-flex justify-content-center align-items-center">
                <h5 class="mb-0 text-center text-white">Inbound Detail</h5>
            </div>
            <div class="col-2 d-flex align-items-center">
                <a class="ps-3" onclick="openDownloadModal()">
                    <i class="mdi mdi-file-download-outline text-white" style="font-size: 22px"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-12">
            <div class="card po-card mb-1">
                <div class="card-body p-2">
                    <div class="info-row"><b>Purc Doc:</b> {{ $purchaseOrder->purc_doc }}</div>
                    <div class="info-row"><b>Customer:</b> {{ $purchaseOrder->customer->name }}</div>
                    <div class="info-row"><b>Date:</b> {{ \Carbon\Carbon::parse($purchaseOrder->created_at)->translatedFormat('d F Y H:i') }}</div>
                </div>
            </div>
        </div>

        <div class="col-12" style="margin-bottom: 80px">
            <h6 class="mb-2 fw-bold">List SO</h6>
            @foreach($purchaseOrderDetail as $detail)
                <a href="{{ route('inbound.indexDetail.so', ['so' => $detail->sales_doc, 'po' => $purchaseOrder->id]) }}">
                    <div class="card item-card mb-2 {{ $detail->qty == $detail->qtyQc ? 'card-complete' : 'card-partial' }}">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="info-row"><b>{{ $detail->sales_doc }}</b></div>
                                @if($detail->qty == $detail->qtyQc)
                                    <span class="badge bg-success-subtle text-success">Complete</span>
                                @else
                                    <span class="badge bg-info-subtle text-info">Partial</span>
                                @endif
                            </div>
                            <div class="info-row"><b>QTY:</b> {{ number_format($detail->qty) }}</div>
                            <div class="info-row"><b>Product:</b> {{ number_format($detail->qtyProduct) }}</div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>

<div id="downloadReportModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Download Inbound</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('inbound.purchase-order-download-pdf', ['id' => $purchaseOrder->id]) }}" class="btn btn-pdf btn-sm">Download PDF</a>
                    <a href="{{ route('inbound.purchase-order-download-excel', ['id' => $purchaseOrder->id]) }}" class="btn btn-success btn-sm">Download Excel</a>
                </div>
            </div>
        </div>
    </div>
</div>

@include('mobile.layout.menu')

<script>
    function openDownloadModal() {
        $('#downloadReportModal').modal('show');
    }
</script>

</body>
</html>
