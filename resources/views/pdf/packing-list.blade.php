<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Packing List</title>
    <style>
        @page { margin: 28px 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        .hd { width: 100%; border-collapse: collapse; }
        .hd td { vertical-align: top; }
        .hd .right { text-align: right; }
        .hd h1 { margin: 0; font-size: 22px; font-weight: bold; }
        .hd .sub { font-size: 10px; }
        .line { border-top: 1px solid #000; margin: 6px 0 10px; }
        .info { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info td { vertical-align: top; width: 50%; padding: 0; }
        .info .lbl { font-weight: bold; }
        .info .addr { line-height: 1.5; }
        .info .col2 div { padding: 1px 0; }
        .info .col2 .k { display: inline-block; width: 100px; }
        .tbl { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .tbl th, .tbl td { border: 1px solid #000; padding: 4px 5px; vertical-align: top; }
        .tbl th { font-size: 9px; text-align: center; font-weight: bold; }
        .tbl td.c { text-align: center; }
        .tbl td.r { text-align: right; }
        .tbl .sn { font-size: 9px; }
        .tbl tfoot td { font-weight: bold; }
        .carton { margin-top: 8px; font-weight: bold; }
        .sign { width: 100%; margin-top: 26px; border-collapse: collapse; }
        .sign td { width: 50%; vertical-align: bottom; }
        .sign .r { text-align: right; }
        .sign .space { height: 62px; }
        .sign .name { font-weight: bold; font-size: 11px; }
    </style>
</head>
<body>
@php
    $roman = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
    $docDate = $packingList->delivery_date ?? $packingList->created_at;
    $docNo = $packingList->doc_no ?? ('No.DRAFT/PL-NTTIT/' . $roman[$docDate->month - 1] . '/' . $docDate->year);
    $poNo = optional(optional(optional($packingList->details->first())->purchaseOrderDetail)->purchaseOrder)->purc_doc;
    $totalQty = $packingList->details->sum('qty');
    $totalBox = $packingList->details->sum(fn ($d) => $d->inventoryPackageItem?->qty ?? 0);
    $isFinal = ($mode ?? 'original') === 'final' && $packingList->repacked_at;
    $fmt = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, ',', ''), '0'), ',');
    $dims = $isFinal
        ? $fmt($packingList->repack_length) . ' x ' . $fmt($packingList->repack_width) . ' x ' . $fmt($packingList->repack_height)
        : null;
@endphp
    <table class="hd">
        <tr>
            <td></td>
            <td class="right">
                <h1>PACKING LIST</h1>
                <div class="sub">1 of 1</div>
                <div class="sub">{{ $docNo }}</div>
                @if ($isFinal)
                    <div class="sub"><strong>FINAL (REPACKED)</strong></div>
                @endif
            </td>
        </tr>
    </table>
    <div class="line"></div>

    <table class="info">
        <tr>
            <td class="addr">
                <div class="lbl">DELIVER TO:</div>
                <div>DN No. {{ $packingList->dn_no ?? '-' }}</div>
                <div>{{ $packingList->customer->name ?? '-' }}</div>
                <div>{{ $packingList->deliv_loc ?? '-' }}</div>
                <div>{{ $packingList->customer->kota ?? '-' }}, {{ $packingList->customer->kode_pos ?? '-' }}, {{ $packingList->customer->provinsi ?? '-' }}</div>
                <div>Indonesia</div>
            </td>
            <td class="col2">
                <div><span class="k">Reff</span>: {{ $packingList->note ?? '-' }}</div>
                <div><span class="k">Date</span>: {{ $docDate->format('d-M-y') }}</div>
                <div><span class="k">PO No.</span>: {{ $poNo ?? '-' }}</div>
                <div><span class="k">Cust. PO No.</span>: {{ $packingList->cust_po_no ?? '-' }}</div>
                <div><span class="k">PIC</span>: {{ $packingList->pic ?? '-' }}</div>
                <div><span class="k">Ship Via</span>: {{ $packingList->ship_via ?? '-' }}</div>
            </td>
        </tr>
    </table>

    @if ($isFinal)
        <table class="tbl" style="margin-bottom:10px">
            <thead>
                <tr>
                    <th>Total Koli</th>
                    <th>Pallet Dimension (P x L x T) /CM</th>
                    <th>Total Weight /Kg</th>
                    <th>Repacked At</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="c">{{ $packingList->koli_qty }}</td>
                    <td class="c">{{ $dims }}</td>
                    <td class="c">{{ number_format((float) $packingList->repack_weight, 2, ',', '.') }}</td>
                    <td class="c">
                        {{ $packingList->repacked_at->format('d-M-y H:i') }} / {{ $packingList->repackedBy->name ?? '-' }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

    <table class="tbl">
        <thead>
            <tr>
                <th style="width:26px">No</th>
                <th style="width:82px">Part Number</th>
                <th style="width:58px">HS Code</th>
                <th>Description</th>
                <th style="width:48px">Qty unit</th>
                <th style="width:48px">Qty Box</th>
                <th style="width:66px">Gross Weight/Kg</th>
                <th style="width:72px">Measurment /CM</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($packingList->details as $detail)
                <tr>
                    <td class="c">{{ $loop->iteration }}</td>
                    <td>{{ $detail->part_number ?: ($detail->material ?? '-') }}</td>
                    <td class="c">{{ $detail->hs_code ?: '-' }}</td>
                    <td>{{ $detail->po_item_desc ?? '-' }}</td>
                    <td class="c">{{ $detail->qty }}</td>
                    <td class="c">{{ $detail->inventoryPackageItem?->qty ?? '-' }}</td>
                    <td class="c">{{ $detail->gross_weight ?? '-' }}</td>
                    <td class="c">{{ $detail->measurement ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="c">No items</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td>Shipment Weight : {{ $isFinal ? number_format((float) $packingList->repack_weight, 2, ',', '.') . ' kg' : '- kg' }}</td>
                <td class="c">{{ $totalQty }}</td>
                <td class="c">{{ $totalBox }}</td>
                <td class="r">-</td>
                <td class="c"></td>
            </tr>
        </tfoot>
    </table>

    <div class="carton">
        @if ($isFinal)
            Total Koli : {{ $packingList->koli_qty }} Koli &mdash; Pallet {{ $dims }} CM &mdash;
            {{ number_format((float) $packingList->repack_weight, 2, ',', '.') }} Kg
        @else
            Total Carton : {{ $totalBox }} Cartons
        @endif
    </div>

    <table class="sign">
        <tr>
            <td></td>
            <td class="r">
                <div class="space"></div>
                <div class="name">PT NTT INDONESIA TECHNOLOGY</div>
                <div>Logistic</div>
            </td>
        </tr>
    </table>
</body>
</html>
