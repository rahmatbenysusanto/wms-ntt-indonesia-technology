<!doctype html>
<html lang="en">
<head>
    <title>General Room – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="bg-slate-50 pb-24">

<header class="sticky top-0 z-40 bg-brand-400 shadow-md shadow-brand-400/20">
    <div class="flex items-center px-4 h-14">
        <a href="{{ route('dashboardMobile') }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </a>
        <h1 class="absolute left-1/2 -translate-x-1/2 text-white font-bold text-base">General Room</h1>
        <button onclick="document.getElementById('filterModal').classList.remove('hidden')"
                class="flex items-center gap-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-medium px-3 py-1.5 rounded-xl transition-all ml-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" /></svg>
            Filter
        </button>
    </div>
</header>

<main class="px-4 pt-4 space-y-3">
    @if(request()->get('search') == 1)
        <a href="{{ url()->current() }}" class="block text-xs text-red-500 font-semibold text-right pr-1">Hapus Filter ✕</a>
    @endif

    @forelse($inventory as $index => $inv)
        <a href="{{ route('gr.indexDetail.mobile', ['po' => $inv->purc_doc, 'so' => $inv->sales_doc, 'id' => $inv->product_id]) }}" class="block">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 tap-card border-l-4 border-l-teal-400">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1 min-w-0 pr-2">
                        <p class="font-bold text-slate-800 text-sm">{{ $inv->purc_doc }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">SO: {{ $inv->sales_doc }}</p>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-teal-100 text-teal-700 flex-shrink-0">{{ number_format($inv->qty) }}</span>
                </div>
                <p class="text-xs font-semibold text-slate-700">{{ $inv->material }}</p>
                <p class="text-xs text-slate-500 truncate">{{ $inv->po_item_desc }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $inv->prod_hierarchy_desc }}</p>
                <p class="text-xs text-slate-500 mt-1 font-medium">Client: {{ $inv->client_name ?? '-' }}</p>
            </div>
        </a>
    @empty
        <div class="text-center py-16 text-slate-400">
            <p class="text-sm font-medium">Tidak ada data general room</p>
        </div>
    @endforelse

    @if($inventory->hasPages())
    <div class="flex items-center justify-center gap-2 pt-2 pb-2">
        @if($inventory->onFirstPage())
            <span class="px-4 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl opacity-50">‹ Back</span>
        @else
            <a href="{{ $inventory->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" class="px-4 py-2 text-xs font-semibold text-brand-500 bg-white border border-brand-200 rounded-xl">‹ Back</a>
        @endif
        <span class="px-3 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl">{{ $inventory->currentPage() }} / {{ $inventory->lastPage() }}</span>
        @if($inventory->hasMorePages())
            <a href="{{ $inventory->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" class="px-4 py-2 text-xs font-semibold text-brand-500 bg-white border border-brand-200 rounded-xl">Next ›</a>
        @else
            <span class="px-4 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl opacity-50">Next ›</span>
        @endif
    </div>
    @endif
</main>

{{-- Filter Modal --}}
<div id="filterModal" class="hidden fixed inset-0 z-50 flex items-end">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('filterModal').classList.add('hidden')"></div>
    <div class="relative w-full bg-white rounded-t-3xl p-6 pb-28 shadow-2xl max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold text-slate-800">Cari & Filter</h3>
            <button onclick="document.getElementById('filterModal').classList.add('hidden')" class="w-8 h-8 flex items-center justify-center bg-slate-100 rounded-full text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <form action="{{ url()->current() }}" method="GET" class="space-y-4">
            <input type="hidden" name="search" value="1">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Purc Doc</label>
                <input type="text" name="purcDoc" value="{{ request()->get('purcDoc') }}" placeholder="Cari Purc Doc..."
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Sales Doc</label>
                <input type="text" name="salesDoc" value="{{ request()->get('salesDoc') }}" placeholder="Cari Sales Doc..."
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white transition-all">
            </div>
            <button type="submit" class="w-full py-3 bg-brand-400 hover:bg-brand-500 text-white font-semibold text-sm rounded-xl shadow-lg shadow-brand-400/30 transition-all duration-200 active:scale-[.98]">
                Terapkan Filter
            </button>
        </form>
    </div>
</div>

@include('mobile.layout.menu')
</body>
</html>
