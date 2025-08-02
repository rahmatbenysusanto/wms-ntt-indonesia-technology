@extends('layout.index')
@section('title', 'Dashboard PO')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Dashboard PO</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a>Dashboard PO</a></li>
                    </ol>
                </div>
            </div>
        </div>

        @foreach($listPO as $po)
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <table>
                            <tr>
                                <td class="fw-bold">PO</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1">{{ $po->purc_doc }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Date</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1">{{ \Carbon\Carbon::parse($po->created_at)->translatedFormat('d F Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Client Name</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1">{{ $po->customer->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Value</td>
                                <td class="fw-bold ps-3">:</td>
                                <td class="ps-1">Rp {{ number_format($po->value) }}</td>
                            </tr>
                            @foreach($po->listSO as $index => $salesDoc)
                                <tr>
                                    <td class="fw-bold">SO# {{ $index + 1 }} ( {{ $salesDoc->sales_doc }} )</td>
                                    <td class="fw-bold ps-3">:</td>
                                    <td class="ps-1">Rp {{ number_format($salesDoc->total) }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
