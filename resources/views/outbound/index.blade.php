@extends('layout.index')
@section('title', 'Outbound')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Order List</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Outbound</a></li>
                        <li class="breadcrumb-item active">Order List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Order List</h4>
                        <a href="{{ route('outbound.create') }}" class="btn btn-primary">Create Order</a>
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
                                    <th>QTY Item</th>
                                    <th>Order Date</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($outbound as $index => $out)
                                <tr>
                                    <td>{{ $outbound->firstItem() + $index }}</td>
                                    <td>{{ $out->number }}</td>
                                    <td>{{ $out->purc_doc }}</td>
                                    <td>{{ $out->sales_doc }}</td>
                                    <td>{{ $out->client }}</td>
                                    <td>{{ $out->deliv_loc }}</td>
                                    <td>{{ number_format($out->qty_item) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($po->created_at)->translatedFormat('d F Y H:i') }}</td>
                                    <td>{{ $out->user->name }}</td>
                                    <td>
                                        <a class="btn btn-info btn-sm">Detail</a>
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
