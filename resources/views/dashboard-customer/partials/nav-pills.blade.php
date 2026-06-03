{{-- Modern Dashboard Navigation Pills --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2 px-3">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="nav-pills-container">
                    <ul class="nav nav-pills gap-1" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('customer.dashboard') }}"
                               class="nav-link {{ $navActive == 'dashboard' ? 'active' : '' }} d-flex align-items-center gap-1 px-3">
                                <i class="bx bx-grid-alt fs-15"></i>
                                <span class="d-none d-sm-inline">Main Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('customer.inbound') }}"
                               class="nav-link {{ $navActive == 'inbound' ? 'active' : '' }} d-flex align-items-center gap-1 px-3">
                                <i class="bx bx-package-down fs-15"></i>
                                <span class="d-none d-sm-inline">Inbound</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('customer.aging') }}"
                               class="nav-link {{ $navActive == 'aging' ? 'active' : '' }} d-flex align-items-center gap-1 px-3">
                                <i class="bx bx-time-five fs-15"></i>
                                <span class="d-none d-sm-inline">Aging</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('customer.outbound') }}"
                               class="nav-link {{ $navActive == 'outbound' ? 'active' : '' }} d-flex align-items-center gap-1 px-3">
                                <i class="bx bx-log-out-circle fs-15"></i>
                                <span class="d-none d-sm-inline">Outbound</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="d-flex justify-content-end mt-2 mt-lg-0">
                    <select class="form-select form-select-sm" id="customer" onchange="changeCustomer(this.value)" style="max-width:220px;">
                        <option value="">-- All Customer --</option>
                        @foreach($customer ?? [] as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-pills .nav-link {
    color: #6c757d;
    border-radius: 50rem;
    font-size: 0.8125rem;
    font-weight: 500;
    padding: 0.4rem 0.8rem;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}
.nav-pills .nav-link:hover {
    background-color: rgba(79,172,254,0.08);
    color: #4facfe;
    border-color: rgba(79,172,254,0.15);
}
.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
    color: #fff !important;
    box-shadow: 0 2px 8px rgba(79,172,254,0.3);
    border-color: transparent;
}
.nav-pills .nav-link i {
    font-size: 1rem;
}
</style>
