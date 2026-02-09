<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Download Cycle Count</title>
    <style>
        @page {
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 6px;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
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
    <h3 style="text-align: center;">Cycle Count History</h3>
    <section>
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">#</th>
                    <th>Client</th>
                    <th>Purc Doc</th>
                    <th>Sales Doc</th>
                    <th>Material</th>
                    <th>PO Item Desc</th>
                    <th>Prod Hierarchy Desc</th>
                    <th style="width: 5%; text-align: center;">QTY</th>
                    <th>Storage</th>
                    <th style="width: 7%; text-align: center;">Type</th>
                    <th>Date</th>
                    <th>Serial Number</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cycleCount as $item)
                    <tr>
                        <td style="width: 5%; text-align: center;">{{ $loop->iteration }}</td>
                        <td>{{ $item->purchaseOrder->customer->name ?? '-' }}</td>
                        <td>
                            <div>{{ $item->purchaseOrder->purc_doc }}</div>
                            @if ($item->inventoryPackageItem->inventoryPackage->storage->id == 1)
                                <b>Cross Docking</b>
                            @endif
                        </td>
                        <td>{{ $item->purchaseOrderDetail->sales_doc }}</td>
                        <td>{{ $item->purchaseOrderDetail->material }}</td>
                        <td>{{ $item->purchaseOrderDetail->po_item_desc }}</td>
                        <td>{{ $item->purchaseOrderDetail->prod_hierarchy_desc }}</td>
                        <td style="width: 5%; text-align: center;">{{ number_format($item->qty) }}</td>
                        <td>
                            @if (in_array($item->inventoryPackageItem->inventoryPackage->storage->id, [2, 3, 4]))
                                <b>{{ $item->inventoryPackageItem->inventoryPackage->storage->raw }}</b>
                            @elseif($item->inventoryPackageItem->inventoryPackage->storage->id == 1)
                                <b>Cross Docking</b>
                            @else
                                <b>{{ $item->inventoryPackageItem->inventoryPackage->storage->raw }}</b> -
                                <b>{{ $item->inventoryPackageItem->inventoryPackage->storage->area }}</b> -
                                <b>{{ $item->inventoryPackageItem->inventoryPackage->storage->rak }}</b> -
                                <b>{{ $item->inventoryPackageItem->inventoryPackage->storage->bin }}</b>
                            @endif
                        </td>
                        <td style="width: 5%; text-align: center;">{{ $item->type }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}</td>
                        <td>
                            @foreach (json_decode($item->serial_number) ?? [] as $serialNumber)
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
