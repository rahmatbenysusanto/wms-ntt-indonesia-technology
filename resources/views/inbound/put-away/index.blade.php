@extends('layout.index')
@section('title', 'Put Away')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Put Away</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item active">Put Away</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Put Away</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Quality Control Number</th>
                                    <th>Purchasing Document</th>
                                    <th>Sales Doc</th>
                                    <th class="text-center">QTY Parent</th>
                                    <th class="text-center">Status</th>
                                    <th>Quality Control By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($putAway as $index => $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->number }}</td>
                                    <td>{{ $item->purchaseOrder->purc_doc }}</td>
                                    <td>{{ $item->sales_doc }}</td>
                                    <td class="text-center">{{ number_format($item->qty_parent) }}</td>
                                    <td class="text-center">
                                        @switch($item->status)
                                            @case('qc')
                                                <span class="badge bg-info-subtle text-info">Quality Control</span>
                                                @break
                                            @case('put away')
                                                <span class="badge bg-secondary-subtle text-secondary">Put Away</span>
                                                @break
                                            @case('done')
                                                <span class="badge bg-primary-subtle text-primary">Done</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('inbound.put-away-detail', ['number' => $item->number]) }}" class="btn btn-info btn-sm">Detail</a>
                                            @if(in_array($item->status, ['qc', 'put away']))
                                                <a href="{{ route('inbound.put-away-process', ['number' => $item->number]) }}" class="btn btn-secondary btn-sm">Proses Put Away</a>
                                            @endif
                                        </div>
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
