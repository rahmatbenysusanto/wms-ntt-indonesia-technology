@extends('layout.index')
@section('title', 'Dashboard Aging')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card p-2">
                <div class="row">
                    <div class="col-9">
                        <div class="d-flex gap-2">
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-dark">Main Dashboard</a>
                            <a href="{{ route('customer.inbound') }}" class="btn btn-dark">Inbound</a>
                            <a href="{{ route('customer.aging') }}" class="btn btn-info">Aging</a>
                            <a href="{{ route('customer.outbound') }}" class="btn btn-dark">Outbound</a>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="d-flex justify-content-end">
                            <select class="form-control" id="customer" onchange="changeCustomer(this.value)">
                                <option value="">-- All Customer --</option>
                                @foreach($customer as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
