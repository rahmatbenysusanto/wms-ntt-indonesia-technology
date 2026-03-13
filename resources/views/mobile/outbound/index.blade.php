<!doctype html>
<html lang="en">
<head>
    <title>Outbound – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="bg-slate-50 pb-24">

<header class="sticky top-0 z-40 bg-brand-400 shadow-md shadow-brand-400/20">
    <div class="flex items-center px-4 h-14">
        <a href="{{ route('dashboardMobile') }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </a>
        <h1 class="absolute left-1/2 -translate-x-1/2 text-white font-bold text-base tracking-wide">Outbound</h1>
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
        <a href="{{ url()->current() }}" class="block text-xs text-red-500 font-semibold text-right pr-1 hover:text-red-700 transition-colors">Hapus Filter ✕</a>
    @endif

    @forelse($outbound as $item)
        <a href="{{ route('outbound.indexDetail.mobile', ['id' => $item->id]) }}" class="block">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 tap-card border-l-4 border-l-violet-400">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-bold text-slate-800 text-sm">{{ $item->delivery_note_number ?? $item->purc_doc }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">SO: {{ $item->sales_docs }}</p>
                    </div>
                    @if($item->status == 'outbound')
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full badge-outbound flex-shrink-0">Outbound</span>
                    @else
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full badge-return flex-shrink-0">Return</span>
                    @endif
                </div>
                <div class="grid grid-cols-3 gap-2 mt-3">
                    <div class="bg-slate-50 rounded-xl p-2 text-center">
                        <p class="text-[10px] text-slate-400 font-medium">QTY</p>
                        <p class="text-sm font-bold text-slate-700">{{ number_format($item->qty) }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-2 text-center">
                        <p class="text-[10px] text-slate-400 font-medium">Nominal</p>
                        <p class="text-xs font-bold text-slate-700 leading-tight">$ {{ number_format($item->nominal) }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-2 text-center">
                        <p class="text-[10px] text-slate-400 font-medium">Tanggal</p>
                        <p class="text-xs font-bold text-slate-700 leading-tight">{{ \Carbon\Carbon::parse($item->delivery_date)->format('d M Y') }}</p>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-1 text-xs text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                    {{ $item->deliv_dest ?? '-' }}
                </div>
            </div>
        </a>
    @empty
        <div class="text-center py-16 text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
            <p class="text-sm font-medium">Tidak ada data outbound</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($outbound->hasPages())
    <div class="flex items-center justify-center gap-2 pt-2 pb-2">
        @if($outbound->onFirstPage())
            <span class="px-4 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl opacity-50 cursor-not-allowed">‹ Back</span>
        @else
            <a href="{{ $outbound->previousPageUrl() }}&per_page={{ request('per_page', 5) }}" class="px-4 py-2 text-xs font-semibold text-brand-500 bg-white border border-brand-200 rounded-xl hover:bg-brand-50 transition-colors">‹ Back</a>
        @endif

        <span class="px-3 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl">
            {{ $outbound->currentPage() }} / {{ $outbound->lastPage() }}
        </span>

        @if($outbound->hasMorePages())
            <a href="{{ $outbound->nextPageUrl() }}&per_page={{ request('per_page', 5) }}" class="px-4 py-2 text-xs font-semibold text-brand-500 bg-white border border-brand-200 rounded-xl hover:bg-brand-50 transition-colors">Next ›</a>
        @else
            <span class="px-4 py-2 text-xs font-semibold text-slate-400 bg-white border border-slate-200 rounded-xl opacity-50 cursor-not-allowed">Next ›</span>
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
