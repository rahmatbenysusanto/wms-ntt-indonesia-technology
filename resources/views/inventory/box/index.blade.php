@extends('layout.index')
@section('title', 'List Box Inventory')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Inventory Box List</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Box List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Box</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Number</th>
                                    <th>Reff</th>
                                    <th>Storage</th>
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th class="text-center">QTY Item</th>
                                    <th class="text-center">QTY</th>
                                    <th>Created By</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($box as $index => $item)
                                    <tr>
                                        <td>{{ $box->firstItem() + $index }}</td>
                                        <td>{{ $item->number }}</td>
                                        <td>{{ $item->reff_number }}</td>
                                        <td>
                                            <div><b>Raw: </b>{{ $item->storage->raw }}</div>
                                            <div><b>Area: </b>{{ $item->storage->area }}</div>
                                            <div><b>Rak: </b>{{ $item->storage->rak }}</div>
                                            <div><b>Bin: </b>{{ $item->storage->bin }}</div>
                                        </td>
                                        <td>{{ $item->purchaseOrder->purc_doc }}</td>
                                        <td>
                                            @foreach(json_decode($item->sales_docs) ?? [] as $salesDoc)
                                                <div>{{ $salesDoc }}</div>
                                            @endforeach
                                        </td>
                                        <td class="text-center fw-bold">{{ number_format($item->qty_item) }}</td>
                                        <td class="text-center fw-bold">{{ number_format($item->qty) }}</td>
                                        <td>{{ $item->user->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('inventory.box.detail', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
