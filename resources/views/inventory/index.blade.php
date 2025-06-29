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
                    <h4 class="card-title mb-0">Product List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th class="text-center">Parent</th>
                                    <th class="text-center">Child</th>
                                    <th class="text-center">Item QTY</th>
                                    <th>Storage Loc</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($inventory as $index => $inv)
                                <tr>
                                    <td>{{ $inventory->firstItem() + $index }}</td>
                                    <td>{{ $inv->purc_doc }}</td>
                                    <td>{{ $inv->sales_doc }}</td>
                                    <td class="text-center fw-bold">{{ $inv->parent }}</td>
                                    <td class="text-center fw-bold">{{ $inv->child }}</td>
                                    <td class="text-center fw-bold">{{ number_format($inv->qty_item) }}</td>
                                    <td>{{ $inv->storage->raw }} - {{ $inv->storage->area }} - {{ $inv->storage->rak }} - {{ $inv->storage->bin }} </td>
                                    <td><a href="{{ route('inventory.detail', ['id' => $inv->id]) }}" class="btn btn-info btn-sm">Detail Item</a></td>
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
