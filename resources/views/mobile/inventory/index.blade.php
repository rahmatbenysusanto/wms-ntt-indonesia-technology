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

        .text-sm {
            font-size: 10px;
        }

        .btn-info {
            background-color: #39BBBD!important;
            border-color: #39BBBD!important;
        }
    </style>
</head>
<body>

    <div class="mobile-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-2 d-flex align-items-center">
                    <a href="{{ route('dashboardMobile') }}" class="ps-3">
                        <i class="mdi mdi-arrow-left-thin text-white" style="font-size: 32px"></i>
                    </a>
                </div>
                <div class="col-8 d-flex justify-content-center align-items-center">
                    <h5 class="mb-0 text-center text-white">Inventory</h5>
                </div>
                <div class="col-2 d-flex align-items-center">
                    <a class="ps-3" onclick="openDownloadModal()">
                        <i class="mdi mdi-file-download-outline text-white" style="font-size: 22px"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-3" style="margin-bottom: 80px">
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
            <a class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">Search Filter</a>
            @if(request()->get('search') == 1)
                <a href="{{ url()->current() }}" class="btn btn-danger btn-sm">Clear Filter</a>
            @endif
        </div>

        <div class="row">
            <div class="col-12">
                @foreach($inventory as $index => $inv)
                    <a href="{{ route('inventory.indexDetail.mobile', ['po' => $inv->purc_doc, 'so' => $inv->sales_doc, 'id' => $inv->product_id]) }}">
                        <div class="card inventory-card mb-2">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div>
                                        <div class="fw-bold">{{ $inv->purc_doc }}</div>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <small class="text-muted">SO#: {{ $inv->sales_doc }}</small>
                                            @if($inv->is_parent == 1)
                                                <span class="badge bg-info ms-2" style="font-size: 8px">Parent</span>
                                            @else
                                                <span class="badge bg-secondary ms-2" style="font-size: 8px">Child</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="badge bg-primary">{{ number_format($inv->qty) }}</span>
                                </div>
                                <div class="info-row"><b>Nominal:</b> $ {{ number_format($inv->nominal) }}</div>
                                <div class="info-row"><b>Material:</b> {{ $inv->material }}</div>
                                <div class="info-row">{{ $inv->po_item_desc }}</div>
                                <div class="info-row">{{ $inv->prod_hierarchy_desc }}</div>
                            </div>
                        </div>
                    </a>
                @endforeach
                    <div>
                        <div class="d-flex justify-content-center">
                            @if ($inventory->hasPages())
                                <ul class="pagination mb-0">
                                    @if ($inventory->onFirstPage())
                                        <li class="page-item disabled"><span class="page-link">Back</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $inventory->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">Back</a></li>
                                    @endif

                                    @foreach ($inventory->links()->elements as $element)
                                        @if (is_string($element))
                                            <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                                        @endif

                                        @if (is_array($element))
                                            @foreach ($element as $page => $url)
                                                @if ($page == $inventory->currentPage())
                                                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                                @else
                                                    <li class="page-item"><a class="page-link" href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach

                                    @if ($inventory->hasMorePages())
                                        <li class="page-item"><a class="page-link" href="{{ $inventory->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next</a></li>
                                    @else
                                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                                    @endif
                                </ul>
                            @endif
                        </div>
                    </div>
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
                        <a href="{{ route('inventory.download-pdf') }}" class="btn btn-pdf btn-sm">Download PDF</a>
                        <a href="{{ route('inventory.download-excel') }}" class="btn btn-success btn-sm">Download Excel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Search Modals -->
    <div id="filterModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Filter Search</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url()->current() }}" method="GET">
                        <input type="hidden" name="search" value="1">
                        <div class="mb-3">
                            <label class="form-label">Purc Doc</label>
                            <input type="text" class="form-control form-control-sm" name="purcDoc" value="{{ request()->get('purcDoc', null) }}" placeholder="Purc Doc ...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sales Doc</label>
                            <input type="text" class="form-control form-control-sm" name="salesDoc" value="{{ request()->get('salesDoc', null) }}" placeholder="Sales Doc ...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Customer</label>
                            <input type="text" class="form-control form-control-sm" name="customer" value="{{ request()->get('customer', null) }}" placeholder="Customer ...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Material</label>
                            <input type="text" class="form-control form-control-sm" name="material" value="{{ request()->get('material', null) }}" placeholder="Material ...">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        </div>
                    </form>
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
