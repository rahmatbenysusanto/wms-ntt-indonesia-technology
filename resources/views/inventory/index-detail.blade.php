@extends('layout.index')
@section('title', 'Detail Inventory')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Product Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Product Detail</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Product Information</h4>
                </div>
                <div class="card-body">
                    <table>
                        <tr>
                            <td class="fw-bold">Purc Doc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $product->purc_doc }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Sales Doc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $product->sales_doc }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Material</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $product->material }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">PO Item Desc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $product->po_item_desc }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Prod Hierarchy Desc</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $product->prod_hierarchy_desc }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Box Product</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Box Data</th>
                                <th>Serial Number</th>
                                <th class="text-center">QTY</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($dataBox as $box)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div><b>Number: </b>{{ $box->number }}</div>
                                    <div><b>Reff: </b>{{ $box->reff_number }}</div>
                                    <div><b>Storage: </b>{{ $box->raw }} - {{ $box->area }} - {{ $box->rak }} - {{ $box->bin }}</div>
                                    @if($box->return == 1)
                                        @switch($box->return_from)
                                            @case('gr')
                                                <span class="badge bg-danger">Return From General Room</span>
                                                @break
                                            @case('pm')
                                                <span class="badge bg-danger">Return From PM Room</span>
                                                @break
                                            @case('spare')
                                                <span class="badge bg-danger">Return From Spare Room</span>
                                                @break
                                        @endswitch
                                    @endif
                                </td>
                                <td>
                                    @foreach($box->serial_number as $serial_number)
                                        <div>{{ $serial_number->serial_number }}</div>
                                    @endforeach
                                </td>
                                <td class="text-center fw-bold">{{ number_format($box->qty) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Outbound</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Number</th>
                                <th>Delivery Note Number</th>
                                <th>Type</th>
                                <th class="text-center">QTY</th>
                                <th>Serial Number</th>
                                <th>Delivery Loc</th>
                                <th>Delivery Dest</th>
                                <th>Delivery Date</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataOutbound as $outbound)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $outbound->number }}</td>
                                    <td>{{ $outbound->delivery_note_number }}</td>
                                    <td>{{ $outbound->type }}</td>
                                    <td class="text-center fw-bold">{{ number_format($outbound->qty) }}</td>
                                    <td>
                                        @foreach($outbound->serial_number as $serial_number)
                                            <div>{{ $serial_number->serial_number }}</div>
                                        @endforeach
                                    </td>
                                    <td>{{ $outbound->deliv_loc }}</td>
                                    <td>{{ $outbound->deliv_dest }}</td>
                                    <td>{{ \Carbon\Carbon::parse($outbound->delivery_date)->translatedFormat('d F Y H:i') }}</td>
                                    <td>{{ $outbound->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
