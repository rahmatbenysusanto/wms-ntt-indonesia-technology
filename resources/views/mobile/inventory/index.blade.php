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
            <div class="col-2">

            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12 mb-3">
            <form action="{{ url()->current() }}" method="GET">
                <div class="row gx-1">
                    <div class="col-10">
                        <input type="number" class="form-control w-100" name="search" placeholder="Search ...">
                    </div>
                    <div class="col-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="mdi mdi-search-web" style="font-size: 14px"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-12">
            @foreach($inventory as $index => $inv)
                <div class="card p-2 mb-3">
                    <div><b>Purc Doc: </b>{{ $inv->purc_doc }}</div>
                    <div><b>Sales Doc: </b>{{ $inv->sales_doc }}</div>
                    <div><b>Item: </b>{{ $inv->item }}</div>
                    <div><b>Material: </b>{{ $inv->material }}</div>
                    <div><b>Storage: </b>{{ $inv->raw }} - {{ $inv->area }} - {{ $inv->rak }} - {{ $inv->bin }}</div>
                    <div><b>QTY: </b>{{ number_format($inv->qty) }}</div>
                </div>
            @endforeach
            <div class="d-flex justify-content-end mt-2">
                    @if ($inventory->hasPages())
                        <ul class="pagination">
                            @if ($inventory->onFirstPage())
                                <li class="disabled"><span>&laquo; Previous</span></li>
                            @else
                                <li><a href="{{ $inventory->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                            @endif

                            @foreach ($inventory->links()->elements as $element)
                                @if (is_string($element))
                                    <li class="disabled"><span>{{ $element }}</span></li>
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        @if ($page == $inventory->currentPage())
                                            <li class="active"><span>{{ $page }}</span></li>
                                        @else
                                            <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach

                            @if ($inventory->hasMorePages())
                                <li><a href="{{ $inventory->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
                            @else
                                <li class="disabled"><span>Next &raquo;</span></li>
                            @endif
                        </ul>
                    @endif

                </div>
        </div>
    </div>
</div>

</body>
</html>
