@extends('layout.index')
@section('title', 'Edit Serial Number')

@section('content')
<style>
    .sn-row-na { background-color: #fff8f0; }
    .sn-input-new { transition: border-color 0.2s; }
    .sn-input-new:focus { border-color: #405189; box-shadow: 0 0 0 0.15rem rgba(64,81,137,.2); }
    .log-badge { font-size: 11px; }
    .sn-row-saved { background-color: #f0fff4 !important; }
</style>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit Serial Number — Box <span class="text-primary">{{ $inventoryPackage->number }}</span></h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('inventory.index') }}">Inventory</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('inventory.sn-update') }}">Update SN</a></li>
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
                        <div class="fw-semibold text-muted small mb-1">SN N/A</div>
                        <div class="fw-bold text-danger" id="naCountBadge">{{ $naCount }}</div>
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
                        Klik <strong>Simpan</strong> per baris untuk update satu SN saja, atau gunakan
                        <strong>Simpan Semua</strong> untuk update sekaligus.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Item Cards --}}
@foreach($items as $item)
    @php
        $snList = $item->inventoryPackageItemSn;
        $hasNa = $snList->contains(fn($s) => strtoupper(trim($s->serial_number)) === 'N/A');
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
                @if($hasNa)
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2" id="itemNaBadge_{{ $item->id }}">
                        <i class="ri-alert-line me-1"></i> Ada SN N/A
                    </span>
                @else
                    <span class="badge bg-success-subtle text-success px-3 py-2" id="itemNaBadge_{{ $item->id }}">
                        <i class="ri-checkbox-circle-line me-1"></i> Semua SN OK
                    </span>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted">
                            <th class="ps-3 py-2">#</th>
                            <th class="py-2">Serial Number Lama</th>
                            <th class="py-2" style="min-width:220px;">Serial Number Baru</th>
                            <th class="py-2">Catatan</th>
                            <th class="py-2 text-center">Aksi</th>
                            <th class="py-2 text-center">Log</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($snList as $idx => $sn)
                            @php $isNa = strtoupper(trim($sn->serial_number)) === 'N/A'; @endphp
                            <tr class="{{ $isNa ? 'sn-row-na' : '' }}" id="sn_row_{{ $sn->id }}">
                                <td class="ps-3 small text-muted">{{ $idx + 1 }}</td>
                                <td id="sn_old_display_{{ $sn->id }}">
                                    <code class="{{ $isNa ? 'text-danger fw-bold' : 'text-dark' }}">
                                        {{ $sn->serial_number }}
                                    </code>
                                    @if($isNa)
                                        <span class="badge bg-danger ms-1 small" style="font-size:9px;">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($isNa)
                                        <input type="text"
                                            id="sn_input_{{ $sn->id }}"
                                            class="form-control form-control-sm sn-input-new"
                                            placeholder="Masukkan SN baru...">
                                    @else
                                        <code class="text-muted small">Tidak berubah</code>
                                    @endif
                                </td>
                                <td>
                                    @if($isNa)
                                        <input type="text"
                                            id="sn_notes_{{ $sn->id }}"
                                            class="form-control form-control-sm"
                                            placeholder="Opsional..."
                                            style="min-width:130px;">
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($isNa)
                                        <button type="button"
                                            id="btn_save_{{ $sn->id }}"
                                            class="btn btn-primary btn-sm px-3"
                                            onclick="saveSingleSN({{ $sn->id }}, {{ $inventoryPackage->id }}, {{ $item->id }}, '{{ $sn->serial_number }}')">
                                            <i class="ri-save-line"></i> Simpan
                                        </button>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $logCount = $changeLogs->where('inventory_package_item_sn_id', $sn->id)->count();
                                    @endphp
                                    @if($logCount > 0)
                                        <button type="button"
                                            class="btn btn-link btn-sm text-info p-0 log-badge"
                                            id="logBtn_{{ $sn->id }}"
                                            onclick="showLog({{ $sn->id }})"
                                            title="Lihat {{ $logCount }} perubahan">
                                            <i class="ri-history-line"></i> <span id="logCount_{{ $sn->id }}">{{ $logCount }}</span>x
                                        </button>
                                    @else
                                        <span class="text-muted small" id="logBtn_{{ $sn->id }}">-</span>
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

@if($naCount > 0)
<div class="row mt-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-0 text-muted small">
                            <i class="ri-save-line me-1"></i>
                            Gunakan tombol <strong>Simpan</strong> per baris untuk update satu SN, atau <strong>Simpan Semua</strong> untuk update semua SN yang sudah diisi sekaligus.
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('inventory.sn-update') }}" class="btn btn-light px-4">
                            <i class="ri-arrow-left-line me-1"></i> Kembali
                        </a>
                        <button type="button" class="btn btn-primary px-4 fw-semibold" id="btnSaveAll" onclick="saveAllSN()">
                            <i class="ri-save-line me-1"></i> Simpan Semua
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-success border-0 shadow-sm">
    <i class="ri-checkbox-circle-line me-2"></i>
    Semua Serial Number di box ini sudah valid (tidak ada N/A). Tidak ada yang perlu diubah.
    <a href="{{ route('inventory.sn-update') }}" class="btn btn-sm btn-success ms-3">Kembali</a>
</div>
@endif

{{-- Log History Modal --}}
<div class="modal fade" id="logModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white"><i class="ri-history-line me-2"></i>Log Perubahan SN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="logModalBody"></div>
        </div>
    </div>
</div>

<script>
    // Log data dari server, bisa di-update client-side setelah save
    let logsData = @json($changeLogs->groupBy('inventory_package_item_sn_id'));
    let naCount = {{ $naCount }};

    function showLog(snId) {
        const logs = logsData[snId] || [];
        let html = '';
        if (logs.length === 0) {
            html = '<div class="p-4 text-center text-muted">Belum ada log perubahan</div>';
        } else {
            html = '<div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead class="table-light"><tr class="small text-muted"><th class="ps-3 py-2">SN Lama</th><th class="py-2">SN Baru</th><th class="py-2">Catatan</th><th class="py-2">Diubah Oleh</th><th class="py-2">Waktu</th></tr></thead><tbody>';
            logs.forEach(log => {
                html += `<tr>
                    <td class="ps-3"><code class="text-danger">${log.old_serial_number || '-'}</code></td>
                    <td><code class="text-success">${log.new_serial_number || '-'}</code></td>
                    <td class="small text-muted">${log.notes || '-'}</td>
                    <td class="small">${log.user ? log.user.name : '-'}</td>
                    <td class="small text-muted">${log.created_at}</td>
                </tr>`;
            });
            html += '</tbody></table></div>';
        }
        document.getElementById('logModalBody').innerHTML = html;
        new bootstrap.Modal(document.getElementById('logModal')).show();
    }

    // Simpan satu SN saja
    function saveSingleSN(snId, inventoryPackageId, itemId, oldSN) {
        const newSN = document.getElementById(`sn_input_${snId}`)?.value.trim();
        const notes = document.getElementById(`sn_notes_${snId}`)?.value.trim() || '';
        const btn = document.getElementById(`btn_save_${snId}`);

        if (!newSN) {
            Swal.fire('Peringatan', 'Masukkan Serial Number baru terlebih dahulu.', 'warning');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        $.ajax({
            url: '{{ route('inventory.sn-update.store') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                inventory_package_id: inventoryPackageId,
                sn_updates: {
                    [snId]: {
                        sn_id: snId,
                        new_serial_number: newSN,
                        old_serial_number: oldSN,
                        inventory_package_item_id: itemId,
                        notes: notes
                    }
                }
            },
            success: function(res) {
                if (res.status) {
                    markRowSaved(snId, newSN, oldSN, notes, itemId);
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `SN berhasil diupdate menjadi ${newSN}`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="ri-save-line"></i> Simpan';
                }
            },
            error: function() {
                Swal.fire('Error', 'Server error. Coba lagi.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-save-line"></i> Simpan';
            }
        });
    }

    // Update tampilan baris setelah berhasil disimpan
    function markRowSaved(snId, newSN, oldSN, notes, itemId) {
        const row = document.getElementById(`sn_row_${snId}`);
        if (!row) return;

        // Update tampilan SN lama → tampilkan SN baru
        document.getElementById(`sn_old_display_${snId}`).innerHTML =
            `<code class="text-success fw-bold">${newSN}</code>
             <span class="badge bg-success ms-1 small" style="font-size:9px;">Updated</span>`;

        // Hapus input, tombol simpan, ganti dengan tanda centang
        const cells = row.querySelectorAll('td');
        // Kolom SN Baru (index 2)
        cells[2].innerHTML = '<span class="text-muted small">—</span>';
        // Kolom Catatan (index 3)
        cells[3].innerHTML = notes ? `<span class="small text-muted">${notes}</span>` : '<span class="text-muted small">—</span>';
        // Kolom Aksi (index 4)
        cells[4].innerHTML = '<i class="ri-checkbox-circle-fill text-success fs-18"></i>';

        // Update log button
        const existingLogs = logsData[snId] || [];
        const now = new Date().toISOString().replace('T', ' ').slice(0, 19);
        existingLogs.unshift({
            old_serial_number: oldSN,
            new_serial_number: newSN,
            notes: notes,
            user: { name: '{{ Auth::user()->name ?? "Anda" }}' },
            created_at: now
        });
        logsData[snId] = existingLogs;
        document.getElementById(`logBtn_${snId}`).outerHTML =
            `<button type="button" class="btn btn-link btn-sm text-info p-0 log-badge" id="logBtn_${snId}" onclick="showLog(${snId})">
                <i class="ri-history-line"></i> <span id="logCount_${snId}">${existingLogs.length}</span>x
            </button>`;

        // Update row style
        row.classList.remove('sn-row-na');
        row.classList.add('sn-row-saved');

        // Decrement naCount global
        naCount--;
        document.getElementById('naCountBadge').textContent = naCount;
    }

    // Simpan semua SN yang sudah diisi
    function saveAllSN() {
        const inputs = document.querySelectorAll('[id^="sn_input_"]');
        const updates = {};
        let hasAny = false;

        inputs.forEach(input => {
            const snId = input.id.replace('sn_input_', '');
            const newSN = input.value.trim();
            if (!newSN) return;

            const notesEl = document.getElementById(`sn_notes_${snId}`);
            const row = document.getElementById(`sn_row_${snId}`);

            // Ambil data dari hidden attrs via data attribute di tombol
            const btn = document.getElementById(`btn_save_${snId}`);
            if (!btn) return;

            // Parse dari onclick attribute
            const onclickAttr = btn.getAttribute('onclick');
            const match = onclickAttr.match(/saveSingleSN\((\d+),\s*(\d+),\s*(\d+),\s*'([^']*)'\)/);
            if (!match) return;

            updates[snId] = {
                sn_id: snId,
                new_serial_number: newSN,
                old_serial_number: match[4],
                inventory_package_item_id: match[3],
                notes: notesEl ? notesEl.value.trim() : ''
            };
            hasAny = true;
        });

        if (!hasAny) {
            Swal.fire('Peringatan', 'Belum ada SN baru yang diisi.', 'warning');
            return;
        }

        const btn = document.getElementById('btnSaveAll');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

        $.ajax({
            url: '{{ route('inventory.sn-update.store') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                inventory_package_id: {{ $inventoryPackage->id }},
                sn_updates: updates
            },
            success: function(res) {
                if (res.status) {
                    // Mark semua baris yang disimpan
                    Object.keys(updates).forEach(snId => {
                        const u = updates[snId];
                        markRowSaved(parseInt(snId), u.new_serial_number, u.old_serial_number, u.notes, u.inventory_package_item_id);
                    });
                    Swal.fire({
                        title: 'Berhasil!',
                        text: res.message,
                        icon: 'success',
                        confirmButtonColor: '#405189'
                    });
                } else {
                    Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                }
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-save-line me-1"></i> Simpan Semua';
            },
            error: function() {
                Swal.fire('Error', 'Server error. Coba lagi.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-save-line me-1"></i> Simpan Semua';
            }
        });
    }
</script>
@endsection
