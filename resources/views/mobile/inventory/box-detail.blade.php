<!doctype html>
<html lang="en">
<head>
    <title>Box Detail – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="bg-slate-50 pb-24">

<header class="sticky top-0 z-40 bg-slate-800 shadow-md">
    <div class="flex items-center px-4 h-14">
        <a href="{{ route('inventory.box.mobile') }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/10 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </a>
        <h1 class="absolute left-1/2 -translate-x-1/2 text-white font-bold text-base">Box Detail</h1>
    </div>
</header>

<main class="px-4 pt-4 space-y-3 pb-4">
    {{-- Box Info --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
            </div>
            <div>
                <p class="font-bold text-slate-800 text-sm">{{ $box->purchaseOrder?->purc_doc }}</p>
                <p class="text-xs text-slate-500">No: {{ $box->number }}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2 text-xs text-slate-600">
            <div><span class="text-slate-400">Reff:</span> {{ $box->reff_number }}</div>
            <div><span class="text-slate-400">Storage:</span> {{ $box->storage?->raw }}-{{ $box->storage?->area }}-{{ $box->storage?->rak }}-{{ $box->storage?->bin }}</div>
        </div>
    </div>

    {{-- Items --}}
    <h2 class="text-sm font-bold text-slate-700 px-1">List Item</h2>

    @foreach($detail as $item)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <div class="flex items-start justify-between mb-2">
                <div class="flex-1 min-w-0 pr-2">
                    <p class="text-xs text-slate-500">SO: {{ $item->purchaseOrderDetail?->sales_doc }}</p>
                    <p class="font-semibold text-slate-800 text-sm mt-0.5">{{ $item->purchaseOrderDetail?->material }}</p>
                </div>
                <div class="flex flex-col items-end gap-1.5">
                    @if($item->is_parent == 1)
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-brand-50 text-brand-600">Parent</span>
                    @else
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">Child</span>
                    @endif
                    <span class="text-xs font-bold text-slate-700">QTY: {{ $item->qty }}</span>
                </div>
            </div>
            <p class="text-xs text-slate-500 truncate">{{ $item->purchaseOrderDetail?->po_item_desc }}</p>
            <p class="text-xs text-slate-400 truncate">{{ $item->purchaseOrderDetail?->prod_hierarchy_desc }}</p>
            <p class="text-xs text-slate-500 mt-1">Item: {{ $item->purchaseOrderDetail?->item }}</p>
        </div>
    @endforeach
</main>

@include('mobile.layout.menu')
</body>
</html>
