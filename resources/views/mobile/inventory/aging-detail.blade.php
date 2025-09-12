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
            <a href="{{ route('inventory.aging.mobile') }}" class="ps-3">
                <i class="mdi mdi-arrow-left-thin text-white" style="font-size: 32px;"></i>
            </a>
        </div>
        <div class="col-8 d-flex justify-content-center align-items-center">
            <h5 class="mb-0 text-center text-white">Aging Detail {{ $text }}</h5>
        </div>
        <div class="col-2 d-flex align-items-center">
            <a class="ps-1" onclick="openDownloadModal()">
                <i class="mdi mdi-file-download-outline text-white" style="font-size: 22px;"></i>
            </a>
        </div>
    </div>
</div>

<div class="container-fluid mt-3" style="margin-bottom: 80px">
    <div class="row">
        <div class="col-12">
            @foreach($inventoryDetail as $detail)
                <div class="card inventory-card mb-2">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                <div class="fw-bold">{{ $detail->purc_doc }}</div>
                                <small class="text-muted">SO#: {{ $detail->sales_doc }}</small>
                            </div>
                            <span class="badge bg-primary">{{ number_format($detail->qty) }}</span>
                        </div>
                        <div class="info-row"><b>Nominal:</b> $ {{ number_format($detail->total) }}</div>
                        <div class="info-row"><b>Material:</b> {{ $detail->material }}</div>
                        <div class="info-row">{{ $detail->po_item_desc }}</div>
                        <div class="info-row">{{ $detail->prod_hierarchy_desc }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div id="downloadReportModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Download Aging</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('inventory.aging.detail.pdf', ['type' => $type]) }}" class="btn btn-pdf btn-sm">Download PDF</a>
                    <a href="{{ route('inventory.aging.detail.excel', ['type' => $type]) }}" class="btn btn-success btn-sm">Download Excel</a>
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
