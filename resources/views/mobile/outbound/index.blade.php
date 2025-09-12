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

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
            padding-bottom: 50px;
        }

        .btn-info {
            background-color: #39BBBD!important;
            border-color: #39BBBD!important;
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

    <div class="container-fluid mt-3" style="margin-bottom: 80px">
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
            <a class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">Search Filter</a>
            @if(request()->get('search') == 1)
                <a href="{{ url()->current() }}" class="btn btn-danger btn-sm">Clear Filter</a>
            @endif
        </div>

        <div class="row">
            <div class="col-12">
                @foreach ($outbound as $item)
                    <a href="{{ route('outbound.indexDetail.mobile', ['id' => $item->id]) }}">
                        <div class="card inventory-card mb-2">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div>
                                        <div class="fw-bold">{{ $item->purc_doc }}</div>
                                        <small class="text-muted">SO#: {{ $item->sales_docs }}</small>
                                        <div class="info-row"><b>{{ $item->delivery_note_number ?? '-' }}</b></div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-primary">{{ number_format($item->qty) }}</span>
                                        <div class="info-row fw-bold">
                                            {{ \Carbon\Carbon::parse($item->delivery_date)->translatedFormat('d M Y') }}
                                        </div>
                                        @if($item->status == 'outbound')
                                            <span class="badge bg-secondary">Outbound</span>
                                        @else
                                            <span class="badge bg-warning">Return</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="info-row"><b>Nominal:</b> $ {{ number_format($item->nominal) }}</div>
                                <div class="info-row"><b>Deliv Dest:</b>{{ $item->deliv_dest }}</div>
                            </div>
                        </div>
                    </a>
                @endforeach
                <div>
                        <div class="d-flex justify-content-center">
                            @if ($outbound->hasPages())
                                <ul class="pagination mb-0">
                                    @if ($outbound->onFirstPage())
                                        <li class="page-item disabled"><span class="page-link">Back</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $outbound->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">Back</a></li>
                                    @endif

                                    @foreach ($outbound->links()->elements as $element)
                                        @if (is_string($element))
                                            <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                                        @endif

                                        @if (is_array($element))
                                            @foreach ($element as $page => $url)
                                                @if ($page == $outbound->currentPage())
                                                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                                @else
                                                    <li class="page-item"><a class="page-link" href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach

                                    @if ($outbound->hasMorePages())
                                        <li class="page-item"><a class="page-link" href="{{ $outbound->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next</a></li>
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
                            <select class="form-control form-control-sm" name="customer">
                                <option value="">-- Select Customer --</option>
                                @foreach($customer as $item)
                                    <option {{ request()->get('customer') == $item->name ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Material</label>
                            <select class="form-control form-control-sm select2" name="material">
                                <option value="">-- Select Material --</option>
                                @foreach($products as $item)
                                    <option {{ request()->get('material') == $item->material ? 'selected' : '' }}>{{ $item->material }}</option>
                                @endforeach
                            </select>
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
        $(document).ready(function() {
            $('#filterModal').on('shown.bs.modal', function () {
                $('.select2').select2({
                    dropdownParent: $('#filterModal'),
                    placeholder: "-- Select Material --",
                    allowClear: true,
                    width: '100%'
                });
            });
        });
    </script>
</body>
</html>
