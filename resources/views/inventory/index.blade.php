@extends('layout.index')
@section('title', 'Produk List')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Product List</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Product List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Product List</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('inventory.download-excel') }}" class="btn btn-success btn-sm">Download
                                Excel</a>
                            <a href="{{ route('inventory.download-pdf') }}" class="btn btn-pdf btn-sm"
                                target="_blank">Download PDF</a>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row">
                            <div class="col-2">
                                <label class="form-label">Purc Doc</label>
                                <input type="text" class="form-control" value="{{ request()->get('purcDoc', null) }}"
                                    name="purcDoc" placeholder="Purc Doc">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Sales Doc</label>
                                <input type="text" class="form-control" value="{{ request()->get('salesDoc', null) }}"
                                    name="salesDoc" placeholder="Sales Doc">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Material</label>
                                <select name="material" class="form-control select2">
                                    <option value="">-- Select Material --</option>
                                    @foreach ($products as $product)
                                        <option {{ request()->get('material') == $product->material ? 'selected' : '' }}>
                                            {{ $product->material }}</option>
                                    @endforeach
                                </select>
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
                                    <th>Client</th>
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th class="text-center">Item</th>
                                    <th>Material</th>
                                    <th>PO Item Desc</th>
                                    <th class="text-center">Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventory as $index => $item)
                                    <tr>
                                        <td>{{ $inventory->firstItem() + $index }}</td>
                                        <td>{{ $item->client }}</td>
                                        <td>{{ $item->purc_doc }}</td>
                                        <td>{{ $item->sales_doc }}</td>
                                        <td class="text-center">{{ $item->purchaseOrderDetail->item }}</td>
                                        <td>{{ $item->material }}</td>
                                        <td>{{ $item->po_item_desc }}</td>
                                        <td class="text-center fw-bold">{{ number_format($item->qty) }}</td>
                                        <td>
                                            <a href="{{ route('inventory.indexDetail', ['salesDoc' => $item->sales_doc, 'id' => $item->product_id]) }}"
                                                class="btn btn-info btn-sm">Detail</a>
                                        </td>
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
                                        <li><a href="{{ $inventory->previousPageUrl() }}&per_page={{ request('per_page', 10) }}"
                                                rel="prev">&laquo; Previous</a></li>
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
                                                    <li><a
                                                            href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach

                                    @if ($inventory->hasMorePages())
                                        <li><a href="{{ $inventory->nextPageUrl() }}&per_page={{ request('per_page', 10) }}"
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
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "-- Select Material --",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection
