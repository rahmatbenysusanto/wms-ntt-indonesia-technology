@extends('layout.index')
@section('title', 'Detail SO')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Dashboard SO Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a>Dashboard SO Detail</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Material</th>
                                <th>PO Item Desc</th>
                                <th>Prod Hierarchy Desc</th>
                                <th class="text-center">QTY PO</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Outbound</th>
                            </tr>
                        </thead>
                        @foreach($purchaseOrderDetail as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $detail->item }}</td>
                                <td>{{ $detail->material }}</td>
                                <td>{{ $detail->po_item_desc }}</td>
                                <td>{{ $detail->prod_hierarchy_desc }}</td>
                                <td class="text-center">{{ number_format($detail->po_item_qty) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('dashboard.po.stock.sn', ['id' => $detail->id]) }}">{{ number_format($detail->stock) }}</a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('dashboard.po.outbound.sn', ['id' => $detail->id]) }}">{{ number_format($detail->qty_outbound) }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
