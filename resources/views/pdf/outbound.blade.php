<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Outbound</title>
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
        <div class="row">
            <div class="col-4">
                <div><i>FROM</i></div>
                <div><b>PT.  NTT Indonesia Technology</b></div>
                <div>DBS Tower 22nd Floor</div>
                <div>JL. Prof. Dr. Satrio Kav. 3-5</div>
                <div>Jakarta Selatan 12940 Indonesia</div>
                <div>Phone : (021) 2854-8000</div>
            </div>
            <div class="col-4">
                <div><i>DELIVER / SHIP TO </i></div>
                <div><b>{{ $outbound->customer->name }}</b></div>
                <div>{{ $outbound->deliv_loc }}</div>
            </div>
            <div class="col-4">
                <table>
                    <tr>
                        <td>NO</td>
                        <td>:</td>
                        <td>{{ $outbound->delivery_note_number }}</td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($outbound->delivery_date)->translatedFormat('d F Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </section>

    <div class="clearfix"></div>

    <section style="margin-top: 20px">
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:15%;">Product</th>
                    <th style="width: 55%">Description</th>
                    <th style="width:10%; text-align: center">QTY</th>
                    <th style="width:15%;">Serial Number</th>
                </tr>
            </thead>
            <tbody>
            @php $number = 1; @endphp
            @foreach($outboundDetail as $detail)
                <tr>
                    <td>{{ $number++ }}</td>
                    <td>{{ $detail->inventoryPackageItem->purchaseOrderDetail->sales_doc }}</td>
                    <td>
                        <div><b>{{ $detail->inventoryPackageItem->purchaseOrderDetail->material }}</b></div>
                        <div>{{ $detail->inventoryPackageItem->purchaseOrderDetail->po_item_desc }}</div>
                        <div>{{ $detail->inventoryPackageItem->purchaseOrderDetail->prod_hierarchy_desc }}</div>
                    </td>
                    <td style="text-align: center">{{ number_format($detail->qty) }}</td>
                    <td>
                        @foreach($detail->outboundDetailSN as $serialNumber)
                            <span class="serial-item">{{ $serialNumber->serial_number }}</span>
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>

    <div class="clearfix"></div>

    <section style="margin-top: 80px">
        <div class="row">
            <div class="col-6" style="text-align: center">
                <div>Logistic Executive</div>
            </div>
            <div class="col-6" style="text-align: center">
                <div>Customer's name & signature</div>
            </div>
        </div>
    </section>
</body>
</html>
