{{-- Bottom Navigation Bar --}}
<nav class="bottom-nav fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-slate-100 shadow-lg">
    <div class="flex items-center justify-around py-2 px-1">
        {{-- Inbound --}}
        <a href="{{ route('inbound.index.mobile') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-xl transition-all duration-200
                  {{ request()->routeIs('inbound.*') ? 'text-brand-500' : 'text-slate-400 hover:text-brand-400' }}">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v13M5 21h14" />
                </svg>
                @if(request()->routeIs('inbound.*'))
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-brand-400 rounded-full"></span>
                @endif
            </div>
            <span class="text-[10px] font-semibold tracking-wide">Inbound</span>
        </a>

        {{-- Inventory --}}
        <a href="{{ route('inventory.index.mobile') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-xl transition-all duration-200
                  {{ request()->routeIs('inventory.index.*') ? 'text-brand-500' : 'text-slate-400 hover:text-brand-400' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
            </svg>
            <span class="text-[10px] font-semibold tracking-wide">Inventory</span>
        </a>

        {{-- Aging --}}
        <a href="{{ route('inventory.aging.mobile') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-xl transition-all duration-200
                  {{ request()->routeIs('inventory.aging.*') || request()->routeIs('dashboard.mobile.aging.*') ? 'text-brand-500' : 'text-slate-400 hover:text-brand-400' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-[10px] font-semibold tracking-wide">Aging</span>
        </a>

        {{-- Outbound --}}
        <a href="{{ route('outbound.index.mobile') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-xl transition-all duration-200
                  {{ request()->routeIs('outbound.*') ? 'text-brand-500' : 'text-slate-400 hover:text-brand-400' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21V8M5 3h14" />
            </svg>
            <span class="text-[10px] font-semibold tracking-wide">Outbound</span>
        </a>

        {{-- Movement --}}
        <a href="{{ route('inventory.index.movement.mobile') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-xl transition-all duration-200
                  {{ request()->routeIs('inventory.index.movement.*') || request()->routeIs('inventory.indexDetail.movement.*') ? 'text-brand-500' : 'text-slate-400 hover:text-brand-400' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <span class="text-[10px] font-semibold tracking-wide">Movement</span>
        </a>
    </div>
</nav>

{{-- Keep jQuery & Bootstrap for modal & select2 support --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
