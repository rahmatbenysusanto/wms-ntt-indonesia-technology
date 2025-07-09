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
                <div class="card-header">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row">
                            <div class="col-2">
                                <label class="form-label">Purc Doc</label>
                                <input type="text" class="form-control" value="{{ request()->get('purcDoc', null) }}" name="purcDoc">
                            </div>
                            <div class="col-2">
                                <label class="form-label">Sales Doc</label>
                                <input type="text" class="form-control" value="{{ request()->get('salesDoc', null) }}" name="salesDoc">
                            </div>
                            <div class="col-2">
                                <label class="form-label text-white">-</label>
                                <div>
                                    <button type="submit" class="btn btn-info">Search</button>
                                    <a href="{{ route('inbound.put-away') }}" class="btn btn-danger">Clear</a>
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
                                    <th>Purchasing Document</th>
                                    <th>Sales Doc</th>
                                    <th>Parent Material</th>
                                    <th>Parent Item Desc</th>
                                    <th class="text-center">QTY Parent</th>
                                    <th class="text-center">Status</th>
                                    <th>QC Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($putAway as $index => $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->purchaseOrder->purc_doc }}</td>
                                    <td>
                                        @php $salesDoc = [] @endphp
                                        @foreach($item->productParentDetail as $detail)
                                            @php $salesDoc[] = $detail->sales_doc @endphp
                                        @endforeach
                                        @php
                                            $salesDoc = array_unique($salesDoc);
                                        @endphp
                                        @foreach($salesDoc as $sales)
                                            <p class="mb-1">{{ $sales }}</p>
                                        @endforeach
                                    </td>
                                    <td>{{ $item->product->material }}</td>
                                    <td>{{ $item->product->po_item_desc }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->qty) }}</td>
                                    <td>
                                        @if($item->storage_id == null)
                                            <span class="badge bg-warning-subtle text-warning">Progress</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success">Done</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('inbound.put-away-detail', ['id' => $item->id]) }}" class="btn btn-primary btn-sm">Detail</a>
                                            @if($item->storage_id == null)
                                                <a href="{{ route('inbound.put-away-process', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Put Away</a>
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
