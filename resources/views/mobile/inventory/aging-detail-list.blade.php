<!doctype html>
<html lang="en">
<head>
    <title>Aging Detail List – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="bg-slate-50 pb-24">

<header class="sticky top-0 z-40 bg-brand-400 shadow-md shadow-brand-400/20">
    <div class="flex items-center px-4 h-14">
        <a href="{{ route('dashboard.mobile.aging.detail', ['type' => request()->get('type')]) }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </a>
        <h1 class="absolute left-1/2 -translate-x-1/2 text-white font-bold text-base truncate px-16 text-center">Aging {{ $text }}</h1>
        <button onclick="document.getElementById('downloadModal').classList.remove('hidden')"
                class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white ml-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
        </button>
    </div>
</header>

<main class="px-4 pt-4 space-y-3">
    @forelse($inventoryDetail as $detail)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 border-l-4 border-l-amber-400">
            <div class="flex items-start justify-between mb-2">
                <div class="flex-1 min-w-0 pr-2">
                    <p class="font-bold text-slate-800 text-sm">{{ $detail->purc_doc }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">SO: {{ $detail->sales_doc }}</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-amber-100 text-amber-700 flex-shrink-0">{{ number_format($detail->qty) }}</span>
            </div>
            <p class="text-xs font-semibold text-slate-700">{{ $detail->material }}</p>
            <p class="text-xs text-slate-500 truncate">{{ $detail->po_item_desc }}</p>
            <p class="text-xs text-slate-400 truncate">{{ $detail->prod_hierarchy_desc }}</p>
            <p class="text-xs font-bold text-emerald-600 mt-2">$ {{ number_format($detail->total) }}</p>
        </div>
    @empty
        <div class="text-center py-16 text-slate-400">
            <p class="text-sm font-medium">Tidak ada data</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if(method_exists($inventoryDetail, 'hasPages') && $inventoryDetail->hasPages())
    <div class="flex items-center justify-center gap-2 pt-2 pb-2">
        @if($inventoryDetail->onFirstPage())
            <span class="px-4 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl opacity-50">‹ Back</span>
        @else
            <a href="{{ $inventoryDetail->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" class="px-4 py-2 text-xs font-semibold text-brand-500 bg-white border border-brand-200 rounded-xl">‹ Back</a>
        @endif
        <span class="px-3 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl">{{ $inventoryDetail->currentPage() }} / {{ $inventoryDetail->lastPage() }}</span>
        @if($inventoryDetail->hasMorePages())
            <a href="{{ $inventoryDetail->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" class="px-4 py-2 text-xs font-semibold text-brand-500 bg-white border border-brand-200 rounded-xl">Next ›</a>
        @else
            <span class="px-4 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl opacity-50">Next ›</span>
        @endif
    </div>
    @endif
</main>

{{-- Download Modal --}}
<div id="downloadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-5">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('downloadModal').classList.add('hidden')"></div>
    <div class="relative w-full max-w-xs bg-white rounded-3xl p-6 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 mb-4">Download Aging {{ $text }}</h3>
        <div class="space-y-3">
            <a href="{{ route('inventory.aging.detail.pdf', ['type' => $type]) }}"
               class="flex items-center gap-3 bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-sm rounded-2xl px-4 py-3 transition-all active:scale-[.98]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                Download PDF
            </a>
            <a href="{{ route('inventory.aging.detail.excel', ['type' => $type]) }}"
               class="flex items-center gap-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-sm rounded-2xl px-4 py-3 transition-all active:scale-[.98]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125" /></svg>
                Download Excel
            </a>
        </div>
    </div>
</div>

@include('mobile.layout.menu')
</body>
</html>
