@extends('layout.index')
@section('title', 'Detail General Room')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">General Room Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">General Room</a></li>
                        <li class="breadcrumb-item">List</li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Detail Product General Room</h4>
                </div>
                <div class="card-body">
                    <table>
                        <tr>
                            <td class="fw-bold">General Room Number</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $generalRoom->number }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Outbound Number</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ $outbound->number }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Outbound Date</td>
                            <td class="fw-bold ps-3">:</td>
                            <td class="ps-1">{{ \Carbon\Carbon::parse($outbound->created_at)->translatedFormat('d F Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title mb-0">List product</div>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Material</th>
                                <th>PO Item Desc</th>
                                <th>QTY</th>
                                <th>Serial Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($generalRoomDetail as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail->product->material }}</td>
                                    <td>{{ $detail->product->po_item_desc }}</td>
                                    <td>{{ number_format($detail->qty) }}</td>
                                    <td>
                                        @foreach(json_decode($detail->serial_number, true) as $serialNumber)
                                            <div>{{ $serialNumber }}</div>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
