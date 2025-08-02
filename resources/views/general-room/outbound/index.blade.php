@extends('layout.index')
@section('title', 'GR Outbound')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">General Room Outbound</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">General Room</a></li>
                        <li class="breadcrumb-item active">Outbound</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Outbound</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('general-room.create') }}" class="btn btn-primary">Create Outbound</a>
                            <a href="{{ route('general-room.return') }}" class="btn btn-info">Create Return</a>
                        </div>
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
                                    <th>Deliv Loc</th>
                                    <th class="text-center">Deliv Dest</th>
                                    <th class="text-center">QTY Item</th>
                                    <th class="text-center">Type</th>
                                    <th>Order Date</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($generalRoom as $index => $item)
                                <tr>
                                    <td>{{ $generalRoom->firstItem() + $index }}</td>
                                    <td>{{ $item->number }}</td>
                                    <td>{{ $item->purc_doc }}</td>
                                    <td>
                                        @foreach(json_decode($item->sales_docs) as $detail)
                                            <div>{{ $detail }}</div>
                                        @endforeach
                                    </td>
                                    <td>{{ $item->customer->name }}</td>
                                    <td>{{ $item->deliv_loc }}</td>
                                    <td class="text-center">{{ $item->deliv_dest }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->qty_item) }}</td>
                                    <td class="text-center">
                                        @if($item->status == 'outbound')
                                            <span class="badge bg-info-subtle text-info">Outbound</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">Return</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>
                                        <a href="{{ route('outbound.detail', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Detail</a>
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
