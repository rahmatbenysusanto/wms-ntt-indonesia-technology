<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Packing List</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
        }

        .tbl {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .tbl th,
        .tbl td {
            border: 1px solid #333;
            padding: 4px 6px;
            vertical-align: top;
        }

        .tbl th {
            background: #eee;
        }

        .label {
            font-weight: bold;
        }

        .head {
            text-align: center;
            margin-bottom: 10px;
        }

        .head h2 {
            margin: 0 0 4px;
        }

        td.num {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="head">
        <h2>PACKING LIST</h2>
        <div>No. {{ $packingList->id }} &mdash; {{ $packingList->created_at->format('d M Y') }}</div>
    </div>

    <table style="width:100%">
        <tr>
            <td style="width:50%">
                <div><span class="label">Customer : </span>{{ $packingList->customer->name ?? '-' }}</div>
                <div><span class="label">Delivery Date : </span>
                    {{ $packingList->delivery_date ? $packingList->delivery_date->format('d M Y') : '-' }}</div>
                <div><span class="label">Delivery Location : </span>{{ $packingList->deliv_loc ?? '-' }}</div>
                <div><span class="label">Delivery Destination : </span>{{ $packingList->deliv_dest ?? '-' }}</div>
            </td>
            <td style="width:50%">
                <div><span class="label">Sales Doc : </span>
                    {{ implode(', ', $packingList->sales_docs ?? []) ?: '-' }}</div>
                <div><span class="label">Total Item : </span>{{ $packingList->qty_item }}</div>
                <div><span class="label">Total Qty : </span>{{ $packingList->qty }}</div>
                <div><span class="label">Note : </span>{{ $packingList->note ?? '-' }}</div>
            </td>
        </tr>
    </table>

    <table class="tbl">
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Sales Doc</th>
                <th>Material</th>
                <th>Item</th>
                <th>Description</th>
                <th style="width:60px">Qty</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($packingList->details as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $detail->sales_doc ?? '-' }}</td>
                    <td>{{ $detail->material ?? '-' }}</td>
                    <td>{{ $detail->item ?? '-' }}</td>
                    <td>{{ $detail->po_item_desc ?? '-' }}</td>
                    <td class="num">{{ $detail->qty }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center">No items</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align:right">Total</th>
                <th class="num">{{ $packingList->details->sum('qty') }}</th>
            </tr>
        </tfoot>
    </table>

    <table style="width:100%;margin-top:30px;text-align:center">
        <tr>
            <td style="width:50%">Created By<br><br><br><br>{{ $packingList->createdBy->name ?? '-' }}</td>
            <td style="width:50%">Released By<br><br><br><br>{{ $packingList->releasedBy->name ?? '-' }}</td>
        </tr>
    </table>
</body>

</html>
