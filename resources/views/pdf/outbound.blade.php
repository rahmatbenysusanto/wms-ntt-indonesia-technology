<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Outbound</title>
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

        @page {
            margin: 40px 30px 60px 30px;
        }
    </style>
</head>

<body>
    <section>
        <div class="row">
            <div class="col-4">
                <div><i>FROM</i></div>
                <div><b>PT. Transkargo Solusindo</b></div>
                <div>Komplek Pergudangan Tunas Daan Mogot</div>
                <div>Blok B2 No.11 Kebon Besar</div>
                <div>Batu Ceper, Tangerang 15122</div>
            </div>
            <div class="col-4">
                @if ($outbound->deliv_dest == 'client')
                    <div><i>DELIVER / SHIP TO </i></div>
                    <div><b>{{ $outbound->customer->name }}</b></div>
                    <div>{{ $outbound->deliv_loc }}</div>
                @elseif($outbound->deliv_dest == 'general room')
                    <div><i>DELIVER / SHIP TO </i></div>
                    <div><b>General Room</b></div>
                @elseif($outbound->deliv_dest == 'pm room')
                    <div><i>DELIVER / SHIP TO </i></div>
                    <div><b>PM Room</b></div>
                @elseif($outbound->deliv_dest == 'spare room')
                    <div><i>DELIVER / SHIP TO </i></div>
                    <div><b>Spare Room</b></div>
                @elseif($outbound->deliv_dest == '-')
                    <div><i>DELIVER / SHIP TO </i></div>
                    <div><b>Return To Inventory</b></div>
                @endif
            </div>
            <div class="col-4">
                <table>
                    <tr>
                        <td>NO</td>
                        <td>:</td>
                        <td>{{ $outbound->delivery_note_number }}</td>
                    </tr>
                    <tr>
                        <td>Purc Doc</td>
                        <td>:</td>
                        <td>{{ $outbound->purc_doc }}</td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($outbound->delivery_date)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>SO</td>
                        <td>:</td>
                        <td>
                            @php
                                $so = [];
                                foreach ($outboundDetail as $detail) {
                                    $val = $detail->inventoryPackageItem->purchaseOrderDetail->sales_doc ?? null;
                                    if ($val) {
                                        $so[] = $val;
                                    }
                                }
                                $so = array_unique($so);
                            @endphp
                            {{ implode(', ', $so) }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </section>

    <div class="clearfix"></div>

    <section style="margin-top: 20px;">
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 20%;">Product</th>
                    <th style="width: 55%;">Description</th>
                    <th style="width: 8%; text-align: center;">QTY</th>
                    <th style="width: 17%;">Serial Number</th>
                </tr>
            </thead>
            <tbody>
                @php $number = 1; @endphp
                @foreach ($outboundDetail as $detail)
                    @php
                        $sns = $detail->outboundDetailSN;
                        $chunks = $sns->chunk(20);
                    @endphp
                    @foreach ($chunks as $idx => $chunk)
                        <tr>
                            <td style="text-align: center">{{ $idx == 0 ? $number++ : '' }}</td>
                            <td>
                                @if ($idx == 0)
                                    <div>{{ $detail->inventoryPackageItem->purchaseOrderDetail->material }}</div>
                                    <div>{{ $detail->inventoryPackageItem->purchaseOrderDetail->sales_doc }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($idx == 0)
                                    <div>{{ $detail->inventoryPackageItem->purchaseOrderDetail->po_item_desc }}</div>
                                    <div>{{ $detail->inventoryPackageItem->purchaseOrderDetail->prod_hierarchy_desc }}
                                    </div>
                                    @if ($detail->inventoryPackageItem->inventoryPackage->storage->id == 1)
                                        <div><b style="color: green;">Note: Delivered Directly to Client</b></div>
                                    @endif
                                @endif
                            </td>
                            <td style="text-align: center">{{ $idx == 0 ? number_format($detail->qty) : '' }}</td>
                            <td>
                                @foreach ($chunk as $serialNumber)
                                    <span class="serial-item">{{ $serialNumber->serial_number }}</span>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </section>

    <div class="clearfix"></div>

    <section style="margin-top: 80px; page-break-inside: avoid;">
        <div class="row">
            <div class="col-6" style="text-align: center">
                <div style="font-weight: bold;">WH Transkargo Solusindo</div>
            </div>
            <div class="col-6" style="text-align: center">
                <div style="font-weight: bold;">Team Movers</div>
            </div>
        </div>
    </section>
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $size = 9;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size);
            $x = $pdf->get_width() - $width - 20;
            $y = $pdf->get_height() - 30;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>

</html>
