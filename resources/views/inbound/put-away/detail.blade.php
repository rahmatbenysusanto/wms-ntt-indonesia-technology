@extends('layout.index')
@section('title', 'Put Away')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Detail Put Away</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item active">Detail Put Away</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Item</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th class="text-center">Item</th>
                                <th  class="text-center">Type</th>
                                <th>Material</th>
                                <th>Po Item Desc</th>
                                <th>Prod Hierarchy Desc</th>
                                <th class="text-center">QTY</th>
                                <th>Data Storage Location</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $product['item'] }}</td>
                                    <td class="text-center"><span class="badge bg-info-subtle text-info">Parent</span></td>
                                    <td>{{ $product['sku'] }}</td>
                                    <td>{{ $product['name'] }}</td>
                                    <td>{{ $product['type'] }}</td>
                                    <td class="text-center fw-bold">{{ number_format($product['qty']) }}</td>
                                    <td>{{ $product['location'] }}</td>
                                    <td>
                                </tr>
                                @foreach($product['child'] as $child)
                                    <tr>
                                        <td></td>
                                        <td class="text-center">{{ $child['item'] }}</td>
                                        <td class="text-center"></td>
                                        <td>{{ $child['sku'] }}</td>
                                        <td>{{ $child['name'] }}</td>
                                        <td>{{ $child['type'] }}</td>
                                        <td class="text-center fw-bold">{{ number_format($child['qty']) }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
