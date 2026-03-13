<!doctype html>
<html lang="en">
<head>
    <title>Outbound Detail – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="bg-slate-50 pb-24">

<header class="sticky top-0 z-40 bg-brand-400 shadow-md shadow-brand-400/20">
    <div class="flex items-center px-4 h-14">
        <a href="{{ route('outbound.index.mobile') }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </a>
        <h1 class="absolute left-1/2 -translate-x-1/2 text-white font-bold text-base">Outbound Detail</h1>
        <button onclick="document.getElementById('downloadModal').classList.remove('hidden')"
                class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white ml-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
        </button>
    </div>
</header>

<main class="px-4 pt-4 space-y-3">
    {{-- Outbound Info Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7M5 3h14M5 21h14" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-slate-800 text-sm truncate">{{ $outbound->delivery_note_number }}</p>
                <p class="text-xs text-slate-500">{{ $outbound->customer?->name ?? '-' }}</p>
            </div>
            @if($outbound->status == 'outbound')
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full badge-outbound flex-shrink-0">Outbound</span>
            @else
                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full badge-return flex-shrink-0">Return</span>
            @endif
        </div>
        <div class="text-xs text-slate-400 flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75" /></svg>
            {{ \Carbon\Carbon::parse($outbound->delivery_date)->translatedFormat('d F Y') }}
        </div>
    </div>

    {{-- Items --}}
    <h2 class="text-sm font-bold text-slate-700 px-1">Daftar Item</h2>

    @forelse($outboundDetail as $detail)
        <a href="{{ route('outbound.indexDetailSN.mobile', ['id' => $detail->id, 'outbound' => $detail->outbound_id]) }}" class="block">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 tap-card border-l-4 border-l-violet-400">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1 min-w-0 pr-2">
                        <p class="font-bold text-slate-800 text-sm">{{ $detail->inventoryPackageItem?->purchaseOrderDetail?->purchaseOrder?->purc_doc ?? '-' }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">SO: {{ $detail->inventoryPackageItem?->purchaseOrderDetail?->sales_doc ?? '-' }}</p>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-violet-100 text-violet-700 flex-shrink-0">{{ number_format($detail->qty) }}</span>
                </div>
                <p class="text-xs font-semibold text-slate-700">{{ $detail->inventoryPackageItem?->purchaseOrderDetail?->material }}</p>
                <p class="text-xs text-slate-500 truncate">{{ $detail->inventoryPackageItem?->purchaseOrderDetail?->po_item_desc }}</p>
                <p class="text-xs text-slate-400 mt-1 font-medium">$ {{ number_format($detail->qty * ($detail->inventoryPackageItem?->purchaseOrderDetail?->net_order_price ?? 0)) }}</p>
            </div>
        </a>
    @empty
        <div class="text-center py-16 text-slate-400">
            <p class="text-sm font-medium">Tidak ada item ditemukan</p>
        </div>
    @endforelse
</main>

{{-- Download Modal --}}
<div id="downloadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-5">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('downloadModal').classList.add('hidden')"></div>
    <div class="relative w-full max-w-xs bg-white rounded-3xl p-6 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 mb-4">Download Laporan</h3>
        <div class="space-y-3">
            <a href="{{ route('outbound.download-pdf', ['id' => $outbound->id]) }}"
               class="flex items-center gap-3 bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-sm rounded-2xl px-4 py-3 transition-all active:scale-[.98]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                Download PDF
            </a>
            <a href="{{ route('outbound.download-excel', ['id' => $outbound->id]) }}"
               class="flex items-center gap-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-sm rounded-2xl px-4 py-3 transition-all active:scale-[.98]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125" /></svg>
                Download Excel
            </a>
        </div>
    </div>
</div>

@include('mobile.layout.menu')
</body>
</html>
