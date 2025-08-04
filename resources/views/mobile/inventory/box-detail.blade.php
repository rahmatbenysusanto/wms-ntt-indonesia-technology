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
                <a href="{{ route('inventory.box.mobile') }}" class="ps-3">
                    <i class="mdi mdi-arrow-left-thin text-white" style="font-size: 32px"></i>
                </a>
            </div>
            <div class="col-8 d-flex justify-content-center align-items-center">
                <h5 class="mb-0 text-center text-white">Box Detail</h5>
            </div>
            <div class="col-2">

            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card p-2">
                <div><b>Purc Doc: </b>{{ $box->purchaseOrder->purc_doc }}</div>
                <div><b>Number: </b>{{ $box->number }}</div>
                <div><b>Reff: </b>{{ $box->reff_number }}</div>
                <div><b>Storage: </b>{{ $box->storage->raw }} - {{ $box->storage->area }} - {{ $box->storage->rak }} - {{ $box->storage->bin }}</div>
            </div>
        </div>

        <h5>List Item</h5>
        <div class="col-12">
            @foreach($detail as $item)
                <div class="card p-2 mb-3">
                    <div class="row">
                        <div class="col-9">
                            <div><b>Sales Doc: </b>{{ $item->purchaseOrderDetail->sales_doc }}</div>
                            <div><b>Item: </b>{{ $item->purchaseOrderDetail->item }}</div>
                            <div><b>QTY: </b>{{ $item->qty }}</div>
                            <div><b>Material: </b>{{ $item->purchaseOrderDetail->material }}</div>
                            <div><b>Desc: </b>{{ $item->purchaseOrderDetail->po_item_desc }}</div>
                            <div><b>Hierarchy: </b>{{ $item->purchaseOrderDetail->prod_hierarchy_desc }}</div>
                        </div>
                        <div class="col-3 d-flex flex-column">
                            @if($item->is_parent == 1)
                                <span class="badge bg-danger-subtle text-danger">Parent</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">Child</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

</body>
</html>
