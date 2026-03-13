<!doctype html>
<html lang="en">
<head>
    <title>Detail SO – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="bg-slate-50 pb-24">

<header class="sticky top-0 z-40 bg-brand-400 shadow-md shadow-brand-400/20">
    <div class="flex items-center px-4 h-14">
        <a href="{{ route('inbound.indexDetail.mobile', ['id' => $purchaseOrderId]) }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </a>
        <h1 class="absolute left-1/2 -translate-x-1/2 text-white font-bold text-base">Detail SO</h1>
        <button onclick="document.getElementById('downloadModal').classList.remove('hidden')"
                class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white ml-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
        </button>
    </div>
</header>

<main class="px-4 pt-4 space-y-3">
    {{-- SO Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 px-4 py-3 flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75c0-.231-.035-.454-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" /></svg>
        </div>
        <div>
            <p class="text-[10px] text-slate-400 font-medium">SO Number</p>
            <p class="font-bold text-slate-800 text-sm">{{ request()->get('so') }}</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-700 px-1">List Item</h2>
        <div class="flex items-center gap-2">
            @if(request()->get('search') == 1)
                <a href="{{ route('inbound.indexDetail.so', ['so' => request()->get('so'), 'po' => request()->get('po')]) }}"
                   class="text-xs text-red-500 font-semibold bg-red-50 px-3 py-1.5 rounded-xl hover:bg-red-100 transition-colors">
                    Hapus ✕
                </a>
            @endif
            <button onclick="document.getElementById('filterModal').classList.remove('hidden')"
                    class="flex items-center gap-1 text-xs font-semibold text-brand-500 bg-brand-50 px-3 py-1.5 rounded-xl hover:bg-brand-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" /></svg>
                Filter
            </button>
        </div>
    </div>

    @forelse($purchaseOrderDetail as $detail)
        <a href="{{ route('inbound.indexDetail.so.sn', ['so' => request()->get('so'), 'po' => request()->get('po'), 'id' => $detail->id]) }}" class="block">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 tap-card
                        {{ $detail->po_item_qty == $detail->qty_qc ? 'border-l-4 border-l-emerald-400' : 'border-l-4 border-l-amber-400' }}">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-bold text-slate-800 text-sm">{{ $detail->sales_doc }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">Item: {{ $detail->item }}</p>
                    </div>
                    @if($detail->po_item_qty == $detail->qty_qc)
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full badge-inbound">Complete</span>
                    @else
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full badge-open">Partial</span>
                    @endif
                </div>
                <p class="text-xs font-semibold text-slate-700">{{ $detail->material }}</p>
                <p class="text-xs text-slate-500 truncate">{{ $detail->po_item_desc }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $detail->prod_hierarchy_desc }}</p>
                <div class="grid grid-cols-2 gap-2 mt-3">
                    <div class="bg-slate-50 rounded-xl p-2 text-center">
                        <p class="text-[10px] text-slate-400 font-medium">QTY PO</p>
                        <p class="text-sm font-bold text-slate-700">{{ number_format($detail->po_item_qty) }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-2 text-center">
                        <p class="text-[10px] text-slate-400 font-medium">QTY QC</p>
                        <p class="text-sm font-bold text-slate-700">{{ number_format($detail->qty_qc) }}</p>
                    </div>
                </div>
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
        <h3 class="text-base font-bold text-slate-800 mb-4">Download Detail SO</h3>
        <div class="space-y-3">
            <a href="{{ route('inbound.purchase-order-download-pdf', ['id' => $purchaseOrderId]) }}"
               class="flex items-center gap-3 bg-red-50 hover:bg-red-100 text-red-700 font-semibold text-sm rounded-2xl px-4 py-3 transition-all active:scale-[.98]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                Download PDF
            </a>
            <a href="{{ route('inbound.purchase-order-download-excel', ['id' => $purchaseOrderId]) }}"
               class="flex items-center gap-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-sm rounded-2xl px-4 py-3 transition-all active:scale-[.98]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25" /></svg>
                Download Excel
            </a>
        </div>
    </div>
</div>

{{-- Filter Modal --}}
<div id="filterModal" class="hidden fixed inset-0 z-50 flex items-end">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('filterModal').classList.add('hidden')"></div>
    <div class="relative w-full bg-white rounded-t-3xl p-6 pb-28 shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold text-slate-800">Cari Item</h3>
            <button onclick="document.getElementById('filterModal').classList.add('hidden')" class="w-8 h-8 flex items-center justify-center bg-slate-100 rounded-full text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <form action="{{ url()->current() }}" method="GET" class="space-y-4">
            <input type="hidden" name="search" value="1">
            <input type="hidden" name="so" value="{{ request()->get('so') }}">
            <input type="hidden" name="po" value="{{ request()->get('po') }}">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Material</label>
                <input type="text" name="material" value="{{ request()->get('material') }}" placeholder="Cari Material..."
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
