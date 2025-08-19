@extends('layout.index')
@section('title', 'Detail Serial Number')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Dashboard Detail Serial Number</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a>Dashboard Detail Serial Number</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Serial Number</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($serialNumber as $sn)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sn->serial_number }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
