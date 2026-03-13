<!doctype html>
<html lang="en">
<head>
    <title>Aging Detail – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="bg-slate-50 pb-24">

<header class="sticky top-0 z-40 bg-brand-400 shadow-md shadow-brand-400/20">
    <div class="flex items-center px-4 h-14">
        <a href="{{ route('inventory.aging.mobile') }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </a>
        <h1 class="absolute left-1/2 -translate-x-1/2 text-white font-bold text-base">Aging {{ $text }}</h1>
        <button onclick="document.getElementById('downloadModal').classList.remove('hidden')"
                class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white ml-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
        </button>
    </div>
</header>

<main class="px-4 pt-4 space-y-3">
    <h2 class="text-sm font-bold text-slate-700 px-1">Daftar Purchase Order</h2>

    @forelse($inventoryDetail as $detail)
        <a href="{{ route('dashboard.mobile.aging.detail.list', ['type' => request()->get('type'), 'po' => $detail->purc_doc]) }}" class="block">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 tap-card border-l-4 border-l-amber-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-800 text-sm">{{ $detail->purc_doc }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $detail->customer_name }}</p>
                        <p class="text-xs text-slate-400">{{ $detail->vendor_name }}</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                </div>
            </div>
        </a>
    @empty
        <div class="text-center py-16 text-slate-400">
            <p class="text-sm font-medium">Tidak ada data aging</p>
        </div>
    @endforelse
</main>

{{-- Download Modal --}}
<div id="downloadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-5">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('downloadModal').classList.add('hidden')"></div>
    <div class="relative w-full max-w-xs bg-white rounded-3xl p-6 shadow-2xl">
        <h3 class="text-base font-bold text-slate-800 mb-1">Download Aging {{ $text }}</h3>
        <div class="space-y-3 mt-4">
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
