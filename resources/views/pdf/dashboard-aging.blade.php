<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report Dashboard Aging</title>
    <style>
        @page {
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 6px;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }
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
    <h2 style="text-align: center;">Product Aging</h2>
    <section>
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">#</th>
                    <th style="width: 8%;">Purc Doc</th>
                    <th style="width: 8%;">Sales Doc</th>
                    <th>Material</th>
                    <th style="width: 7%; text-align: center;">Stock</th>
                    <th style="width: 10%;">Nominal (USD)</th>
                    <th style="width: 10%;">Nominal (IDR)</th>
                    <th style="width: 10%;">Aging Date</th>
                    <th style="width: 10%;">Serial Number</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productAging as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $product->purchaseOrderDetail->purchaseOrder->purc_doc }}</td>
                        <td>{{ $product->purchaseOrderDetail->sales_doc }}</td>
                        <td>
                            <div><b>{{ $product->purchaseOrderDetail->material }}</b></div>
                            <div>{{ $product->purchaseOrderDetail->po_item_desc }}</div>
                            <div><b>{{ $product->purchaseOrderDetail->prod_hierarchy_desc }}</b></div>
                        </td>
                        <td style="text-align: center;">{{ number_format($product->qty) }}</td>
                        <td>${{ number_format($product->purchaseOrderDetail->net_order_price * $product->qty) }}</td>
                        <td>Rp{{ number_format($product->purchaseOrderDetail->price_idr * $product->qty) }}</td>
                        <td>{{ \Carbon\Carbon::parse($product->aging_date)->translatedFormat('d F Y') }}</td>
                        <td>
                            @foreach($product->inventoryPackageItem->inventoryPackageItemSN as $serialNumber)
                                <div>{{ $serialNumber->serial_number }}</div>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</body>
</html>
