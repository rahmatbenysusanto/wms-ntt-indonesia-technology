<!doctype html>
<html lang="en">
<head>
    <title>Aging – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="bg-slate-50 pb-24">

<header class="sticky top-0 z-40 bg-brand-400 shadow-md shadow-brand-400/20">
    <div class="flex items-center px-4 h-14">
        <a href="{{ route('dashboardMobile') }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </a>
        <h1 class="absolute left-1/2 -translate-x-1/2 text-white font-bold text-base">Inventory Aging</h1>
    </div>
</header>

<main class="px-4 pt-4 space-y-3 pb-4">
    {{-- Aging Cards --}}
    @php
        $agingItems = [
            ['type'=>1, 'label'=>'1 – 90 Hari',   'color'=>'bg-emerald-400', 'textColor'=>'text-emerald-700', 'bg'=>'bg-emerald-50', 'qty'=>$agingType1->qty, 'total'=>$agingType1->total],
            ['type'=>2, 'label'=>'91 – 180 Hari',  'color'=>'bg-amber-400',  'textColor'=>'text-amber-700',  'bg'=>'bg-amber-50',  'qty'=>$agingType2->qty, 'total'=>$agingType2->total],
            ['type'=>3, 'label'=>'181 – 365 Hari', 'color'=>'bg-orange-400', 'textColor'=>'text-orange-700', 'bg'=>'bg-orange-50', 'qty'=>$agingType3->qty, 'total'=>$agingType3->total],
            ['type'=>4, 'label'=>'> 365 Hari',     'color'=>'bg-red-500',    'textColor'=>'text-red-700',    'bg'=>'bg-red-50',    'qty'=>$agingType4->qty, 'total'=>$agingType4->total],
        ];
    @endphp

    @foreach($agingItems as $aging)
        <a href="{{ route('dashboard.mobile.aging.detail', ['type' => $aging['type']]) }}" class="block">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 tap-card overflow-hidden relative">
                <div class="absolute left-0 top-0 bottom-0 w-1 {{ $aging['color'] }}"></div>
                <div class="pl-3 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-800 text-sm">{{ $aging['label'] }}</p>
                        <p class="text-xs text-slate-500 mt-1 font-medium">$ {{ number_format($aging['total'], 2) }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center justify-center px-4 py-2 rounded-2xl {{ $aging['bg'] }} {{ $aging['textColor'] }} font-bold text-lg">
                            {{ number_format($aging['qty']) }}
                        </span>
                        <p class="text-[10px] text-slate-400 mt-1 font-medium">QTY →</p>
                    </div>
                </div>
            </div>
        </a>
    @endforeach

    {{-- Download --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <p class="text-xs font-bold text-slate-600 mb-3 uppercase tracking-wider">Download Summary</p>
        <div class="grid grid-cols-2 gap-2">
            @foreach($agingItems as $aging)
            <div class="bg-slate-50 rounded-xl p-2">
                <p class="text-[10px] text-slate-500 font-medium mb-1.5">{{ $aging['label'] }}</p>
                <div class="flex gap-1.5">
                    <a href="{{ route('inventory.aging.detail.pdf', ['type' => $aging['type']]) }}"
                       class="flex-1 text-center text-[10px] font-bold bg-red-100 text-red-600 py-1 rounded-lg hover:bg-red-200 transition-colors">PDF</a>
                    <a href="{{ route('inventory.aging.detail.excel', ['type' => $aging['type']]) }}"
                       class="flex-1 text-center text-[10px] font-bold bg-emerald-100 text-emerald-600 py-1 rounded-lg hover:bg-emerald-200 transition-colors">XLS</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Charts --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <p class="text-sm font-bold text-slate-700 mb-3">Distribusi QTY</p>
        <div id="simple_pie_chart" data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger"]'></div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <p class="text-sm font-bold text-slate-700 mb-3">Distribusi Nominal</p>
        <div id="agingByTotalPrice" data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger"]'></div>
    </div>
</main>

@include('mobile.layout.menu')

<script>
    function makeChart(id, series, labels) {
        const el = document.getElementById(id);
        if (!el) return;
        new ApexCharts(el, {
            series: series,
            chart: { height: 220, type: 'pie' },
            labels: labels,
            legend: { position: 'bottom', fontSize: '11px' },
            dataLabels: { dropShadow: { enabled: false } },
            colors: ['#22a3a5', '#f59e0b', '#f97316', '#ef4444'],
            tooltip: { y: { formatter: v => v.toLocaleString() } }
        }).render();
    }

    const labels = ['1-90 Hari', '91-180 Hari', '181-365 Hari', '>365 Hari'];
    makeChart('simple_pie_chart',
        [{{ $agingType1->qty }}, {{ $agingType2->qty }}, {{ $agingType3->qty }}, {{ $agingType4->qty }}],
        labels
    );
    makeChart('agingByTotalPrice',
        [{{ $agingType1->total }}, {{ $agingType2->total }}, {{ $agingType3->total }}, {{ $agingType4->total }}],
        labels
    );
</script>
</body>
</html>
