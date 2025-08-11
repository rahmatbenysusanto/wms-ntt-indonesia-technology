@extends('layout.index')
@section('title', 'Dashboard Aging')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Dashboard Aging</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a>Dashboard Aging</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Data Products Aging</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Aging Date</th>
                                <th>Total Price</th>
                                <th class="text-center">Total QTY</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1 - 90 Day</td>
                                <td>Rp {{ number_format($agingType1->total, 2) }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('dashboard.aging.detail', ['type' => 1]) }}" class="text-black">{{ number_format($agingType1->qty) }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td>91 - 180 Day</td>
                                <td>Rp {{ number_format($agingType2->total, 2) }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('dashboard.aging.detail', ['type' => 2]) }}" class="text-black">{{ number_format($agingType2->qty) }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td>181 - 365 Day</td>
                                <td>Rp {{ number_format($agingType3->total, 2) }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('dashboard.aging.detail', ['type' => 3]) }}" class="text-black">{{ number_format($agingType3->qty) }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td>> 365 Day</td>
                                <td>Rp {{ number_format($agingType4->total, 2) }}</td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('dashboard.aging.detail', ['type' => 4]) }}" class="text-black">{{ number_format($agingType4->qty) }}</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
