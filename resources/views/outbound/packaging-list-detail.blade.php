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

    {{-- Items --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Item Packing</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Material / Item</th>
                                <th>Part Number</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Gross Weight</th>
                                <th class="text-end">Measurement</th>
                            </tr>
                        </thead>
                        <tbody>
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
                                <td class="text-end">{{ number_format((float) $detail->measurement, 2, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No item data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Repack --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Repack (Koli Final)</h4>
                @if (in_array($packingList->status, ['draft', 'released']))
                    <button type="button" class="btn btn-info btn-sm" onclick="openRepack()">
                        <i class="mdi mdi-package-variant"></i> {{ $packingList->repacked_at ? 'Edit Repack' : 'Input Repack' }}
                    </button>
                @endif
            </div>
            <div class="card-body">
                @if ($packingList->repacks->count())
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Koli No.</th>
                                <th class="text-end">Length (cm)</th>
                                <th class="text-end">Width (cm)</th>
                                <th class="text-end">Height (cm)</th>
                                <th class="text-end">Weight (kg)</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packingList->repacks as $repack)
                            <tr>
                                <td>{{ $repack->koli_no }}</td>
                                <td class="text-end">{{ number_format((float) $repack->length, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format((float) $repack->width, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format((float) $repack->height, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format((float) $repack->weight, 2, ',', '.') }}</td>
                                <td>{{ $repack->note ?? '-' }}</td>
                            </tr>
                            @endforeach
                            <tr class="fw-bold">
                                <td>TOTAL ({{ $packingList->koli_qty }} koli)</td>
                                <td colspan="3"></td>
                                <td class="text-end">{{ number_format((float) $packingList->repacks->sum('weight'), 2, ',', '.') }} kg</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="mdi mdi-package-variant-closed fs-3 d-block mb-2"></i>
                    Belum ada data repack.
                    Klik tombol <strong>Input Repack</strong> di atas.
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Repack Data (Koli Final)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Input per koli (dimensi & berat masing-masing koli)</span>
                    <button type="button" class="btn btn-success btn-sm" onclick="addRepackRow()">
                        <i class="mdi mdi-plus"></i> Tambah Koli
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th style="width:130px">Koli No.</th>
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
    const existingRepacks = @json($packingList->repacks);

    function rowTemplate(koli = '') {
        return `
            <tr>
                <td><input type="text" class="form-control form-control-sm koli-no" value="${koli}" placeholder="Koli 1"></td>
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
        row.querySelector('.koli-length').value = data.length ?? '';
        row.querySelector('.koli-width').value = data.width ?? '';
        row.querySelector('.koli-height').value = data.height ?? '';
        row.querySelector('.koli-weight').value = data.weight ?? '';
        row.querySelector('.koli-note').value = data.note ?? '';
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
