<!doctype html>
<html lang="en">
<head>
    <title>SN Outbound – WMS Mobile</title>
    @include('mobile.layout.app')
</head>
<body class="bg-slate-50 pb-24">

<header class="sticky top-0 z-40 bg-brand-400 shadow-md shadow-brand-400/20">
    <div class="flex items-center px-4 h-14">
        <a href="{{ route('outbound.indexDetail.mobile', ['id' => $outboundId]) }}"
           class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/20 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
        </a>
        <h1 class="absolute left-1/2 -translate-x-1/2 text-white font-bold text-base">Serial Numbers</h1>
    </div>
</header>

<main class="px-4 pt-4 space-y-2">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-bold text-slate-700">List Serial Number</h2>
        <span class="text-xs text-slate-500 bg-white border border-slate-200 px-2 py-0.5 rounded-full font-medium">{{ $serialNumber->count() }} item</span>
    </div>

    @forelse($serialNumber as $detail)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 px-4 py-3 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" /></svg>
            </div>
            <p class="font-semibold text-slate-800 text-sm font-mono tracking-wide">{{ $detail->serial_number }}</p>
        </div>
    @empty
        <div class="text-center py-16 text-slate-400">
            <p class="text-sm font-medium">Tidak ada serial number</p>
        </div>
    @endforelse
</main>

@include('mobile.layout.menu')
</body>
</html>
