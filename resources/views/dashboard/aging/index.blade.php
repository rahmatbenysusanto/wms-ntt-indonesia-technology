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
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1 - 90 Day</td>
                                <td>Rp {{ number_format($aging1, 2) }}</td>
                            </tr>
                            <tr>
                                <td>91 - 180 Day</td>
                                <td>Rp {{ number_format($aging2, 2) }}</td>
                            </tr>
                            <tr>
                                <td>181 - 365 Day</td>
                                <td>Rp {{ number_format($aging3, 2) }}</td>
                            </tr>
                            <tr>
                                <td>> 365 Day</td>
                                <td>Rp {{ number_format($aging4, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
