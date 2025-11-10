<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Outbound Report</title>
    <style>
        @page {
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 3px;
            font-family: DejaVu Sans, sans-serif;
            font-size: 6px;
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
            font-size: 7px;
            table-layout: fixed; /* wajib kalau pakai width di th */
        }

        .tbl th,
        .tbl td {
            /*border: 1px solid #000;*/
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

    <h2 style="text-align: center;">Report Outbound</h2>
    <section>
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 3%;">#</th>
                    <th style="width: 15%;">Delivery Data</th>
                    <th style="width: 5%;">SO</th>
                    <th style="width: 20%;">Material</th>
                    <th style="width: 10%;">Serial Number</th>
                    <th style="width: 15%;">Customer</th>
                    <th style="width: 5%;">Type</th>
                    <th style="width: 10%;">Deliv Dest</th>
                    <th style="width: 10%;">Deliv Loc</th>
                </tr>
            </thead>
            <tbody>
            @foreach($outbound as $index => $item)

                @foreach($item->outboundDetail as $detailIndex => $detail)
                    <tr>
                        @if($detailIndex === 0)
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div><b>Deli Note Number : </b>{{ $item->delivery_note_number }}</div>
                                <div><b>Deli Date : </b>{{ \Carbon\Carbon::parse($item->delivery_date)->translatedFormat('d F Y') }}</div>
                                <div><b>PO : </b>{{ $item->purc_doc }}</div>
                            </td>
                            <td>
                                @foreach(json_decode($item->sales_docs) as $salesDoc)
                                    <div>{{ $salesDoc }}</div>
                                @endforeach
                            </td>
                        @else
                            <td></td>
                            <td></td>
                            <td></td>
                        @endif

                        <td>
                            <b>{{ $detail->inventoryPackageItem->purchaseOrderDetail->material }}</b><br>
                            {{ $detail->inventoryPackageItem->purchaseOrderDetail->po_item_desc }}<br>
                            {{ $detail->inventoryPackageItem->purchaseOrderDetail->prod_hierarchy_desc }}
                        </td>
                        <td>
                            @foreach($detail->outboundDetailSN as $serialNumber)
                                <div>{{ $serialNumber->serial_number }}</div>
                            @endforeach
                        </td>

                        @if($detailIndex === 0)
                            <td>{{ $item->customer->name }}</td>
                            <td>{{ $item->type }}</td>
                            <td>{{ $item->deliv_dest }}</td>
                            <td>{{ $item->deliv_loc }}</td>
                        @else
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @endif
                    </tr>
                @endforeach

            @endforeach

{{--                @foreach($outbound as $item)--}}
{{--                    <tr>--}}
{{--                        <td>{{ $loop->iteration }}</td>--}}
{{--                        <td>{{ $item->delivery_note_number }}</td>--}}
{{--                        <td>{{ $item->delivery_date }}</td>--}}
{{--                        <td>{{ $item->purc_doc }}</td>--}}
{{--                        <td>--}}
{{--                            @foreach(json_decode($item->sales_docs) as $salesDoc)--}}
{{--                                <div>{{ $salesDoc }}</div>--}}
{{--                            @endforeach--}}
{{--                        </td>--}}
{{--                        <td>--}}
{{--                            @foreach($item->outboundDetail as $outboundDetail)--}}
{{--                                <div>--}}
{{--                                    <div><b>{{ $outboundDetail->inventoryPackageItem->purchaseOrderDetail->material }}</b></div>--}}
{{--                                    <div>{{ $outboundDetail->inventoryPackageItem->purchaseOrderDetail->po_item_desc }}</div>--}}
{{--                                    <div>{{ $outboundDetail->inventoryPackageItem->purchaseOrderDetail->prod_hierarchy_desc }}</div>--}}
{{--                                </div>--}}
{{--                                <br>--}}
{{--                            @endforeach--}}
{{--                        </td>--}}
{{--                        <td>--}}
{{--                            @foreach($item->outboundDetail as $outboundDetail)--}}
{{--                                <div>{{ $outboundDetail->qty }}</div>--}}
{{--                            @endforeach--}}
{{--                        </td>--}}
{{--                        <td>{{ $item->customer->name }}</td>--}}
{{--                        <td>{{ $item->type }}</td>--}}
{{--                    </tr>--}}
{{--                @endforeach--}}
            </tbody>
        </table>
    </section>
</body>
</html>
