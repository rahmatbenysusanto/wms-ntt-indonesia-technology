<!doctype html>
<html lang="en">
<head>
    <title>Box – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="bg-slate-50 pb-24">

<header class="sticky top-0 z-40 bg-slate-800 shadow-md">
    <div class="flex items-center px-4 h-14">
        <a href="{{ route('dashboardMobile') }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/10 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </a>
        <h1 class="absolute left-1/2 -translate-x-1/2 text-white font-bold text-base">Box Inventory</h1>
    </div>
</header>

<main class="px-4 pt-4 space-y-3">
    {{-- Search --}}
    <form action="{{ url()->current() }}" method="GET">
        <div class="flex gap-2">
            <input type="number" name="search" value="{{ request()->get('search') }}" placeholder="Cari nomor box..."
                   class="flex-1 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:border-brand-400 transition-all shadow-sm">
            <button type="submit" class="px-4 py-2.5 bg-brand-400 hover:bg-brand-500 text-white rounded-xl text-sm font-semibold shadow-sm transition-all active:scale-[.98]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
            </button>
        </div>
    </form>

    @forelse($box as $inv)
        <a href="{{ route('inventory.box.detail.mobile', ['id' => $inv->id]) }}" class="block">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 tap-card border-l-4 border-l-slate-600">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-bold text-slate-800 text-sm">{{ $inv->purchaseOrder?->purc_doc }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">No: {{ $inv->number }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">QTY: {{ number_format($inv->qty) }}</span>
                    </div>
                </div>
                <p class="text-xs text-slate-500">Reff: {{ $inv->reff_number }}</p>
                <p class="text-xs text-slate-400 mt-1">Storage: {{ $inv->storage?->raw }} - {{ $inv->storage?->area }} - {{ $inv->storage?->rak }} - {{ $inv->storage?->bin }}</p>
            </div>
        </a>
    @empty
        <div class="text-center py-16 text-slate-400">
            <p class="text-sm font-medium">Tidak ada data box</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($box->hasPages())
    <div class="flex items-center justify-center gap-2 pt-2 pb-2">
        @if($box->onFirstPage())
            <span class="px-4 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl opacity-50">‹ Back</span>
        @else
            <a href="{{ $box->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" class="px-4 py-2 text-xs font-semibold text-brand-500 bg-white border border-brand-200 rounded-xl">‹ Back</a>
        @endif
        <span class="px-3 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl">{{ $box->currentPage() }} / {{ $box->lastPage() }}</span>
        @if($box->hasMorePages())
            <a href="{{ $box->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" class="px-4 py-2 text-xs font-semibold text-brand-500 bg-white border border-brand-200 rounded-xl">Next ›</a>
        @else
            <span class="px-4 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl opacity-50">Next ›</span>
        @endif
    </div>
    @endif
</main>

@include('mobile.layout.menu')
</body>
</html>
