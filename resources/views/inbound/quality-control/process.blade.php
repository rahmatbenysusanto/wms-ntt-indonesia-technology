@extends('layout.index')
@section('title', 'Process QC')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Process Quality Control</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item">Quality Control</li>
                        <li class="breadcrumb-item active">Process</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Purchase Order Information</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">

                    </div>
                </div>
            </div>
        </div>

        <div class="col-12" id="salesDocList">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Sales Doc List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sales Doc</th>
                                    <th class="text-center">Material QTY</th>
                                    <th class="text-center">Item QTY</th>
                                    <th class="text-center">Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($sales_doc as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->sales_doc }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->product_qty) }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->item_qty) }}</td>
                                    <td class="text-center">
                                        @if($item->status == 'process')
                                            <span class="badge bg-info-subtle text-info">QC</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success">Done</span>
                                        @endif
                                    <td>
                                        @if($item->status == 'process')
                                            <a class="btn btn-info btn-sm" href="{{ route('inbound.quality-control-process', ['sales-doc' => $item->sales_doc, 'po' => $purchaseOrder->id]) }}">Process</a>
                                        @endif
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


































