<!doctype html>
<html lang="en">
<head>
    <title>Movement Detail – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="bg-slate-50 pb-24">

<header class="sticky top-0 z-40 bg-brand-400 shadow-md shadow-brand-400/20">
    <div class="flex items-center px-4 h-14">
        <a href="{{ route('inventory.index.movement.mobile') }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </a>
        <h1 class="absolute left-1/2 -translate-x-1/2 text-white font-bold text-base">Movement Detail</h1>
    </div>
</header>

<main class="px-4 pt-4 space-y-3 pb-4">
    {{-- Product Info --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 font-medium">Purc Doc / Sales Doc</p>
                <p class="font-bold text-slate-800 text-sm">{{ $cycleCount->purchaseOrder->purc_doc }} / {{ $cycleCount->purchaseOrderDetail->sales_doc }}</p>
            </div>
        </div>
        <p class="font-semibold text-slate-800 text-sm">{{ $cycleCount->purchaseOrderDetail->material }}</p>
        <p class="text-xs text-slate-500 mt-0.5">{{ $cycleCount->purchaseOrderDetail->po_item_desc }}</p>
        <p class="text-xs text-slate-400">{{ $cycleCount->purchaseOrderDetail->prod_hierarchy_desc }}</p>
    </div>

    {{-- Serial Numbers --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <p class="text-xs font-bold text-slate-700 mb-3">Serial Numbers</p>
        @php $serials = json_decode($cycleCount->serial_number) ?? []; @endphp
        @forelse($serials as $sn)
            <div class="flex items-center gap-2 py-2 border-b border-slate-50 last:border-0">
                <div class="w-6 h-6 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5z" /></svg>
                </div>
                <p class="text-sm font-mono text-slate-800 font-semibold">{{ $sn }}</p>
            </div>
        @empty
            <p class="text-xs text-slate-400">Tidak ada serial number</p>
        @endforelse
    </div>
</main>

@include('mobile.layout.menu')
</body>
</html>
