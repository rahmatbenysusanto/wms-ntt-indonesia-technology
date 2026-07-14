@extends('layout.index')
@section('title', 'Swap Serial Number')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Riwayat Swap Serial Number (RMA)</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.swap-sn') }}">Swap SN</a></li>
                    <li class="breadcrumb-item active">Riwayat</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('inventory.swap-sn.history') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted fw-semibold">PA Number (Box)</label>
                        <input type="text" name="paNumber" class="form-control form-control-sm"
                            placeholder="Cari PA Number..." value="{{ request('paNumber') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted fw-semibold">PO Number</label>
                        <input type="text" name="purcDoc" class="form-control form-control-sm"
                            placeholder="Cari PO Number..." value="{{ request('purcDoc') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted fw-semibold">SN Lama</label>
                        <input type="text" name="oldSn" class="form-control form-control-sm"
                            placeholder="Cari SN lama..." value="{{ request('oldSn') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted fw-semibold">SN Baru</label>
                        <input type="text" name="newSn" class="form-control form-control-sm"
                            placeholder="Cari SN baru..." value="{{ request('newSn') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted fw-semibold">Rentang Tanggal</label>
                        <input type="date" name="date_range" class="form-control form-control-sm"
                            value="{{ request('date_range') }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="ri-search-line me-1"></i> Cari
                        </button>
                        <a href="{{ route('inventory.swap-sn.history') }}" class="btn btn-light btn-sm w-100">
                            <i class="ri-refresh-line me-1"></i> Reset
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('inventory.swap-sn') }}" class="btn btn-warning btn-sm w-100">
                            <i class="ri-swap-line me-1"></i> Kembali ke Swap SN
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Riwayat Swap --}}
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1 fw-bold">Riwayat Swap Serial Number</h5>
                        <p class="text-muted small mb-0">
                            <i class="ri-information-line me-1"></i>
                            Menampilkan seluruh riwayat pergantian serial number (RMA) yang telah dilakukan.
                        </p>
                    </div>
                    <span class="badge bg-info-subtle text-info fs-12 px-3 py-2">
                        <i class="ri-history-line me-1"></i>
                        {{ $logs->total() }} Riwayat Ditemukan
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-muted small fw-semibold">
                                <th class="ps-4 py-3">#</th>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">Box (PA Number)</th>
                                <th class="py-3">Material</th>
                                <th class="py-3">SN Lama</th>
                                <th class="py-3">SN Baru</th>
                                <th class="py-3">Alasan</th>
                                <th class="py-3">Diubah Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $i => $log)
                                <tr>
                                    <td class="ps-4 text-muted small">{{ $logs->firstItem() + $i }}</td>
                                    <td>
                                        <span class="small">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td>
                                        @if($log->inventoryPackage)
                                            <span class="fw-bold text-primary">{{ $log->inventoryPackage->number }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->inventoryPackageItem && $log->inventoryPackageItem->purchaseOrderDetail)
                                            <span class="small">{{ $log->inventoryPackageItem->purchaseOrderDetail->material }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <code class="text-danger">{{ $log->old_serial_number }}</code>
                                    </td>
                                    <td>
                                        <code class="text-success">{{ $log->new_serial_number }}</code>
                                    </td>
                                    <td>
                                        <span class="small text-muted">{{ $log->reason ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="small">{{ $log->user->name ?? '-' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="ri-inbox-line display-5 text-muted d-block mb-2"></i>
                                        Belum ada riwayat swap serial number
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($logs->hasPages())
                <div class="card-footer bg-white border-0">
                    <div class="d-flex justify-content-end mt-2">
                        {{ $logs->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
