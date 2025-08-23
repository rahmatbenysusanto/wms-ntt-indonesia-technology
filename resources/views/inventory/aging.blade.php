@extends('layout.index')
@section('title', 'Produk List')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Product Aging</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Product Aging</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Product Aging</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('inventory.aging.download-excel') }}" class="btn btn-success btn-sm">Download Report Excel</a>
                            <a href="{{ route('inventory.aging.download-excel') }}" class="btn btn-pdf btn-sm">Download Report PDF</a>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row">
                            <div class="col-2">
                                <label class="form-label">Purc Doc</label>
                                <input type="text" class="form-control" value="{{ request()->get('purcDoc', null) }}" name="purcDoc" placeholder="Purc Doc">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Sales Doc</label>
                                <input type="text" class="form-control" value="{{ request()->get('salesDoc', null) }}" name="salesDoc" placeholder="Sales Doc">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Material</label>
                                <input type="text" class="form-control" value="{{ request()->get('material', null) }}" name="material" placeholder="Material">
                            </div>
                            <div class="col-2">
                                <label class="form-label text-white">-</label>
                                <div>
                                    <button type="submit" class="btn btn-info">Search</button>
                                    <a href="{{ route('inventory.index') }}" class="btn btn-danger">Clear</a>
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
                                <th>Purc Doc</th>
                                <th>Sales Doc</th>
                                <th>Box</th>
                                <th>Material</th>
                                <th>PO Item Desc</th>
                                <th class="text-center">Stock</th>
                                <th>Storage Loc</th>
                                <th>Date In WH</th>
                                <th>Aging Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($inventory as $index => $item)
                                <tr>
                                    <td>{{ $inventory->firstItem() + $index }}</td>
                                    <td>{{ $item->purchaseOrderDetail->purchaseOrder->purc_doc }}</td>
                                    <td>{{ $item->sales_doc }}</td>
                                    <td>
                                        <div>{{ $item->inventoryPackageItem->inventoryPackage->number }}</div>
                                    </td>
                                    <td>{{ $item->purchaseOrderDetail->material }}</td>
                                    <td>{{ $item->purchaseOrderDetail->po_item_desc }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->qty) }}</td>
                                    <td>
                                        @if($item->storage->raw == '-')
                                            <span class="badge bg-danger-subtle text-danger"> Cross Docking </span>
                                        @else
                                            {{ $item->storage->raw.' - '.$item->storage->area.' - '.$item->storage->rak.' - '.$item->storage->bin }}
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->aging_date)->translatedFormat('d F Y') }}</td>
                                    <td>
                                        @php
                                            $tanggalMasuk = \Carbon\Carbon::parse($item->aging_date);
                                            $today = \Carbon\Carbon::now();
                                            $totalHari = $tanggalMasuk->diffInDays($today);

                                            if ($totalHari <= 90) {
                                                $label = '0 - 90 Hari';
                                                $class = 'bg-success-subtle text-success';
                                            } elseif ($totalHari <= 180) {
                                                $label = '91 - 180 Hari';
                                                $class = 'bg-warning-subtle text-warning';
                                            } elseif ($totalHari <= 365) {
                                                $label = '181 - 365 Hari';
                                                $class = 'bg-orange-subtle text-orange';
                                            } else {
                                                $label = '> 365 Hari';
                                                $class = 'bg-danger-subtle text-danger';
                                            }
                                        @endphp
                                        <span class="badge {{ $class }}">
                                                {{ $label }} ({{ number_format($totalHari) }} hari)
                                            </span>
                                    </td>
                                    <td><a href="{{ route('inventory.detail', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Detail</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end mt-2">
                            @if ($inventory->hasPages())
                                <ul class="pagination">
                                    @if ($inventory->onFirstPage())
                                        <li class="disabled"><span>&laquo; Previous</span></li>
                                    @else
                                        <li><a href="{{ $inventory->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                    @endif

                                    @foreach ($inventory->links()->elements as $element)
                                        @if (is_string($element))
                                            <li class="disabled"><span>{{ $element }}</span></li>
                                        @endif

                                        @if (is_array($element))
                                            @foreach ($element as $page => $url)
                                                @if ($page == $inventory->currentPage())
                                                    <li class="active"><span>{{ $page }}</span></li>
                                                @else
                                                    <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach

                                    @if ($inventory->hasMorePages())
                                        <li><a href="{{ $inventory->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
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
    </div>
@endsection
