<!doctype html>
<html lang="en">

<head>
    <title>Dashboard – WMS Mobile</title>
    @include('mobile.layout.app')
</head>

<body class="bg-slate-50 min-h-screen">

    {{-- Header Hero --}}
    <div class="bg-gradient-to-br from-brand-500 via-brand-400 to-teal-500 px-5 pt-10 pb-6 relative">
        {{-- Decorative circles --}}
        <div class="absolute -top-8 -right-8 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="absolute top-10 -right-4 w-24 h-24 bg-white/10 rounded-full"></div>

        <div class="relative z-10">
            <div class="flex items-center justify-between mb-1">
                <div>
                    <p class="text-white/70 text-xs font-medium">Selamat Datang 👋</p>
                    <h1 class="text-white font-bold text-xl">{{ auth()->user()->name }}</h1>
                </div>
                <a href="{{ route('logout') }}"
                    class="flex items-center gap-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-semibold px-3 py-2 rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                    Logout
                </a>
            </div>
            <p class="text-white/60 text-xs mt-1">WMS – Trans Kargo Solusindo</p>
        </div>
    </div>

    {{-- Menu Grid --}}
    <div class="px-4 pt-4 pb-6 space-y-3">
        @php
            $menus = [
                [
                    'menu' => 'Mobile Inbound',
                    'route' => 'inbound.index.mobile',
                    'label' => 'Inbound',
                    'icon' => 'download',
                    'color' => 'brand',
                ],
                [
                    'menu' => 'Mobile Outbound',
                    'route' => 'outbound.index.mobile',
                    'label' => 'Outbound',
                    'icon' => 'upload',
                    'color' => 'violet',
                ],
                [
                    'menu' => 'Mobile Inventory',
                    'route' => 'inventory.index.mobile',
                    'label' => 'Inventory',
                    'icon' => 'package',
                    'color' => 'indigo',
                ],
                [
                    'menu' => 'Mobile Inventory',
                    'route' => 'inventory.index.movement.mobile',
                    'label' => 'Movement',
                    'icon' => 'trending-up',
                    'color' => 'sky',
                ],
                [
                    'menu' => 'Mobile Aging',
                    'route' => 'inventory.aging.mobile',
                    'label' => 'Aging',
                    'icon' => 'clock',
                    'color' => 'amber',
                ],
                [
                    'menu' => 'Mobile General Room',
                    'route' => 'gr.index.mobile',
                    'label' => 'General Room',
                    'icon' => 'layers',
                    'color' => 'teal',
                ],
            ];

            $colorMap = [
                'brand' => ['bg' => 'bg-brand-100', 'icon' => 'text-brand-600', 'card' => 'border-brand-200'],
                'violet' => ['bg' => 'bg-violet-100', 'icon' => 'text-violet-600', 'card' => 'border-violet-200'],
                'indigo' => ['bg' => 'bg-indigo-100', 'icon' => 'text-indigo-600', 'card' => 'border-indigo-200'],
                'sky' => ['bg' => 'bg-sky-100', 'icon' => 'text-sky-600', 'card' => 'border-sky-200'],
                'amber' => ['bg' => 'bg-amber-100', 'icon' => 'text-amber-600', 'card' => 'border-amber-200'],
                'teal' => ['bg' => 'bg-teal-100', 'icon' => 'text-teal-600', 'card' => 'border-teal-200'],
            ];

            $iconMap = [
                'download' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7M12 16V3M5 21h14" />',
                'upload' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7M12 8v13M5 3h14" />',
                'package' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />',
                'trending-up' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />',
                'clock' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />',
                'layers' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3" />',
            ];

            $shown = [];
        @endphp

        <div class="grid grid-cols-2 gap-3">
            @foreach ($menus as $m)
                @if (Session::get('userHasMenu') && Session::get('userHasMenu')->contains($m['menu']) && !in_array($m['route'], $shown))
                    @php
                        $shown[] = $m['route'];
                        $c = $colorMap[$m['color']];
                    @endphp
                    <a href="{{ route($m['route']) }}" class="block">
                        <div
                            class="bg-white rounded-2xl shadow-sm border {{ $c['card'] }} border p-5 tap-card text-center">
                            <div
                                class="w-12 h-12 rounded-2xl {{ $c['bg'] }} flex items-center justify-center mx-auto mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 {{ $c['icon'] }}"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    {!! $iconMap[$m['icon']] !!}
                                </svg>
                            </div>
                            <p class="font-bold text-slate-800 text-sm">{{ $m['label'] }}</p>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>

        {{-- Inventory Box shortcut if menu accessible --}}
        @if (Session::get('userHasMenu') && Session::get('userHasMenu')->contains('Mobile Inventory'))
            <a href="{{ route('inventory.box.mobile') }}" class="block">
                <div
                    class="bg-gradient-to-r from-slate-700 to-slate-800 rounded-2xl shadow-sm p-4 tap-card flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Box Inventory</p>
                        <p class="text-white/60 text-xs">Kelola data box storage</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white/40 ml-auto flex-shrink-0"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </div>
            </a>
        @endif
    </div>

    @include('mobile.layout.menu')
</body>

</html>
