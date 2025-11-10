<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Order</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; }
        .row { width: 100%; clear: both; }
        .col-4 {
            float: left;
            width: 33.33%;
            box-sizing: border-box;
            padding: 5px;
        }
        .col-6 {
            float: left;
            width: 50%;
            box-sizing: border-box;
            padding: 5px;
        }
        .clearfix { clear: both; }

        .tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            table-layout: fixed; /* wajib kalau pakai width di th */
        }

        .tbl th,
        .tbl td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .tbl thead th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .tbl tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        /* Rata-tengah kolom QTY */
        .tbl th.qty,
        .tbl td.qty {
            text-align: center;
        }

        .desc b {
            display: inline-block;
            width: 90px;
        }

        .serial-item { display: block; }

        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>

    <section>
        <table>
            <tr>
                <td style="font-weight: bold">Purc Doc</td>
                <td>:</td>
                <td>{{ $purchaseOrder->purc_doc }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold">Customer</td>
                <td>:</td>
                <td>{{ $purchaseOrder->customer->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold">Vendor</td>
                <td>:</td>
                <td>{{ $purchaseOrder->vendor->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold">Created Date</td>
                <td>:</td>
                <td>{{ $purchaseOrder->created_at }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold">Created By</td>
                <td>:</td>
                <td>{{ $purchaseOrder->user->name }}</td>
            </tr>
        </table>
    </section>

    <div class="clearfix"></div>

    <section style="margin-top: 20px">
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width:5%; text-align: center;">No</th>
                    <th>Sales Doc</th>
                    <th style="width: 5%; text-align: center;">Item</th>
                    <th>Material</th>
                    <th>PO Item Desc</th>
                    <th>Prod Hierarchy Desc</th>
                    <th style="width: 5%; text-align: center;">PO QTY</th>
                    <th style="width: 5%; text-align: center;">QTY QC</th>
                    <th>Serial Number</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrderDetail as $detail)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $detail->sales_doc }}</td>
                        <td style="text-align: center;">{{ $detail->item }}</td>
                        <td>{{ $detail->material }}</td>
                        <td>{{ $detail->po_item_desc }}</td>
                        <td>{{ $detail->prod_hierarchy_desc }}</td>
                        <td style="text-align: center;">{{ number_format($detail->po_item_qty) }}</td>
                        <td style="text-align: center;">{{ number_format($detail->qty_qc) }}</td>
                        <td>
                            @foreach($detail->serialNumber as $serialNumber)
                                <div>{{ $serialNumber }}</div>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</body>
</html>
