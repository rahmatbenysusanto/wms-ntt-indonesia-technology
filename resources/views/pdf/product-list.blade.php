<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Product List</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
        }

        .row {
            width: 100%;
            clear: both;
        }

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

        .clearfix {
            clear: both;
        }

        .tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            table-layout: fixed;
            /* wajib kalau pakai width di th */
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

        .serial-item {
            display: block;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">Product List Inventory</h2>
    <section style="margin-top: 20px;">
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">#</th>
                    <th>Client</th>
                    <th style="width: 8%;">Purc Doc</th>
                    <th style="width: 8%;">Sales Doc</th>
                    <th>Material</th>
                    <th style="width: 7%; text-align: center;">Stock</th>
                    <th style="width: 10%;">Nominal USD</th>
                    <th style="width: 10%;">Nominal IDR</th>
                    <th style="width: 10%;">Serial Number</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inventoryDetail as $detail)
                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $detail->client_name }}</td>
                        <td>{{ $detail->purc_doc }}</td>
                        <td>{{ $detail->sales_doc }}</td>
                        <td>
                            <div><b>{{ $detail->material }}</b></div>
                            <div>{{ $detail->po_item_desc }}</div>
                            <div><b>{{ $detail->prod_hierarchy_desc }}</b></div>
                        </td>
                        <td style="text-align: center;">{{ number_format($detail->stock) }}</td>
                        <td>${{ number_format($detail->nominal) }}</td>
                        <td>Rp{{ number_format($detail->nominalIDR) }}</td>
                        <td>
                            @foreach ($detail->serialNumber as $serialNumber)
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
