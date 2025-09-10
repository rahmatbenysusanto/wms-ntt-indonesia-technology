@extends('layout.index')
@section('title', 'Cycle Count')

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
                        <h4 class="card-title mb-0">List Cycle Count Product</h4>
                        <div class="d-flex gap-2">
                            <a class="btn btn-pdf btn-sm" onclick="downloadPDF()">Download PDF</a>
                            <a class="btn btn-success btn-sm" onclick="downloadExcel()">Download Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row">
                            <div class="col-2">
                                <label class="form-label">Purc Doc</label>
                                <input type="text" class="form-control" name="purcDoc" value="{{ request()->get('purcDoc') }}" placeholder="Purc Doc ...">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Sales Doc</label>
                                <input type="text" class="form-control" name="salesDoc" value="{{ request()->get('salesDoc') }}" placeholder="Sales Doc ...">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Material</label>
                                <input type="text" class="form-control" name="material" value="{{ request()->get('material') }}" placeholder="Material ...">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Type</label>
                                <select class="form-control" name="type">
                                    <option value="">-- Select Type --</option>
                                    <option value="inbound">Inbound</option>
                                    <option value="outbound">Outbound</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" value="{{ request()->get('date') }}">
                            </div>
                            <div class="col-2">
                                <label class="form-label text-white">-</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <a href="{{ url()->current() }}" class="btn btn-danger">Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Purc Doc</th>
                                <th>Sales Doc</th>
                                <th>Material</th>
                                <th class="text-center">QTY</th>
                                <th>Storage Loc</th>
                                <th>Type</th>
                                <th class="text-center">Serial Number</th>
                                <th>Created By</th>
                                <td>Date</td>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($cycleCount as $index => $item)
                            <tr>
                                <td>{{ $cycleCount->firstItem() + $index }}</td>
                                <td>
                                    {{ $item->purchaseOrder->purc_doc }}
                                    @if($item->inventoryPackageItem->inventoryPackage->storage->id == 1)
                                        <br>
                                        <span class="badge bg-danger">Cross Docking</span>
                                    @endif
                                </td>
                                <td>{{ $item->purchaseOrderDetail->sales_doc }}</td>
                                <td>
                                    <div class="fw-bold">{{ $item->purchaseOrderDetail->material }}</div>
                                    <div>{{ $item->purchaseOrderDetail->po_item_desc }}</div>
                                    <div>{{ $item->purchaseOrderDetail->prod_hierarchy_desc }}</div>
                                </td>
                                <td class="text-center fw-bold">{{ number_format($item->qty) }}</td>
                                <td>
                                    @if(in_array($item->inventoryPackageItem->inventoryPackage->storage->id, [2,3,4]))
                                        <b>{{ $item->inventoryPackageItem->inventoryPackage->storage->raw }}</b>
                                    @elseif($item->inventoryPackageItem->inventoryPackage->storage->id == 1)
                                        <b>Direct Outbound</b>
                                    @else
                                        <b>{{ $item->inventoryPackageItem->inventoryPackage->storage->raw }}</b> -
                                        <b>{{ $item->inventoryPackageItem->inventoryPackage->storage->area }}</b> -
                                        <b>{{ $item->inventoryPackageItem->inventoryPackage->storage->rak }}</b> -
                                        <b>{{ $item->inventoryPackageItem->inventoryPackage->storage->bin }}</b>
                                    @endif
                                </td>
                                <td>
                                    @if($item->type == 'outbound')
                                        <span class="badge bg-danger-subtle text-danger">Outbound</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success">Inbound</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('inventory.cycle-count-detail', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Detail SN</a>
                                </td>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end mt-2">
                        @if ($cycleCount->hasPages())
                            <ul class="pagination">
                                @if ($cycleCount->onFirstPage())
                                    <li class="disabled"><span>&laquo; Previous</span></li>
                                @else
                                    <li><a href="{{ $cycleCount->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                @endif

                                @foreach ($cycleCount->links()->elements as $element)
                                    @if (is_string($element))
                                        <li class="disabled"><span>{{ $element }}</span></li>
                                    @endif

                                    @if (is_array($element))
                                        @foreach ($element as $page => $url)
                                            @if ($page == $cycleCount->currentPage())
                                                <li class="active"><span>{{ $page }}</span></li>
                                            @else
                                                <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @if ($cycleCount->hasMorePages())
                                    <li><a href="{{ $cycleCount->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
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

    <!-- Download PDF Modals -->
    <div id="download-pdf-modal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Download Cycle Count PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('inventory.cycle-count.download-pdf') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="startDate" id="startDate">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="endDate" id="endDate">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-control" name="type">
                                <option value="all">All</option>
                                <option value="inbound">Inbound</option>
                                <option value="outbound">Outbound</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" formtarget="_blank" rel="noopener noreferrer">Download</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Download Excel Modals -->
    <div id="download-excel-modal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Download Cycle Count Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('inventory.cycle-count.download-excel') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="startDate" id="startDate">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="endDate" id="endDate">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-control" name="type">
                                <option value="all">All</option>
                                <option value="inbound">Inbound</option>
                                <option value="outbound">Outbound</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" formtarget="_blank" rel="noopener noreferrer">Download</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function downloadPDF() {
            $('#download-pdf-modal').modal('show');
        }

        function downloadExcel() {
            $('#download-excel-modal').modal('show');
        }
    </script>
@endsection
