<!doctype html>
<html lang="en">
<head>
    <title>Inventory – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="bg-slate-50 pb-24">

<header class="sticky top-0 z-40 bg-brand-400 shadow-md shadow-brand-400/20">
    <div class="flex items-center px-4 h-14">
        <a href="{{ route('dashboardMobile') }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </a>
        <h1 class="absolute left-1/2 -translate-x-1/2 text-white font-bold text-base">Inventory</h1>
        <div class="flex items-center gap-2 ml-auto">
            <button onclick="document.getElementById('downloadModal').classList.remove('hidden')"
                    class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
            </button>
            <button onclick="document.getElementById('filterModal').classList.remove('hidden')"
                    class="flex items-center gap-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-medium px-3 py-1.5 rounded-xl transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" /></svg>
                Filter
                @if(request()->get('search') == 1)
                    <span class="w-2 h-2 rounded-full bg-red-400"></span>
                @endif
            </button>
        </div>
    </div>
</header>

<main class="px-4 pt-4 space-y-3">
    @if(request()->get('search') == 1)
        <a href="{{ url()->current() }}" class="block text-xs text-red-500 font-semibold text-right pr-1">Hapus Filter ✕</a>
    @endif

    @forelse($inventory as $inv)
        <a href="{{ route('inventory.indexDetail.mobile', ['po' => $inv->purc_doc, 'so' => $inv->sales_doc, 'id' => $inv->product_id]) }}" class="block">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 tap-card border-l-4 border-l-indigo-400">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1 min-w-0 pr-2">
                        <div class="flex items-center gap-2">
                            <p class="font-bold text-slate-800 text-sm">{{ $inv->purc_doc }}</p>
                            @if($inv->is_parent == 1)
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-brand-50 text-brand-600">Parent</span>
                            @else
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-500">Child</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">SO: {{ $inv->sales_doc }}</p>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 flex-shrink-0">{{ number_format($inv->qty) }}</span>
                </div>
                <p class="text-xs font-semibold text-slate-700">{{ $inv->material }}</p>
                <p class="text-xs text-slate-500 truncate">{{ $inv->po_item_desc }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $inv->prod_hierarchy_desc }}</p>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-xs text-slate-500 font-medium">Client: {{ $inv->client_name ?? '-' }}</p>
                    <p class="text-xs font-bold text-emerald-600">$ {{ number_format($inv->nominal) }}</p>
                </div>
            </div>
        </a>
    @empty
        <div class="text-center py-16 text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" /></svg>
            <p class="text-sm font-medium">Tidak ada data inventory</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($inventory->hasPages())
    <div class="flex items-center justify-center gap-2 pt-2 pb-2">
        @if($inventory->onFirstPage())
            <span class="px-4 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl opacity-50 cursor-not-allowed">‹ Back</span>
        @else
            <a href="{{ $inventory->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" class="px-4 py-2 text-xs font-semibold text-brand-500 bg-white border border-brand-200 rounded-xl hover:bg-brand-50 transition-colors">‹ Back</a>
        @endif
        <span class="px-3 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl">{{ $inventory->currentPage() }} / {{ $inventory->lastPage() }}</span>
        @if($inventory->hasMorePages())
            <a href="{{ $inventory->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" class="px-4 py-2 text-xs font-semibold text-brand-500 bg-white border border-brand-200 rounded-xl hover:bg-brand-50 transition-colors">Next ›</a>
        @else
            <span class="px-4 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl opacity-50 cursor-not-allowed">Next ›</span>
        @endif
    </div>
    @endif
</main>

{{-- Download Modal --}}
<div id="downloadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-5">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('downloadModal').classList.add('hidden')"></div>
    <div class="relative w-full max-w-xs bg-white rounded-3xl p-6 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 mb-4">Download Laporan Inventory</h3>
        <div class="space-y-3">
            <a href="{{ route('inventory.download-pdf') }}"
               class="flex items-center gap-3 bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-sm rounded-2xl px-4 py-3 transition-all active:scale-[.98]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                Download PDF
            </a>
            <a href="{{ route('inventory.download-excel') }}"
               class="flex items-center gap-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-sm rounded-2xl px-4 py-3 transition-all active:scale-[.98]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625" /></svg>
                Download Excel
            </a>
        </div>
    </div>
</div>

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
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Customer</label>
                <select name="customer" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white transition-all">
                    <option value="">-- Semua Customer --</option>
                    @foreach($customer as $c)
                        <option {{ request()->get('customer') == $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Material</label>
                <select name="material" class="select2 w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white transition-all">
                    <option value="">-- Semua Material --</option>
                    @foreach($products as $p)
                        <option {{ request()->get('material') == $p->material ? 'selected' : '' }}>{{ $p->material }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full py-3 bg-brand-400 hover:bg-brand-500 text-white font-semibold text-sm rounded-xl shadow-lg shadow-brand-400/30 transition-all duration-200 active:scale-[.98]">
                Terapkan Filter
            </button>
        </form>
    </div>
</div>

@include('mobile.layout.menu')
<script>
    $(document).ready(function() { $('.select2').select2({ placeholder: "-- Semua Material --", allowClear: true, width: '100%' }); });
</script>
</body>
</html>
