<!doctype html>
<html lang="en">
<head>
    <title>Inventory Detail – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="bg-slate-50 pb-24">

<header class="sticky top-0 z-40 bg-brand-400 shadow-md shadow-brand-400/20">
    <div class="flex items-center px-4 h-14">
        <a href="{{ route('inventory.index.mobile') }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </a>
        <h1 class="absolute left-1/2 -translate-x-1/2 text-white font-bold text-base">Inventory Detail</h1>
    </div>
</header>

<main class="px-4 pt-4 space-y-3 pb-4">
    {{-- Product Info --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" /></svg>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-medium">Purc Doc / Sales Doc</p>
                <p class="font-bold text-slate-800 text-sm">{{ request()->get('po') }} / {{ request()->get('so') }}</p>
            </div>
        </div>
        <p class="font-semibold text-slate-800 text-sm">{{ $product->material }}</p>
        <p class="text-xs text-slate-500 mt-0.5">{{ $product->po_item_desc }}</p>
        <p class="text-xs text-slate-400">{{ $product->prod_hierarchy_desc }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 text-center">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center mx-auto mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" /></svg>
            </div>
            <p class="text-[10px] text-slate-400 font-medium mb-0.5">Inventory</p>
            <p class="text-xl font-bold text-slate-800">{{ number_format($inventoryPackageItem) }}</p>
            <p class="text-xs text-emerald-600 font-semibold mt-0.5">$ {{ number_format($inventoryNominal) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 text-center">
            <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center mx-auto mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" /></svg>
            </div>
            <p class="text-[10px] text-slate-400 font-medium mb-0.5">Outbound</p>
            <p class="text-xl font-bold text-slate-800">{{ number_format($outboundDetail) }}</p>
            <p class="text-xs text-emerald-600 font-semibold mt-0.5">$ {{ number_format($outboundNominal) }}</p>
        </div>
    </div>

    {{-- Serial Numbers --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-3">
            <p class="text-xs font-bold text-slate-700 mb-2">SN Stock</p>
            @forelse($serialNumberStock as $sn)
                <div class="text-xs font-mono text-slate-700 py-0.5 border-b border-slate-50 last:border-0">{{ $sn->serial_number }}</div>
            @empty
                <p class="text-xs text-slate-400">Tidak ada SN</p>
            @endforelse
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-3">
            <p class="text-xs font-bold text-slate-700 mb-2">SN Outbound</p>
            @forelse($serialNumberOutbound as $sn)
                <div class="text-xs font-mono text-slate-700 py-0.5 border-b border-slate-50 last:border-0">{{ $sn->serial_number }}</div>
            @empty
                <p class="text-xs text-slate-400">Tidak ada SN</p>
            @endforelse
        </div>
    </div>
</main>

@include('mobile.layout.menu')
</body>
</html>
