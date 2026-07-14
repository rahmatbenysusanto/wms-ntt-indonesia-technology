@extends('layout.index')
@section('title', 'Swap Serial Number')

@section('content')
<style>
    .sn-input-new { transition: border-color 0.2s; }
    .sn-input-new:focus { border-color: #405189; box-shadow: 0 0 0 0.15rem rgba(64,81,137,.2); }
    .log-badge { font-size: 11px; }
</style>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Swap Serial Number — Box <span class="text-primary">{{ $inventoryPackage->number }}</span></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.swap-sn') }}">Swap SN</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- Info Box --}}
<div class="row mb-3">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="fw-semibold text-muted small mb-1">PA Number</div>
                        <div class="fw-bold text-primary">{{ $inventoryPackage->number }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-semibold text-muted small mb-1">PO Number</div>
                        <div class="fw-bold">{{ $inventoryPackage->purchaseOrder->purc_doc ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-semibold text-muted small mb-1">Customer</div>
                        <div class="fw-bold">{{ $inventoryPackage->purchaseOrder->customer->name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="fw-semibold text-muted small mb-1">Storage Location</div>
                        @if($inventoryPackage->storage)
                            <span class="badge bg-light text-dark border">
                                {{ $inventoryPackage->storage->raw }} | {{ $inventoryPackage->storage->area }} | {{ $inventoryPackage->storage->rak }} | {{ $inventoryPackage->storage->bin }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <div class="fw-semibold text-muted small mb-1">Total QTY</div>
                        <div class="fw-bold">{{ $inventoryPackage->qty }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="fw-semibold text-muted small mb-1">Total SN</div>
                        <div class="fw-bold text-info">{{ $totalSnCount }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 bg-warning-subtle border border-warning-subtle">
            <div class="card-body d-flex align-items-center">
                <div>
                    <i class="ri-information-line fs-24 text-warning mb-2 d-block"></i>
                    <p class="text-warning-emphasis mb-0 small">
                        Swap SN digunakan untuk mengganti serial number (RMA). SN lama akan tetap tersimpan sebagai referensi dalam log sistem.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form Swap SN --}}
<form id="formSwapSN" action="{{ route('inventory.swap-sn.store') }}" method="POST">
    @csrf
    <input type="hidden" name="inventory_package_id" value="{{ $inventoryPackage->id }}">

    @foreach($items as $item)
        @php
            $snList = $item->inventoryPackageItemSn;
        @endphp

        @if($snList->isNotEmpty())
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 pt-3 pb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="fw-bold text-dark">{{ $item->purchaseOrderDetail->material ?? '-' }}</span>
                        <span class="ms-2 text-muted small">{{ $item->purchaseOrderDetail->po_item_desc ?? '' }}</span>
                        <div class="mt-1">
                            <span class="badge {{ $item->is_parent ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary' }} small me-1">
                                {{ $item->is_parent ? 'Parent' : 'Child' }}
                            </span>
                            <span class="badge bg-light text-dark border small">
                                Sales Doc: {{ $item->purchaseOrderDetail->sales_doc ?? '-' }}
                            </span>
                        </div>
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2">
                        <i class="ri-file-list-line me-1"></i> {{ $snList->count() }} SN
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th class="ps-3 py-2">#</th>
                                <th class="py-2">Serial Number Lama</th>
                                <th class="py-2" style="min-width:200px;">Serial Number Baru</th>
                                <th class="py-2" style="min-width:180px;">Alasan RMA</th>
                                <th class="py-2 text-center">Riwayat Swap</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($snList as $idx => $sn)
                                <tr>
                                    <td class="ps-3 small text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <code class="text-dark fw-semibold">
                                            {{ $sn->serial_number }}
                                        </code>
                                    </td>
                                    <td>
                                        <input type="text"
                                            name="sn_swaps[{{ $sn->id }}][new_serial_number]"
                                            class="form-control form-control-sm sn-input-new"
                                            placeholder="Masukkan SN baru (kosongkan jika tidak swap)"
                                            value="">
                                        <input type="hidden" name="sn_swaps[{{ $sn->id }}][inventory_package_item_id]" value="{{ $item->id }}">
                                        <input type="hidden" name="sn_swaps[{{ $sn->id }}][old_serial_number]" value="{{ $sn->serial_number }}">
                                    </td>
                                    <td>
                                        <input type="text"
                                            name="sn_swaps[{{ $sn->id }}][reason]"
                                            class="form-control form-control-sm"
                                            placeholder="Contoh: RMA - unit defect"
                                            style="min-width:150px;">
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $logCount = $swapLogs->where('inventory_package_item_sn_id', $sn->id)->count();
                                        @endphp
                                        @if($logCount > 0)
                                            <button type="button"
                                                class="btn btn-link btn-sm text-info p-0 log-badge"
                                                onclick="showSwapLog({{ $sn->id }})"
                                                title="Lihat {{ $logCount }} swap">
                                                <i class="ri-history-line"></i> {{ $logCount }}x
                                            </button>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    @endforeach

    <div class="row mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-0 text-muted small">
                                <i class="ri-save-line me-1"></i>
                                Serial Number lama akan tetap direkam sebagai <strong>referensi RMA</strong> di log sistem.
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('inventory.swap-sn') }}" class="btn btn-light px-4">
                                <i class="ri-arrow-left-line me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold" id="btnSave">
                                <i class="ri-swap-line me-1"></i> Simpan Swap
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Log History Modal --}}
<div class="modal fade" id="logModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white"><i class="ri-history-line me-2"></i>Riwayat Swap SN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="logModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Log Data (hidden) --}}
<script>
    const swapLogsData = @json($swapLogs->groupBy('inventory_package_item_sn_id'));
</script>

@endsection

@section('js')
<script>
    function showSwapLog(snId) {
        const logs = swapLogsData[snId] || [];
        let html = '';
        if (logs.length === 0) {
            html = '<div class="p-4 text-center text-muted">Belum ada riwayat swap</div>';
        } else {
            html = '<div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead class="table-light"><tr class="small text-muted"><th class="ps-3 py-2">SN Lama</th><th class="py-2">SN Baru</th><th class="py-2">Alasan</th><th class="py-2">Diubah Oleh</th><th class="py-2">Waktu</th></tr></thead><tbody>';
            logs.forEach(log => {
                html += `<tr>
                    <td class="ps-3"><code class="text-danger">${log.old_serial_number || '-'}</code></td>
                    <td><code class="text-success">${log.new_serial_number || '-'}</code></td>
                    <td class="small text-muted">${log.reason || '-'}</td>
                    <td class="small">${log.user ? log.user.name : '-'}</td>
                    <td class="small text-muted">${log.created_at}</td>
                </tr>`;
            });
            html += '</tbody></table></div>';
        }
        document.getElementById('logModalBody').innerHTML = html;
        new bootstrap.Modal(document.getElementById('logModal')).show();
    }

    document.getElementById('formSwapSN')?.addEventListener('submit', function(e) {
        e.preventDefault();

        // Cek apakah ada SN baru yang diisi
        let hasChanges = false;
        const inputs = this.querySelectorAll('input[name$="[new_serial_number]"]');
        inputs.forEach(inp => {
            if (inp.value.trim() !== '') hasChanges = true;
        });

        if (!hasChanges) {
            Swal.fire('Tidak ada perubahan', 'Silakan isi Serial Number baru untuk di-swap.', 'info');
            return;
        }

        const btn = document.getElementById('btnSave');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

        const formData = new FormData(this);

        $.ajax({
            url: this.action,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: res.message,
                        icon: 'success',
                        confirmButtonColor: '#405189'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-swap-line me-1"></i> Simpan Swap';
                }
            },
            error: function(xhr) {
                let msg = 'Server error. Coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-swap-line me-1"></i> Simpan Swap';
            }
        });
    });
</script>
@endsection
