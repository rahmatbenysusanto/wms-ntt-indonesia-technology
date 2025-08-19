@extends('layout.index')
@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Projects</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a>Dashboard</a></li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <div class="row project-wrapper">
        <div class="col-xxl-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-primary rounded-2 fs-2">
                                        <i data-feather="briefcase"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 overflow-hidden ms-3">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-3">Total Purchase Order</p>
                                    <div class="d-flex align-items-center mb-3">
                                        <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{ $totalPurcDoc }}">0</span></h4>
                                    </div>
                                    <p class="text-muted text-truncate mb-0">Data di bulan {{ date('F Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-warning rounded-2 fs-2">
                                        <i data-feather="award"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-uppercase fw-medium text-muted mb-3">Total Sales Doc</p>
                                    <div class="d-flex align-items-center mb-3">
                                        <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{ $totalSalesDoc }}">0</span></h4>
                                    </div>
                                    <p class="text-muted text-truncate mb-0">Data di bulan {{ date('F Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info rounded-2 fs-2">
                                        <i data-feather="clock"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 overflow-hidden ms-3">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-3">Total Stock Product</p>
                                    <div class="d-flex align-items-center mb-3">
                                        <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{ $totalStock }}">0</span></h4>
                                    </div>
                                    <p class="text-muted text-truncate mb-0">Data Stock Inventory</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-info rounded-2 fs-2">
                                        <i data-feather="clock"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 overflow-hidden ms-3">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-3">Total Box</p>
                                    <div class="d-flex align-items-center mb-3">
                                        <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value" data-target="{{ $stockGR }}">0</span></h4>
                                    </div>
                                    <p class="text-muted text-truncate mb-0">Data Total Box</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end col -->

        <div class="col-xl-8">
            <div class="card">
                <div class="card-header border-0 align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Warehouse Overview</h4>
                </div>

                <div class="card-header p-0 border-0 bg-light-subtle">
                    <div class="row g-0 text-center">
                        <div class="col-6 col-sm-3">
                            <div class="p-3 border border-dashed border-start-0">
                                <h5 class="mb-1"><span class="counter-value" data-target="{{ $totalPO }}">0</span></h5>
                                <p class="text-muted mb-0">Total Purchase Order</p>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-6 col-sm-3">
                            <div class="p-3 border border-dashed border-start-0">
                                <h5 class="mb-1"><span class="counter-value" data-target="{{ $totalQtyPO }}">0</span></h5>
                                <p class="text-muted mb-0">Total QTY Purchase Order</p>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-6 col-sm-3">
                            <div class="p-3 border border-dashed border-start-0">
                                <h5 class="mb-1"><span class="counter-value" data-target="{{ $totalOutbound }}">0</span></h5>
                                <p class="text-muted mb-0">Total Outbound</p>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-6 col-sm-3">
                            <div class="p-3 border border-dashed border-start-0 border-end-0">
                                <h5 class="mb-1"><span class="counter-value" data-target="{{ $totalQtyOutbound }}">0</span></h5>
                                <p class="text-muted mb-0">Total QTY Outbound</p>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                </div><!-- end card header -->
                <div class="card-body p-0 pb-2">
                    <div>
                        <div id="projects-overview-chart" data-colors='["--vz-primary", "--vz-warning", "--vz-success"]' class="apex-charts" dir="ltr"></div>
                    </div>
                </div><!-- end card body -->
            </div><!-- end card -->
        </div>

        <div class="col-xxl-4">
            <div class="card">
                <div class="card-header border-0">
                    <h4 class="card-title mb-0">Daftar Purchase Order Terbaru</h4>
                </div>
                <div class="card-body pt-0">
                    @foreach($listPO as $po)
                        <div class="mini-stats-wid d-flex align-items-center mt-3">
                            <div class="flex-shrink-0 avatar-sm">
                            <span class="mini-stat-icon avatar-title rounded-circle text-success bg-success-subtle fs-4">
                                {{ \Carbon\Carbon::parse($po->created_at)->translatedFormat('d') }}
                            </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $po->purc_doc }}</h6>
                                <p class="text-muted mb-0">{{ $po->customer->name }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <p class="text-muted mb-0">{{ \Carbon\Carbon::parse($po->created_at)->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

