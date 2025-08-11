@extends('layout.index')
@section('title', 'PM Room')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">PM Room</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">PM Room</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List PM Room</h4>
                        <a href="{{ route('pm-room.create-box') }}" class="btn btn-primary">Create Box</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Number</th>
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th>Client</th>
                                    <th class="text-center">QTY Item</th>
                                    <th class="text-center">QTY</th>
                                    <th>Created At</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pmRoom as $index => $gr)
                                    <tr>
                                        <td>{{ $pmRoom->firstItem() + $index }}</td>
                                        <td>
                                            <div>{{ $gr->number }}</div>
                                            <div><b>Box :</b> {{ $gr->reff_number ?? '-' }}</div>
                                        </td>
                                        <td>{{ $gr->purchaseOrder->purc_doc }}</td>
                                        <td>
                                            @foreach(json_decode($gr->sales_docs) ?? [] as $salesDoc)
                                                <div>{{ $salesDoc }}</div>
                                            @endforeach
                                        </td>
                                        <td>{{ $gr->purchaseOrder->customer->name }}</td>
                                        <td class="text-center fw-bold">{{ $gr->qty_item }}</td>
                                        <td class="text-center fw-bold">{{ $gr->qty }}</td>
                                        <td>{{ \Carbon\Carbon::parse($gr->created_at)->translatedFormat('d F Y H:i') }}</td>
                                        <td>{{ $gr->user->name }}</td>
                                        <td>
                                            <a href="{{ route('pm-room.detail', ['id' => $gr->id]) }}" class="btn btn-info btn-sm">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        @if ($pmRoom->hasPages())
                            <ul class="pagination">
                                @if ($pmRoom->onFirstPage())
                                    <li class="disabled"><span>&laquo; Previous</span></li>
                                @else
                                    <li><a href="{{ $pmRoom->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                @endif

                                @foreach ($pmRoom->links()->elements as $element)
                                    @if (is_string($element))
                                        <li class="disabled"><span>{{ $element }}</span></li>
                                    @endif

                                    @if (is_array($element))
                                        @foreach ($element as $page => $url)
                                            @if ($page == $pmRoom->currentPage())
                                                <li class="active"><span>{{ $page }}</span></li>
                                            @else
                                                <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @if ($pmRoom->hasMorePages())
                                    <li><a href="{{ $pmRoom->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
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
    <script>

    </script>
@endsection
