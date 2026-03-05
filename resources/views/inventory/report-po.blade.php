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
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-4">
                                <label class="form-label">Purc Doc</label>
                                <select name="purcDoc" class="form-control select2">
                                    <option value="">-- Select PO --</option>
                                    @foreach ($purchaseOrders as $po)
                                        <option value="{{ $po->purc_doc }}"
                                            {{ request()->get('purcDoc') == $po->purc_doc ? 'selected' : '' }}>
                                            {{ $po->purc_doc }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <button type="submit" class="btn btn-primary">Process Report</button>
                                <a href="{{ route('inventory.report-po') }}" class="btn btn-danger">Clear</a>
                            </div>
                        </div>
                    </form>
                </div>

                @if (request()->get('purcDoc') && isset($summary))
                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-xl-4 col-md-6">
                                <div class="card card-animate bg-primary">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <p class="text-uppercase fw-medium text-white-50 mb-0">Total PO Qty</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end justify-content-between mt-4">
                                            <div>
                                                <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-white">
                                                    {{ number_format($summary['total_po']) }}</h4>
                                            </div>
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-white-50 rounded fs-3">
                                                    <i class="ri-shopping-cart-2-line text-white"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="card card-animate bg-success">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <p class="text-uppercase fw-medium text-white-50 mb-0">Inventory Stock</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end justify-content-between mt-4">
                                            <div>
                                                <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-white">
                                                    {{ number_format($summary['total_stock']) }}</h4>
                                            </div>
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-white-50 rounded fs-3">
                                                    <i class="ri-archive-line text-white"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="card card-animate bg-danger">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <p class="text-uppercase fw-medium text-white-50 mb-0">Total Outbound</p>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end justify-content-between mt-4">
                                            <div>
                                                <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-white">
                                                    {{ number_format($summary['total_outbound']) }}</h4>
                                            </div>
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-white-50 rounded fs-3">
                                                    <i class="ri-truck-line text-white"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Result Table -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Detail Statistics for PO: {{ request()->get('purcDoc') }}</h5>
                            <a href="{{ route('inventory.report-po.download-excel', request()->query()) }}"
                                class="btn btn-success btn-sm">Download Excel Report</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Material</th>
                                        <th>Description</th>
                                        <th class="text-center">PO Qty</th>
                                        <th class="text-center">Stock</th>
                                        <th class="text-center">Outbound</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($report as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->material }}</td>
                                            <td>{{ $item->po_item_desc }}</td>
                                            <td class="text-center fw-medium">{{ number_format($item->po_item_qty) }}</td>
                                            <td class="text-center text-success fw-bold">
                                                {{ number_format($item->in_stock) }}</td>
                                            <td class="text-center text-danger fw-bold">
                                                {{ number_format($item->out_stock) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif(request()->get('purcDoc'))
                    <div class="card-body">
                        <div class="alert alert-warning mb-0">
                            No data found for PO: {{ request()->get('purcDoc') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "-- Select PO --",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection
