@extends('layout.index')
@section('title', 'Report PO')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Report PO</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Report PO</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Report PO</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('inventory.report-po.download-excel', request()->query()) }}"
                                class="btn btn-success btn-sm">Download
                                Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row">
                            <div class="col-3">
                                <label class="form-label">Client</label>
                                <select name="client" class="form-control select2-client">
                                    <option value="">-- Select Client --</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ request()->get('client') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <label class="form-label">Purc Doc</label>
                                <input type="text" class="form-control" value="{{ request()->get('purcDoc', null) }}"
                                    name="purcDoc" placeholder="Purc Doc">
                            </div>
                            <div class="col-3">
                                <label class="form-label">Material</label>
                                <select name="material" class="form-control select2">
                                    <option value="">-- Select Material --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->material }}"
                                            {{ request()->get('material') == $product->material ? 'selected' : '' }}>
                                            {{ $product->material }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <label class="form-label text-white">-</label>
                                <div>
                                    <button type="submit" class="btn btn-info">Search</button>
                                    <a href="{{ route('inventory.report-po') }}" class="btn btn-danger">Clear</a>
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
                                    <th>Material</th>
                                    <th>PO Item Desc</th>
                                    <th class="text-center">PO Qty</th>
                                    <th class="text-center">In Stock</th>
                                    <th class="text-center">Out Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report as $index => $item)
                                    <tr>
                                        <td>{{ $report->firstItem() + $index }}</td>
                                        <td>{{ $item->customer_name }}</td>
                                        <td>{{ $item->purc_doc }}</td>
                                        <td>{{ $item->material }}</td>
                                        <td>{{ $item->po_item_desc }}</td>
                                        <td class="text-center">{{ number_format($item->po_item_qty) }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-link link-success fw-bold btn-show-sn p-0"
                                                data-id="{{ $item->po_detail_id }}" data-type="in-stock"
                                                title="View Serial Numbers">
                                                {{ number_format($item->in_stock) }}
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-link link-danger fw-bold btn-show-sn p-0"
                                                data-id="{{ $item->po_detail_id }}" data-type="out-stock"
                                                title="View Serial Numbers">
                                                {{ number_format($item->out_stock) }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end mt-2">
                            @if ($report->hasPages())
                                <ul class="pagination">
                                    @if ($report->onFirstPage())
                                        <li class="disabled"><span>&laquo; Previous</span></li>
                                    @else
                                        <li><a href="{{ $report->previousPageUrl() }}&per_page={{ request('per_page', 10) }}"
                                                rel="prev">&laquo; Previous</a></li>
                                    @endif

                                    @foreach ($report->links()->elements as $element)
                                        @if (is_string($element))
                                            <li class="disabled"><span>{{ $element }}</span></li>
                                        @endif

                                        @if (is_array($element))
                                            @foreach ($element as $page => $url)
                                                @if ($page == $report->currentPage())
                                                    <li class="active"><span>{{ $page }}</span></li>
                                                @else
                                                    <li><a
                                                            href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach

                                    @if ($report->hasMorePages())
                                        <li><a href="{{ $report->nextPageUrl() }}&per_page={{ request('per_page', 10) }}"
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

    <!-- Modal Detail SN -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailLabel">Detail Serial Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalLoading" class="text-center py-3" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="modalContent">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-striped" id="tableSn">
                                <thead style="position: sticky; top: 0; background: white; z-index: 1;">
                                    <tr>
                                        <th width="10%">No</th>
                                        <th>Serial Number</th>
                                        <th width="15%" class="text-center">Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="snList">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2, .select2-client').select2({
                placeholder: "-- Select --",
                allowClear: true,
                width: '100%'
            });

            $('.btn-show-sn').on('click', function() {
                const id = $(this).data('id');
                const type = $(this).data('type');
                const url = type === 'in-stock' ?
                    "{{ route('inventory.report-po.detail-in-stock') }}" :
                    "{{ route('inventory.report-po.detail-out-stock') }}";

                $('#modalDetailLabel').text(type === 'in-stock' ? 'Detail Serial Number In Stock' :
                    'Detail Serial Number Out Stock');
                $('#snList').empty();
                $('#modalLoading').show();
                $('#modalContent').hide();
                $('#modalDetail').modal('show');

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        $('#modalLoading').hide();
                        $('#modalContent').show();
                        if (response.status && response.data.length > 0) {
                            let totalQty = 0;
                            response.data.forEach((item, index) => {
                                totalQty += parseFloat(item.qty);
                                $('#snList').append(`
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${item.serial_number || 'N/A'}</td>
                                        <td class="text-center">${item.qty}</td>
                                    </tr>
                                `);
                            });
                            // Optional: add total row
                            $('#snList').append(`
                                <tr class="table-light fw-bold">
                                    <td colspan="2" class="text-end">Total</td>
                                    <td class="text-center">${totalQty}</td>
                                </tr>
                            `);
                        } else {
                            $('#snList').append(
                                '<tr><td colspan="3" class="text-center">No Serial Number found</td></tr>'
                            );
                        }
                    },
                    error: function() {
                        $('#modalLoading').hide();
                        Swal.fire('Error', 'Failed to fetch data', 'error');
                    }
                });
            });
        });
    </script>
@endsection
