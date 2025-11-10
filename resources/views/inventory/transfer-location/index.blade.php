@extends('layout.index')
@section('title', 'Transfer Location')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Transfer Location</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Transfer Location</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Transfer Location History</h4>
                        <a href="{{ route('inventory.transfer-location-create') }}" class="btn btn-primary btn-sm">Create Transfer Location</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Data Box</th>
                                    <th>Old Storage</th>
                                    <th>New Storage</th>
                                    <th>Created Date</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfer as $index => $item)
                                    <tr>
                                        <td>{{ $transfer->firstItem() + $index }}</td>
                                        <td>{{ $item->inventoryPackage->number }} | {{ $item->inventoryPackage->reff_number }}</td>
                                        <td>{{ $item->oldLocation->raw }} - {{ $item->oldLocation->area }} - {{ $item->oldLocation->rak }} - {{ $item->oldLocation->bin }}</td>
                                        <td>{{ $item->newLocation->raw }} - {{ $item->newLocation->area }} - {{ $item->newLocation->rak }} - {{ $item->newLocation->bin }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</td>
                                        <td>{{ $item->user->name }}</td>
                                        <td>
                                            <a href="{{ route('inventory.box.detail', ['id' => $item->inventoryPackage->id]) }}" class="btn btn-info btn-sm">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        @if ($transfer->hasPages())
                            <ul class="pagination">
                                @if ($transfer->onFirstPage())
                                    <li class="disabled"><span>&laquo; Previous</span></li>
                                @else
                                    <li><a href="{{ $transfer->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                @endif

                                @foreach ($transfer->links()->elements as $element)
                                    @if (is_string($element))
                                        <li class="disabled"><span>{{ $element }}</span></li>
                                    @endif

                                    @if (is_array($element))
                                        @foreach ($element as $page => $url)
                                            @if ($page == $transfer->currentPage())
                                                <li class="active"><span>{{ $page }}</span></li>
                                            @else
                                                <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @if ($transfer->hasMorePages())
                                    <li><a href="{{ $transfer->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
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
