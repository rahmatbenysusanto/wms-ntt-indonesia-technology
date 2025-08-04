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
                <a href="{{ route('inbound.index.mobile') }}" class="ps-3">
                    <i class="mdi mdi-arrow-left-thin text-white" style="font-size: 32px"></i>
                </a>
            </div>
            <div class="col-8 d-flex justify-content-center align-items-center">
                <h5 class="mb-0 text-center text-white">Inbound Detail</h5>
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
                <div><b>Purc Doc: </b>{{ $purchaseOrder->purc_doc }}</div>
                <div><b>Customer: </b>{{ $purchaseOrder->customer->name }}</div>
                <div><b>Date: </b>{{ \Carbon\Carbon::parse($purchaseOrder->created_at)->translatedFormat('d F Y H:i') }}</div>
            </div>
        </div>

        <h5>List Item</h5>
        <div class="col-12">
            @foreach($purchaseOrderDetail as $detail)
                <div class="card p-2 mb-3">
                    <div><b>Sales Doc: </b>{{ $detail->sales_doc }}</div>
                    <div><b>Item: </b>{{ $detail->item }}</div>
                    <div><b>QTY: </b>{{ $detail->po_item_qty }}</div>
                    <div><b>Material: </b>{{ $detail->material }}</div>
                    <div><b>Desc: </b>{{ $detail->po_item_desc }}</div>
                    <div><b>Hierarchy: </b>{{ $detail->prod_hierarchy_desc }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

</body>
</html>
