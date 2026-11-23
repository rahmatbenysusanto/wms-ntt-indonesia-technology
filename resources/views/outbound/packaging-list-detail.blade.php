@extends('layout.index')
@section('title', 'Detail Packing List')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Detail Packing List</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('outbound.packaging-list') }}">Packing List</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Header Info --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Info Packing List</h4>
                <div class="d-flex gap-2">
                    @if ($packingList->status === 'draft')
                        <span class="badge bg-warning fs-6">Draft</span>
                    @else
                        <span class="badge bg-success fs-6">Released</span>
                        @if ($packingList->repacked_at)
                            <span class="badge bg-info fs-6">Repacked: {{ $packingList->koli_qty }} koli</span>
                        @endif
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="text-muted d-block">Doc No.</label>
                        <strong>{{ $packingList->doc_no ?? '-' }}</strong>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="text-muted d-block">Client</label>
                        <strong>{{ $packingList->customer->name ?? '-' }}</strong>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="text-muted d-block">Delivery Dest</label>
                        <strong>{{ $packingList->deliv_dest ?? '-' }}</strong>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="text-muted d-block">Ship Via</label>
                        <strong>{{ $packingList->ship_via ?? '-' }}</strong>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="text-muted d-block">QTY Item</label>
                        <strong>{{ $packingList->qty_item }}</strong>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="text-muted d-block">QTY Total</label>
                        <strong>{{ $packingList->qty }}</strong>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="text-muted d-block">Created By</label>
                        <strong>{{ $packingList->createdBy->name ?? '-' }}</strong>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="text-muted d-block">Created At</label>
                        <strong>{{ $packingList->created_at->format('d M Y H:i') }}</strong>
                    </div>
                    <div class="col-12 mb-2">
                        <label class="text-muted d-block">Note</label>
                        <strong>{{ $packingList->note ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Items + Repack dalam satu tabel --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Item Packing</h4>
                @if (in_array($packingList->status, ['draft', 'released']))
                    <button type="button" class="btn btn-info btn-sm" onclick="openRepack()">
                        <i class="mdi mdi-package-variant"></i> {{ $packingList->repacked_at ? 'Edit Repack' : 'Input Repack' }}
                    </button>
                @endif
            </div>
            <div class="card-body">
                @php
                    $groups = collect();
                    if ($packingList->repacks->isNotEmpty()) {
                        $firstRepack = $packingList->repacks->first();
                        $unassigned = $packingList->details->whereNull('packing_list_repack_id')->values();

                        foreach ($packingList->repacks as $repack) {
                            $rows = $packingList->details->where('packing_list_repack_id', $repack->id)->values();
                            if ($repack->is($firstRepack)) {
                                $rows = $rows->concat($unassigned);
                            }
                            if ($rows->isNotEmpty()) {
                                $groups->push(['repack' => $repack, 'rows' => $rows]);
                            }
                        }
                    }
                @endphp
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Material / Item</th>
                                <th>Part Number</th>
                                <th class="text-center">Qty</th>
                                @if ($groups->isNotEmpty())
                                    <th class="text-center">Koli</th>
                                    <th class="text-center">Qty Box</th>
                                    <th class="text-end">Gross Weight</th>
                                    <th class="text-end">Measurement</th>
                                    <th>Note</th>
                                @else
                                    <th class="text-end">Gross Weight</th>
                                    <th class="text-end">Measurement</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if ($groups->isNotEmpty())
                                @foreach ($groups as $group)
                                    @foreach ($group['rows'] as $i => $detail)
                                    <tr>
                                        @if ($i === 0)
                                            <td rowspan="{{ $group['rows']->count() }}">{{ $loop->parent->iteration }}</td>
                                        @endif
                                        <td>
                                            <strong>{{ $detail->material }}</strong><br>
                                            <small class="text-muted">{{ $detail->item }} — {{ $detail->po_item_desc }}</small>
                                        </td>
                                        <td>{{ $detail->part_number ?? '-' }}</td>
                                        <td class="text-center">{{ $detail->qty }}</td>
                                        @if ($i === 0)
                                            <td class="text-center" rowspan="{{ $group['rows']->count() }}">
                                                <span class="badge bg-info">{{ $group['repack']->koli_no }}</span>
                                            </td>
                                            <td class="text-center" rowspan="{{ $group['rows']->count() }}">{{ $group['repack']->qty_box ?? 1 }}</td>
                                            <td class="text-end" rowspan="{{ $group['rows']->count() }}">{{ number_format((float) $group['repack']->weight, 2, ',', '.') }}</td>
                                            <td class="text-end" rowspan="{{ $group['rows']->count() }}">{{ $group['repack']->length }} x {{ $group['repack']->width }} x {{ $group['repack']->height }}</td>
                                            <td rowspan="{{ $group['rows']->count() }}">{{ $group['repack']->note ?? '-' }}</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                @endforeach
                            @else
                                @forelse ($packingList->details as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $detail->material }}</strong><br>
                                        <small class="text-muted">{{ $detail->item }} — {{ $detail->po_item_desc }}</small>
                                    </td>
                                    <td>{{ $detail->part_number ?? '-' }}</td>
                                    <td class="text-center">{{ $detail->qty }}</td>
                                    <td class="text-end">{{ number_format((float) $detail->gross_weight, 2, ',', '.') }}</td>
                                    <td class="text-end">{{ $detail->measurement }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No item data</td>
                                </tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($groups->isNotEmpty())
                <div class="text-end fw-bold mt-2">
                    Total {{ $packingList->repacks->count() }} koli — {{ number_format((float) $packingList->repacks->sum('weight'), 2, ',', '.') }} kg
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12">
        <a href="{{ route('outbound.packaging-list') }}" class="btn btn-light">
            <i class="mdi mdi-arrow-left"></i> Kembali
        </a>
        @if ($packingList->repacked_at)
        <a href="{{ route('outbound.packaging-list.download-pdf', ['id' => $packingList->id, 'mode' => 'final']) }}"
           target="_blank" class="btn btn-dark">
            <i class="mdi mdi-file-pdf-box"></i> PDF Final (Setelah Repack)
        </a>
        @endif
    </div>
</div>

{{-- Repack Modal --}}
<div id="repackModal" class="modal fade" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Repack Data (Koli Final)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Input per koli. Pilih item parent yang di-merge ke koli ini, isi QTY Box, dimensi & berat hasil repack.</span>
                    <button type="button" class="btn btn-success btn-sm" onclick="addRepackRow()">
                        <i class="mdi mdi-plus"></i> Tambah Koli
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th style="width:110px">Koli No.</th>
                                <th style="min-width:280px">Items (parent yang di-merge)</th>
                                <th style="width:80px">Qty Box</th>
                                <th>Length</th>
                                <th>Width</th>
                                <th>Height</th>
                                <th>Weight</th>
                                <th>Note</th>
                                <th style="width:40px"></th>
                            </tr>
                        </thead>
                        <tbody id="repackRows"></tbody>
                    </table>
                </div>
                <div class="text-muted small">Item yang belum dipilih di koli mana pun akan tampil di tabel terpisah pada PDF Final.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveRepack()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    let repackModalInstance = null;
    @php
        $repackJson = $packingList->repacks->map(fn ($r) => [
            'koli_no' => $r->koli_no,
            'qty_box' => $r->qty_box,
            'length' => $r->length,
            'width' => $r->width,
            'height' => $r->height,
            'weight' => $r->weight,
            'note' => $r->note,
            'details' => $r->details->map(fn ($d) => ['id' => $d->id])->values(),
        ])->values();
        $detailJson = $packingList->details->map(fn ($d) => [
            'id' => $d->id,
            'label' => ($d->part_number ?: $d->material) . ' — ' . $d->po_item_desc,
        ])->values();
    @endphp
    const existingRepacks = @json($repackJson);
    let allDetails = @json($detailJson);

    function rowTemplate() {
        const opts = allDetails.map(d => `<option value="${d.id}">${d.label}</option>`).join('');
        return `
            <tr>
                <td><input type="text" class="form-control form-control-sm koli-no" placeholder="Koli 1"></td>
                <td><select class="form-select form-select-sm koli-items" multiple style="min-width:260px">${opts}</select></td>
                <td><input type="number" min="1" class="form-control form-control-sm koli-qty-box" value="1"></td>
                <td><input type="number" step="0.01" min="0.01" class="form-control form-control-sm koli-length" placeholder="0.00"></td>
                <td><input type="number" step="0.01" min="0.01" class="form-control form-control-sm koli-width" placeholder="0.00"></td>
                <td><input type="number" step="0.01" min="0.01" class="form-control form-control-sm koli-height" placeholder="0.00"></td>
                <td><input type="number" step="0.01" min="0.01" class="form-control form-control-sm koli-weight" placeholder="0.00"></td>
                <td><input type="text" class="form-control form-control-sm koli-note" placeholder="-"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                        <i class="mdi mdi-close"></i>
                    </button>
                </td>
            </tr>`;
    }

    function fillRow(row, data) {
        row.querySelector('.koli-no').value = data.koli_no ?? '';
        row.querySelector('.koli-qty-box').value = data.qty_box ?? 1;
        row.querySelector('.koli-length').value = data.length ?? '';
        row.querySelector('.koli-width').value = data.width ?? '';
        row.querySelector('.koli-height').value = data.height ?? '';
        row.querySelector('.koli-weight').value = data.weight ?? '';
        row.querySelector('.koli-note').value = data.note ?? '';

        const ids = (data.details ?? []).map(d => String(d.id));
        [...row.querySelector('.koli-items').options].forEach(o => {
            o.selected = ids.includes(o.value);
        });
    }

    function addRepackRow(data = {}) {
        const tbody = document.getElementById('repackRows');
        const tr = document.createElement('tr');
        tr.innerHTML = rowTemplate();
        fillRow(tr, data);
        tbody.appendChild(tr);
    }

    function openRepack() {
        document.getElementById('repackRows').innerHTML = '';
        if (existingRepacks.length) {
            existingRepacks.forEach(r => addRepackRow(r));
        } else {
            addRepackRow({ koli_no: 'Koli 1' });
        }
        if (!repackModalInstance) {
            repackModalInstance = new bootstrap.Modal(document.getElementById('repackModal'));
        }
        repackModalInstance.show();
    }

    function saveRepack() {
        const rows = [...document.querySelectorAll('#repackRows tr')].map(tr => ({
            koli_no: tr.querySelector('.koli-no').value || null,
            qty_box: tr.querySelector('.koli-qty-box').value || 1,
            detail_ids: [...tr.querySelector('.koli-items').selectedOptions].map(o => Number(o.value)),
            length: tr.querySelector('.koli-length').value,
            width: tr.querySelector('.koli-width').value,
            height: tr.querySelector('.koli-height').value,
            weight: tr.querySelector('.koli-weight').value,
            note: tr.querySelector('.koli-note').value || null,
        }));

        if (!rows.length) {
            Swal.fire({ title: 'Error', text: 'Minimal 1 koli', icon: 'error' });
            return;
        }
        for (const r of rows) {
            if (!r.length || !r.width || !r.height || !r.weight) {
                Swal.fire({ title: 'Error', text: 'Semua field dimensi & berat wajib diisi', icon: 'error' });
                return;
            }
        }

        $.ajax({
            url: '{{ route('outbound.packaging-list.repack') }}',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: {{ $packingList->id }},
                repacks: rows
            },
            success: (res) => {
                if (res?.status) {
                    Swal.fire({ title: 'Success', text: res.message, icon: 'success' })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ title: 'Error', text: res.message, icon: 'error' });
                }
            },
            error: (xhr) => Swal.fire({
                title: 'Error',
                text: xhr.responseJSON?.message ?? 'Request failed',
                icon: 'error'
            })
        });
    }
</script>
@endsection
