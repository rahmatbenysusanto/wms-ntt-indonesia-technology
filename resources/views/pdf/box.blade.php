<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Box Product</title>
    <style>
        @page {
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 6px;
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
        }

        /* Singkirkan spasi ekstra dari judul & section */
        h2 { margin: 0 0 4px 0; }
        section { margin: 0; padding: 0; }

        .row { width: 100%; clear: both; }
        .col-4, .col-6 { float: left; box-sizing: border-box; padding: 5px; }
        .col-4 { width: 33.33%; }
        .col-6 { width: 50%; }

        .tbl { width: 100%; border-collapse: collapse; font-size: 8px; table-layout: fixed; }
        .tbl th, .tbl td { border: 1px solid #000; padding: 2px; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word; }
        .tbl thead th { background: #f2f2f2; text-align: left; }
        .tbl tbody tr:nth-child(even) { background: #fafafa; }

        .tbl th.qty, .tbl td.qty { text-align: center; }

        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Box Products</h2>
    <section style="margin-top: 20px;">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Box</th>
                    <th>Storage</th>
                    <th>Purc Doc</th>
                    <th>Sales Doc</th>
                    <th style="width: 5%; text-align: center;">Item</th>
                    <th>Material</th>
                    <th>PO Item Desc</th>
                    <th>Prod Hierarchy Desc</th>
                    <th style="width: 5%; text-align: center;">QTY</th>
                    <th>Serial Number</th>
                    <th>Note Return</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($listBox as $indexDetail => $detail)
                @foreach ($detail->inventoryPackageItem as $index => $item)
                    @if($index == 0)
                        <tr>
                            <td>
                                <div>{{ $detail->number }}</div>
                                <div>{{ $detail->reff_number }}</div>
                            </td>
                            <td>{{ $detail->storage->raw.'-'.$detail->storage->area.'-'.$detail->storage->rak.'-'.$detail->storage->bin }}</td>
                            <td>{{ $detail->purchaseOrder->purc_doc }}</td>
                            <td>{{ $item->purchaseOrderDetail->sales_doc }}</td>
                            <td style="text-align: center;">{{ $item->purchaseOrderDetail->item }}</td>
                            <td>{{ $item->purchaseOrderDetail->material }}</td>
                            <td>{{ $item->purchaseOrderDetail->po_item_desc }}</td>
                            <td>{{ $item->purchaseOrderDetail->prod_hierarchy_desc }}</td>
                            <td style="text-align: center;">{{ number_format($item->qty) }}</td>
                            <td>
                                @foreach ($item->inventoryPackageItemSN as $serialNumber)
                                    <div>{{ $serialNumber->serial_number }}</div>
                                @endforeach
                            </td>
                            <td>{{ $detail->note }}</td>
                        </tr>
                    @else
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $item->purchaseOrderDetail->sales_doc }}</td>
                            <td style="text-align: center;">{{ $item->purchaseOrderDetail->item }}</td>
                            <td>{{ $item->purchaseOrderDetail->material }}</td>
                            <td>{{ $item->purchaseOrderDetail->po_item_desc }}</td>
                            <td>{{ $item->purchaseOrderDetail->prod_hierarchy_desc }}</td>
                            <td style="text-align: center;">{{ number_format($item->qty) }}</td>
                            <td>
                                @foreach ($item->inventoryPackageItemSN as $serialNumber)
                                    <div>{{ $serialNumber->serial_number }}</div>
                                @endforeach
                            </td>
                            <td></td>
                        </tr>
                    @endif
                @endforeach
            @endforeach
            </tbody>
        </table>
    </section>
</body>
</html>
