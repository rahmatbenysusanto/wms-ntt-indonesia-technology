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
                <h5 class="mb-0 text-center text-white">Inbound</h5>
            </div>
            <div class="col-2">

            </div>
        </div>
    </div>

    <div class="container-fluid mt-3" style="margin-bottom: 80px;">
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
            <a class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">Search Filter</a>
            @if(request()->get('search') == 1)
                <a href="{{ url()->current() }}" class="btn btn-danger btn-sm">Clear Filter</a>
            @endif
        </div>

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
