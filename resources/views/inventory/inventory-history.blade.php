@extends('layout.index')
@section('title', 'Inventory History')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Inventory History</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Inventory History</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Inventory History (Outbound)</h4>
                    </div>
                </div>
                <div class="card-header">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row">
                            <div class="col-md-2">
                                <label class="form-label">Purc Doc</label>
                                <input type="text" class="form-control" name="purcDoc"
                                    value="{{ request()->get('purcDoc') }}" placeholder="Purc Doc ...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sales Doc</label>
                                <input type="text" class="form-control" name="salesDoc"
                                    value="{{ request()->get('salesDoc') }}" placeholder="Sales Doc ...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Material</label>
                                <select name="material" class="form-control select2">
                                    <option value="">-- Select Material --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                            {{ request()->get('material') == $product->id ? 'selected' : '' }}>
                                            {{ $product->material }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Serial Number</label>
                                <input type="text" class="form-control" name="serialNumber"
                                    value="{{ request()->get('serialNumber') }}" placeholder="Serial Number ...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date Range</label>
                                <input type="text" class="form-control" name="date_range" id="date_range"
                                    value="{{ request()->get('date_range', $startDate . ($startDate != $endDate ? ' to ' . $endDate : '')) }}"
                                    placeholder="Select date range...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-white d-block">-</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <a href="{{ route('inventory.history') }}" class="btn btn-danger">Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th>Material</th>
                                    <th class="text-center">QTY</th>
                                    <th>Serial Number</th>
                                    <th>Previous Storage</th>
                                    <th>Outbound Number</th>
                                    <th>Date Outbound</th>
                                    <th class="text-center">History</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($history as $index => $item)
                                    <tr>
                                        <td>{{ $history->firstItem() + $index }}</td>
                                        <td>{{ $item->purchaseOrder->customer->name ?? '-' }}</td>
                                        <td>
                                            {{ $item->purchaseOrder->purc_doc }}
                                            @if ($item->inventoryPackageItem?->inventoryPackage?->storage?->id == 1)
                                                <br>
                                                <span class="badge bg-danger">Cross Docking</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->purchaseOrderDetail->sales_doc }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $item->purchaseOrderDetail->material }}</div>
                                            <div class="small text-muted">{{ $item->purchaseOrderDetail->po_item_desc }}</div>
                                        </td>
                                        <td class="text-center fw-bold">{{ number_format($item->qty) }}</td>
                                        <td>
                                            @php
                                                $serials = json_decode($item->serial_number, true);
                                            @endphp
                                            @if($serials)
                                                @foreach($serials as $sn)
                                                    <span class="badge bg-light text-dark border">{{ $sn }}</span>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if (in_array($item->inventoryPackageItem?->inventoryPackage?->storage?->id ?? null, [2, 3, 4]))
                                                <span class="badge bg-info-subtle text-info">{{ $item->inventoryPackageItem?->inventoryPackage?->storage?->raw ?? '-' }}</span>
                                            @elseif(($item->inventoryPackageItem?->inventoryPackage?->storage?->id ?? null) == 1)
                                                <span class="badge bg-warning-subtle text-warning">Direct Outbound</span>
                                            @else
                                                <div class="small">
                                                    @if($item->inventoryPackageItem?->inventoryPackage?->storage)
                                                        <b>{{ $item->inventoryPackageItem?->inventoryPackage?->storage?->raw }}</b>-<b>{{ $item->inventoryPackageItem?->inventoryPackage?->storage?->area }}</b>-<b>{{ $item->inventoryPackageItem?->inventoryPackage?->storage?->rak }}</b>-<b>{{ $item->inventoryPackageItem?->inventoryPackage?->storage?->bin }}</b>
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('outbound.detail', ['id' => $item->outbound_id]) }}" class="text-primary fw-medium">
                                                {{ $item->outbound->number ?? '-' }}
                                            </a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('inventory.cycle-count-detail', ['id' => $item->id]) }}"
                                                class="btn btn-soft-secondary btn-sm">View SN</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">No history found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        @if ($history->hasPages())
                            <ul class="pagination">
                                @if ($history->onFirstPage())
                                    <li class="disabled"><span>&laquo; Previous</span></li>
                                @else
                                    <li><a href="{{ $history->previousPageUrl() }}"
                                            rel="prev">&laquo; Previous</a></li>
                                @endif

                                @foreach ($history->links()->elements as $element)
                                    @if (is_string($element))
                                        <li class="disabled"><span>{{ $element }}</span></li>
                                    @endif

                                    @if (is_array($element))
                                        @foreach ($element as $page => $url)
                                            @if ($page == $history->currentPage())
                                                <li class="active"><span>{{ $page }}</span></li>
                                            @else
                                                <li><a
                                                        href="{{ $url }}">{{ $page }}</a>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @if ($history->hasMorePages())
                                    <li><a href="{{ $history->nextPageUrl() }}"
                                            rel="next">Next &raquo;</a></li>
                                @else
                                    <li class="disabled"><span>Next &raquo;</span></li>
                                @endif
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "-- Select Material --",
                allowClear: true,
                width: '100%'
            });

            flatpickr("#date_range", {
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: "{{ request()->get('date_range') }}"
            });
        });
    </script>
@endsection
