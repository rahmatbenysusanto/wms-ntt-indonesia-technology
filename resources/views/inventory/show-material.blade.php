<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Detail - {{ $item->material }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen">
    <div class="max-w-md mx-auto py-8 px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Material Details</h1>
            <p class="text-slate-500 mt-2">Warehouse Inventory Management System</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 overflow-hidden border border-slate-100">
            <!-- Material Banner/Info -->
            <div class="bg-indigo-600 px-6 py-6 text-white">
                <span class="text-indigo-200 text-xs font-semibold uppercase tracking-wider">Material Code</span>
                <h2 class="text-2xl font-bold mt-1">{{ $item->material }}</h2>
            </div>

            <div class="p-6">
                <div class="space-y-6">
                    <!-- Detail Item -->
                    <div>
                        <span
                            class="block text-xs font-medium text-slate-400 uppercase tracking-widest mb-1">Description</span>
                        <p class="text-slate-800 font-semibold">{{ $item->po_item_desc ?? '-' }}</p>
                    </div>

                    <!-- Grid for secondary details -->
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <span
                                class="block text-xs font-medium text-slate-400 uppercase tracking-widest mb-1">Purchase
                                Doc</span>
                            <p class="text-slate-700 font-medium">{{ $item->purc_doc }}</p>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-slate-400 uppercase tracking-widest mb-1">Sales
                                Doc</span>
                            <p class="text-slate-700 font-medium">{{ $item->sales_doc }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <span
                                class="block text-xs font-medium text-slate-400 uppercase tracking-widest mb-1">Client</span>
                            <p class="text-slate-700 font-medium">{{ $item->client_name ?? '-' }}</p>
                        </div>
                        <div>
                            <span
                                class="block text-xs font-medium text-slate-400 uppercase tracking-widest mb-1">Current
                                Stock</span>
                            <div class="flex items-center mt-1">
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-bold {{ $stock > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ number_format($stock) }} Units
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <span class="block text-xs font-medium text-slate-400 uppercase tracking-widest mb-2">Available
                            Serial Numbers</span>
                        @if ($serialNumbers->count() > 0)
                            <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto pr-1">
                                @foreach ($serialNumbers as $sn)
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-md bg-slate-100 text-slate-600 text-[10px] font-semibold border border-slate-200">
                                        {{ $sn }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-slate-400 text-sm italic">No serial numbers available for this item.</p>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <span
                            class="block text-xs font-medium text-slate-400 uppercase tracking-widest mb-1">Hierarchy</span>
                        <p class="text-slate-600 text-sm leading-relaxed">{{ $item->prod_hierarchy_desc ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="bg-slate-50 px-6 py-4 flex justify-between items-center text-[10px] text-slate-400 uppercase tracking-widest border-t border-slate-100">
                <span>Ref ID: #{{ $item->id }}</span>
                <span>Last Scan: {{ date('H:i') }}</span>
            </div>
        </div>

        <!-- Back Button (Optional) -->
        <div class="mt-8 text-center">
            <p class="text-slate-400 text-xs">Generated by NTT Indonesia Technology</p>
        </div>
    </div>
</body>

</html>
