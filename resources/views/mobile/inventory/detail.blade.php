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

        .po-card {
            font-size: 12px;
            line-height: 1.4;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .info-row {
            margin-bottom: 2px;
        }

        .stat-card {
            background-color: #FFFFFF;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .stat-card .title {
            font-size: 12px;
            margin-bottom: 4px;
            font-weight: 500;
            color: #555;
        }

        .stat-card .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #222;
        }

        .stat-card .stat-sub {
            font-size: 12px;
            color: #666;
        }

        @media (max-width: 576px) {
            .po-card .card-body {
                padding: 8px;
            }
        }

    </style>
</head>
<body>

    <div class="mobile-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-2 d-flex align-items-center">
                    <a href="{{ route('inventory.index.mobile') }}" class="ps-3">
                        <i class="mdi mdi-arrow-left-thin text-white" style="font-size: 32px"></i>
                    </a>
                </div>
                <div class="col-8 d-flex justify-content-center align-items-center">
                    <h5 class="mb-0 text-center text-white">Inventory Detail</h5>
                </div>
                <div class="col-2 d-flex align-items-center">
                    <a class="ps-2" onclick="openDownloadModal()">
                        <i class="mdi mdi-file-download-outline text-white" style="font-size: 22px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-3">
        <div class="row g-2">
            <!-- PO Info -->
            <div class="col-12">
                <div class="card po-card">
                    <div class="card-body p-2">
                        <div class="info-row"><b>Purc Doc:</b> {{ request()->get('po') }}</div>
                        <div class="info-row"><b>Sales Doc:</b> {{ request()->get('so') }}</div>
                        <div class="info-row"><b>{{ $product->material }}</b></div>
                        <div class="info-row text-muted">{{ $product->po_item_desc }}</div>
                        <div class="info-row text-muted">{{ $product->prod_hierarchy_desc }}</div>
                    </div>
                </div>
            </div>

            <!-- Inventory & Outbound -->
            <div class="col-6">
                <div class="card stat-card text-center">
                    <p class="title">Inventory</p>
                    <div class="stat-value">{{ number_format($inventoryPackageItem) }}</div>
                    <div class="stat-sub">$ {{ number_format($inventoryNominal) }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="card stat-card text-center">
                    <p class="title">Outbound</p>
                    <div class="stat-value">{{ number_format($outboundDetail) }}</div>
                    <div class="stat-sub">$ {{ number_format($outboundNominal) }}</div>
                </div>
            </div>

            <div class="col-6">
                <div class="card p-1">
                    <span class="fw-bold">Serial Number Stock</span>
                    <table class="table table-striped">
                        @foreach($serialNumberStock as $sn)
                            <tr>
                                <td>{{ $sn->serial_number }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="col-6">
                <div class="card p-1">
                    <span class="fw-bold">Serial Number Outbound</span>
                    <table class="table table-striped">
                        @foreach($serialNumberOutbound as $sn)
                            <tr>
                                <td>{{ $sn->serial_number }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="downloadReportModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Download Inventory Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('inbound.purchase-order-download-pdf', ['id' => '']) }}" class="btn btn-pdf btn-sm">Download PDF</a>
                        <a href="{{ route('inbound.purchase-order-download-excel', ['id' => '']) }}" class="btn btn-success btn-sm">Download Excel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('mobile.layout.js')
    <script>
        function openDownloadModal() {
            $('#downloadReportModal').modal('show');
        }
    </script>

</body>
</html>
