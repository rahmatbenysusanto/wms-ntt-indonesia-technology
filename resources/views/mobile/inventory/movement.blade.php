<!doctype html>
<html lang="en">
<head>
    <title>Inventory Movement – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="bg-slate-50 pb-24">

<header class="sticky top-0 z-40 bg-brand-400 shadow-md shadow-brand-400/20">
    <div class="flex items-center px-4 h-14">
        <a href="{{ route('dashboardMobile') }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </a>
        <h1 class="absolute left-1/2 -translate-x-1/2 text-white font-bold text-base">Movement</h1>
        <button onclick="document.getElementById('filterModal').classList.remove('hidden')"
                class="flex items-center gap-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-medium px-3 py-1.5 rounded-xl transition-all ml-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" /></svg>
            Filter
            @if(request()->get('search') == 1)
                <span class="w-2 h-2 rounded-full bg-red-400"></span>
            @endif
        </button>
    </div>
</header>

<main class="px-4 pt-4 space-y-3">
    @if(request()->get('search') == 1)
        <a href="{{ url()->current() }}" class="block text-xs text-red-500 font-semibold text-right pr-1">Hapus Filter ✕</a>
    @endif

    @forelse($inventory as $inv)
        <a href="{{ route('inventory.indexDetail.movement.mobile', ['id' => $inv->id]) }}" class="block">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 tap-card
                        {{ $inv->type == 'inbound' ? 'border-l-4 border-l-emerald-400' : 'border-l-4 border-l-red-400' }}">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1 min-w-0 pr-2">
                        <p class="font-bold text-slate-800 text-sm">PO: {{ $inv->purchaseOrder?->purc_doc }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">SO: {{ $inv->purchaseOrderDetail?->sales_doc }}</p>
                    </div>
                    @if($inv->type == 'inbound')
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full badge-inbound flex-shrink-0">Inbound</span>
                    @else
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full badge-cancel flex-shrink-0">Outbound</span>
                    @endif
                </div>
                <p class="text-xs font-semibold text-slate-700">{{ $inv->purchaseOrderDetail?->material }}</p>
                <p class="text-xs text-slate-500 truncate">{{ $inv->purchaseOrderDetail?->po_item_desc }}</p>
                <div class="flex items-center gap-1.5 mt-2 text-xs text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75" /></svg>
                    {{ \Carbon\Carbon::parse($inv->created_at)->translatedFormat('d F Y H:i') }}
                </div>
            </div>
        </a>
    @empty
        <div class="text-center py-16 text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
            <p class="text-sm font-medium">Tidak ada movement</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($inventory->hasPages())
    <div class="flex items-center justify-center gap-2 pt-2 pb-2">
        @if($inventory->onFirstPage())
            <span class="px-4 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl opacity-50">‹ Back</span>
        @else
            <a href="{{ $inventory->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" class="px-4 py-2 text-xs font-semibold text-brand-500 bg-white border border-brand-200 rounded-xl hover:bg-brand-50 transition-colors">‹ Back</a>
        @endif
        <span class="px-3 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl">{{ $inventory->currentPage() }} / {{ $inventory->lastPage() }}</span>
        @if($inventory->hasMorePages())
            <a href="{{ $inventory->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" class="px-4 py-2 text-xs font-semibold text-brand-500 bg-white border border-brand-200 rounded-xl hover:bg-brand-50 transition-colors">Next ›</a>
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
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Customer</label>
                <select name="customer" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white transition-all">
                    <option value="">-- Semua Customer --</option>
                    @foreach($customer as $c)
                        <option value="{{ $c->id }}" {{ request()->get('customer') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
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
