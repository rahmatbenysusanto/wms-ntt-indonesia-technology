@extends('layout.index')
@section('title', 'Storage Inventory')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Storage Inventory</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Storage Inventory</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Storage Inventory List</h4>
                    </div>
                </div>
                <div class="card-header">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row">
                            <div class="col-2">
                                <label class="form-label">Raw</label>
                                <input type="text" class="form-control" value="{{ request()->get('raw', null) }}"
                                    name="raw" placeholder="Raw">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Area</label>
                                <input type="text" class="form-control" value="{{ request()->get('area', null) }}"
                                    name="area" placeholder="Area">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Rak</label>
                                <input type="text" class="form-control" value="{{ request()->get('rak', null) }}"
                                    name="rak" placeholder="Rak">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Bin</label>
                                <input type="text" class="form-control" value="{{ request()->get('bin', null) }}"
                                    name="bin" placeholder="Bin">
                            </div>
                            <div class="col-4">
                                <label class="form-label text-white">-</label>
                                <div>
                                    <button type="submit" class="btn btn-info">Search</button>
                                    <a href="{{ route('inventory.storage-inventory') }}" class="btn btn-danger">Clear</a>
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
                                    <th>Raw</th>
                                    <th>Area</th>
                                    <th>Rak</th>
                                    <th>Bin</th>
                                    <th class="text-center">Total Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($storage as $index => $item)
                                    <tr>
                                        <td>{{ $storage->firstItem() + $index }}</td>
                                        <td>{{ $item->raw }}</td>
                                        <td>{{ $item->area ?? '-' }}</td>
                                        <td>{{ $item->rak ?? '-' }}</td>
                                        <td>{{ $item->bin ?? '-' }}</td>
                                        <td class="text-center fw-bold">{{ number_format($item->total_qty) }}</td>
                                        <td>
                                            <a href="{{ route('inventory.storage-inventory.detail', ['id' => $item->id]) }}"
                                                class="btn btn-info btn-sm" title="View Detail"><i
                                                    class="ri-eye-line"></i> Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end mt-2">
                            {{ $storage->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
