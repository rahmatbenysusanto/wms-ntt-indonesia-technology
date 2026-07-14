@extends('layout.index')
@section('title', 'Swap Serial Number')

@section('content')
<style>
    .sn-result-card { background: #f8f9fa; border-radius: 8px; }
    .sn-result-card .label { font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: .5px; }
    .sn-result-card .value { font-size: 14px; font-weight: 600; }
</style>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Swap Serial Number (RMA)</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item active">Swap SN</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- Tombol Aksi --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold mb-1">Riwayat Swap Serial Number</h5>
                    <p class="text-muted small mb-0">
                        <i class="ri-information-line me-1"></i>
                        Kelola pergantian serial number (RMA). Klik <strong>Buat Swap Baru</strong> untuk mengganti SN.
                    </p>
                </div>
                <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#createSwapModal">
                    <i class="ri-swap-line me-1"></i> Buat Swap Baru
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('inventory.swap-sn') }}" class="row g-2 align-items-end">
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
                        <label class="form-label small text-muted fw-semibold">SN Lama</label>
                        <input type="text" name="oldSn" class="form-control form-control-sm"
                            placeholder="Cari SN lama..." value="{{ request('oldSn') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted fw-semibold">SN Baru</label>
                        <input type="text" name="newSn" class="form-control form-control-sm"
                            placeholder="Cari SN baru..." value="{{ request('newSn') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted fw-semibold">Tanggal</label>
                        <input type="date" name="date_range" class="form-control form-control-sm"
                            value="{{ request('date_range') }}">
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ri-search-line me-1"></i> Cari
                            </button>
                            <a href="{{ route('inventory.swap-sn') }}" class="btn btn-light btn-sm">
                                <i class="ri-refresh-line me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Riwayat --}}
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
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
                                            <span class="small fw-semibold">{{ $log->inventoryPackageItem->purchaseOrderDetail->material }}</span>
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
                                        <i class="ri-inbox-line display-5 text-muted d-block mb-3"></i>
                                        <p class="mb-0 fw-semibold">Belum ada riwayat swap</p>
                                        <p class="small">Klik tombol <strong>Buat Swap Baru</strong> untuk memulai.</p>
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

{{-- Modal Create Swap --}}
<div class="modal fade" id="createSwapModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white"><i class="ri-swap-line me-2"></i>Buat Swap Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Step 1: Cari SN --}}
                <div id="step1">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Serial Number yang Akan Di-swap</label>
                        <div class="input-group">
                            <input type="text" id="inputFindSN" class="form-control form-control-lg"
                                placeholder="Masukkan serial number..." autocomplete="off">
                            <button type="button" class="btn btn-primary px-4" id="btnFindSN">
                                <i class="ri-search-line me-1"></i> Cari
                            </button>
                        </div>
                        <div class="form-text">Masukkan SN yang ada di sistem, detail barang akan muncul untuk validasi.</div>
                    </div>

                    <div id="loadingSN" class="text-center py-3 d-none">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted small mt-2 mb-0">Mencari serial number...</p>
                    </div>

                    <div id="errorSN" class="alert alert-danger d-none py-2"></div>

                    {{-- Hasil Detail Barang --}}
                    <div id="resultSN" class="d-none">
                        <hr class="my-3">
                        <h6 class="fw-bold text-primary mb-3"><i class="ri-checkbox-circle-line me-1"></i> Detail Barang Ditemukan</h6>
                        <div class="sn-result-card p-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="label">Serial Number</div>
                                    <div class="value"><code id="detailSerialNumber" class="text-dark fs-14"></code></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="label">Material</div>
                                    <div class="value" id="detailMaterial"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="label">Deskripsi</div>
                                    <div class="value" id="detailDesc"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="label">PA Number (Box)</div>
                                    <div class="value" id="detailPaNumber"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="label">PO Number</div>
                                    <div class="value" id="detailPurcDoc"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="label">Customer</div>
                                    <div class="value" id="detailCustomer"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="label">Sales Doc</div>
                                    <div class="value" id="detailSalesDoc"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="label">Storage Location</div>
                                    <div class="value" id="detailStorage"></div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">
                        {{-- Step 2: Form Swap --}}
                        <form id="formSwapSN" action="{{ route('inventory.swap-sn.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="sn_id" id="formSnId">
                            <input type="hidden" name="old_serial_number" id="formOldSN">
                            <input type="hidden" name="inventory_package_item_id" id="formPackageItemId">
                            <input type="hidden" name="inventory_package_id" id="formPackageId">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Serial Number Baru <span class="text-danger">*</span></label>
                                    <input type="text" name="new_serial_number" id="formNewSN"
                                        class="form-control form-control-lg"
                                        placeholder="Masukkan SN pengganti" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Alasan Swap (RMA)</label>
                                    <input type="text" name="reason" id="formReason"
                                        class="form-control form-control-lg"
                                        placeholder="Contoh: RMA - unit defect">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="modalFooter">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary d-none" id="btnSubmitSwap">
                    <i class="ri-swap-line me-1"></i> Konfirmasi Swap
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        const inputFindSN = $('#inputFindSN');
        const btnFindSN = $('#btnFindSN');
        const loadingSN = $('#loadingSN');
        const errorSN = $('#errorSN');
        const resultSN = $('#resultSN');
        const btnSubmitSwap = $('#btnSubmitSwap');

        // Cari SN via AJAX
        function findSerialNumber() {
            const sn = inputFindSN.val().trim();
            if (!sn) {
                errorSN.removeClass('d-none').text('Silakan masukkan serial number.');
                return;
            }

            errorSN.addClass('d-none');
            resultSN.addClass('d-none');
            btnSubmitSwap.addClass('d-none');
            loadingSN.removeClass('d-none');

            $.ajax({
                url: '{{ route('inventory.swap-sn.find-sn') }}',
                method: 'GET',
                data: { serial_number: sn },
                success: function (res) {
                    loadingSN.addClass('d-none');
                    if (res.status) {
                        const d = res.data;
                        // Isi detail
                        $('#detailSerialNumber').text(d.serial_number);
                        $('#detailMaterial').text(d.material);
                        $('#detailDesc').text(d.po_item_desc);
                        $('#detailPaNumber').text(d.pa_number);
                        $('#detailPurcDoc').text(d.purc_doc);
                        $('#detailCustomer').text(d.customer);
                        $('#detailSalesDoc').text(d.sales_doc);
                        $('#detailStorage').text(d.storage);
                        // Isi form
                        $('#formSnId').val(d.sn_id);
                        $('#formOldSN').val(d.serial_number);
                        $('#formPackageItemId').val(d.inventory_package_item_id);
                        $('#formPackageId').val(d.inventory_package_id);
                        $('#formNewSN').val('').focus();
                        $('#formReason').val('');

                        resultSN.removeClass('d-none');
                        btnSubmitSwap.removeClass('d-none');
                        errorSN.addClass('d-none');
                    } else {
                        errorSN.removeClass('d-none').text(res.message);
                    }
                },
                error: function () {
                    loadingSN.addClass('d-none');
                    errorSN.removeClass('d-none').text('Terjadi kesalahan server. Coba lagi.');
                }
            });
        }

        btnFindSN.on('click', findSerialNumber);
        inputFindSN.on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                findSerialNumber();
            }
        });

        // Submit swap
        btnSubmitSwap.on('click', function () {
            const newSN = $('#formNewSN').val().trim();
            if (!newSN) {
                Swal.fire('Perhatian', 'Silakan isi Serial Number baru.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Swap',
                html: `Yakin akan mengganti SN <strong>${$('#formOldSN').val()}</strong> menjadi <strong>${newSN}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Swap!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#405189',
                customClass: { confirmButton: 'btn btn-primary w-xs me-2 mt-2', cancelButton: 'btn btn-light w-xs mt-2' },
                buttonsStyling: false
            }).then((t) => {
                if (!t.value) return;

                const btn = btnSubmitSwap;
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

                $.ajax({
                    url: '{{ route('inventory.swap-sn.store') }}',
                    method: 'POST',
                    data: $('#formSwapSN').serialize(),
                    success: function (res) {
                        if (res.status) {
                            Swal.fire({
                                title: 'Berhasil!',
                                html: res.message,
                                icon: 'success',
                                confirmButtonColor: '#405189'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                            btn.prop('disabled', false).html('<i class="ri-swap-line me-1"></i> Konfirmasi Swap');
                        }
                    },
                    error: function (xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error. Coba lagi.';
                        Swal.fire('Error', msg, 'error');
                        btn.prop('disabled', false).html('<i class="ri-swap-line me-1"></i> Konfirmasi Swap');
                    }
                });
            });
        });

        // Reset modal ketika ditutup
        $('#createSwapModal').on('hidden.bs.modal', function () {
            inputFindSN.val('');
            errorSN.addClass('d-none');
            resultSN.addClass('d-none');
            btnSubmitSwap.addClass('d-none');
            loadingSN.addClass('d-none');
        });
    });
</script>
@endsection
