@extends('layout.index')
@section('title', 'Update Serial Number')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Update Serial Number (N/A)</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active">Update Serial Number</li>
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
                <form method="GET" action="{{ route('inventory.sn-update') }}" class="row g-2 align-items-end">
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
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="ri-search-line me-1"></i> Cari
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('inventory.sn-update') }}" class="btn btn-light btn-sm w-100">
                            <i class="ri-refresh-line me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Daftar Box dengan SN N/A --}}
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1 fw-bold">Daftar Box dengan Serial Number N/A</h5>
                        <p class="text-muted small mb-0">
                            <i class="ri-information-line me-1"></i>
                            Hanya menampilkan box yang memiliki SN bernilai "N/A". Klik tombol <strong>Edit SN</strong> untuk mengubah.
                        </p>
                    </div>
                    <span class="badge bg-warning-subtle text-warning fs-12 px-3 py-2">
                        <i class="ri-error-warning-line me-1"></i>
                        {{ $boxes->total() }} Box Ditemukan
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-muted small fw-semibold">
                                <th class="ps-4 py-3">#</th>
                                <th class="py-3">PA Number (Box)</th>
                                <th class="py-3">PO Number</th>
                                <th class="py-3">Customer</th>
                                <th class="py-3">Storage Location</th>
                                <th class="py-3 text-center">SN N/A Count</th>
                                <th class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($boxes as $i => $box)
                                <tr>
                                    <td class="ps-4 text-muted small">{{ $boxes->firstItem() + $i }}</td>
                                    <td>
                                        <span class="fw-bold text-primary">{{ $box->number }}</span>
                                        @if($box->reff_number)
                                            <br><small class="text-muted">{{ $box->reff_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $box->purchaseOrder->purc_doc ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $box->purchaseOrder->customer->name ?? '-' }}</span>
                                    </td>
                                    <td>
                                        @if($box->storage)
                                            <span class="badge bg-light text-dark border border-light-subtle small">
                                                {{ $box->storage->raw }} | {{ $box->storage->area }} | {{ $box->storage->rak }} | {{ $box->storage->bin }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger-subtle text-danger fs-12 px-3 py-1 rounded-pill">
                                            {{ $box->na_count }} SN
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('inventory.sn-update.detail', ['id' => $box->id]) }}"
                                            class="btn btn-warning btn-sm px-3">
                                            <i class="ri-edit-box-line me-1"></i> Edit SN
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="ri-checkbox-circle-line display-5 text-success d-block mb-2"></i>
                                        Tidak ada Serial Number N/A yang perlu diupdate
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($boxes->hasPages())
                <div class="card-footer bg-white border-0">
                    {{ $boxes->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
