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

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Purchase Order</h4>
                </div>
                <div class="card-body">
                    <table class="table table-responsive align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Purc Doc</th>
                                <th>Client</th>
                                <th class="text-center">Total SO</th>
                                <th class="text-center">Total QTY PO</th>
                                <th class="text-center">QTY Inbound</th>
                                <th class="text-center">Stock Inventory</th>
                                <th class="text-center">QTY Outbound</th>
                                <th>Created Date</th>
                                <th>Created By</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listPO as $index => $item)
                                <tr>
                                    <td>{{ $listPO->firstItem() + $index }}</td>
                                    <td>{{ $item->purc_doc }}</td>
                                    <td>{{ $item->customer->name }}</td>
                                    <td class="text-center fw-bold">
                                        <a href="" class="text-black">{{ number_format($item->sales_doc_qty) }}</a>
                                    </td>
                                    <td class="text-center fw-bold">{{ number_format($item->item_qty) }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->qty_po) }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->stock) }}</td>
                                    <td class="text-center fw-bold">{{ number_format($item->qty_outbound) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y H:i') }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>
                                        <a href="{{ route('dashboard.po.detail', ['id' => $item->id]) }}" class="btn btn-info btn-sm">Detail</a>
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
