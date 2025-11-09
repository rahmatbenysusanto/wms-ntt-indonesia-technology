@extends('layout.index')
@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card p-2">
                <div class="d-flex gap-2">
                    <a class="btn btn-info">Main Dashboard</a>
                    <a class="btn btn-dark">Inbound</a>
                    <a class="btn btn-dark">Aging</a>
                    <a class="btn btn-dark">Outbound</a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Purchase Order</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-primary">
                                        <span class="counter-value" data-target="10">10</span>
                                    </h4>
                                    <a href="#" class="text-decoration-underline text-primary fw-medium">View Detail</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded fs-3" style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: white;">
                                        <i class="bx bx-cart-alt"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Sales Doc (SO)</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-success">
                                        <span class="counter-value" data-target="25">25</span>
                                    </h4>
                                    <a href="#" class="text-decoration-underline text-success fw-medium">View Detail</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded fs-3" style="background: linear-gradient(135deg, #43e97b, #38f9d7); color: white;">
                                        <i class="bx bx-dollar-circle"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Stock Item</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-warning">
                                        <span class="counter-value" data-target="120">120</span>
                                    </h4>
                                    <a href="#" class="text-decoration-underline text-warning fw-medium">View Detail</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded fs-3" style="background: linear-gradient(135deg, #fddb92, #d1fdff); color: #795548;">
                                        <i class="bx bx-package"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Price</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4 text-danger">
                                        $<span class="counter-value" data-target="5400">5400</span>
                                    </h4>
                                    <a href="#" class="text-decoration-underline text-danger fw-medium">View Detail</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title rounded fs-3" style="background: linear-gradient(135deg, #f5576c, #f093fb); color: white;">
                                        <i class="bx bx-line-chart"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">

                </div>
            </div>
        </div>
    </div>
@endsection
