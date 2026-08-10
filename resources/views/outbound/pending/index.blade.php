@extends('layout.index')
@section('title', 'Pending Outbound')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Pending Outbound List</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Outbound</a></li>
                        <li class="breadcrumb-item active">Pending List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Pending Outbound List</h4>
                        <a href="{{ route('outbound.pending.create') }}" class="btn btn-primary btn-sm">Create Pending</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Purc Doc</label>
                                <input type="text" class="form-control" name="purcDoc"
                                    value="{{ request()->get('purcDoc') }}" placeholder="Purc Doc ...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Client</label>
                                <select class="form-control select2Client" name="client">
                                    <option value="">-- Select Client --</option>
                                    @foreach ($customer as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $item->id == request()->get('client') ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Purc Doc</th>
                                    <th>Client</th>
                                    <th>Sales Doc</th>
                                    <th>QTY Item</th>
                                    <th>QTY Total</th>
                                    <th>Delivery Dest</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingOutbounds as $index => $item)
                                    <tr>
                                        <td>{{ $pendingOutbounds->firstItem() + $index }}</td>
                                        <td>{{ $item->purc_doc ?? '-' }}</td>
                                        <td>{{ $item->customer->name ?? '-' }}</td>
                                        <td>
                                            @php
                                                $salesDocs = is_string($item->sales_docs) ? json_decode($item->sales_docs, true) : $item->sales_docs;
                                            @endphp
                                            @if (is_array($salesDocs))
                                                @foreach ($salesDocs as $sd)
                                                    <span class="badge bg-primary mb-1">{{ $sd }}</span><br>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>{{ $item->qty_item }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>{{ $item->deliv_dest ?? '-' }}</td>
                                        <td>{{ $item->createdBy->name ?? '-' }}</td>
                                        <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a class="btn btn-success btn-sm"
                                                    onclick="convertPending({{ $item->id }})"
                                                    title="Convert to Outbound">
                                                    <i class="mdi mdi-transfer-right"></i> Convert
                                                </a>
                                                <a class="btn btn-warning btn-sm"
                                                    href="{{ route('outbound.pending.edit', ['id' => $item->id]) }}"
                                                    title="Edit">
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </a>
                                                <a class="btn btn-info btn-sm" onclick="viewDetail({{ $item->id }})"
                                                    title="View Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a class="btn btn-danger btn-sm"
                                                    onclick="cancelPending({{ $item->id }})"
                                                    title="Cancel">
                                                    <i class="mdi mdi-delete"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="mdi mdi-clipboard-text-outline fs-3 d-block mb-2"></i>
                                                No pending outbound data
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $pendingOutbounds->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pending Outbound Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailModalBody">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2Client').select2({
                placeholder: '-- Select Client --',
                allowClear: true,
                width: '100%'
            });
        });

        function viewDetail(id) {
            $.ajax({
                url: '{{ route('outbound.pending.detail') }}',
                method: 'GET',
                data: { id },
                success: (res) => {
                    if (res?.status) {
                        const d = res.data;
                        let detailHtml = `
                            <div class="row mb-3">
                                <div class="col-6"><b>Purc Doc:</b> ${d.purc_doc ?? '-'}</div>
                                <div class="col-6"><b>Client:</b> ${d.customer?.name ?? '-'}</div>
                                <div class="col-6"><b>Delivery Dest:</b> ${d.deliv_dest ?? '-'}</div>
                                <div class="col-6"><b>Delivery Date:</b> ${d.delivery_date ?? '-'}</div>
                                <div class="col-6"><b>DN Number:</b> ${d.delivery_note_number ?? '-'}</div>
                                <div class="col-6"><b>NTT DN:</b> ${d.ntt_dn ?? '-'}</div>
                                <div class="col-6"><b>Koli:</b> ${d.koli ?? '-'}</div>
                                <div class="col-6"><b>Status:</b> <span class="badge bg-warning">${d.status}</span></div>
                            </div>
                            <hr>
                            <h6>Items:</h6>
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sales Doc</th>
                                        <th>Material</th>
                                        <th>Item</th>
                                        <th>Description</th>
                                        <th>QTY</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        (d.details ?? []).forEach(item => {
                            detailHtml += `
                                <tr>
                                    <td>${item.sales_doc ?? '-'}</td>
                                    <td>${item.material ?? '-'}</td>
                                    <td>${item.item ?? '-'}</td>
                                    <td>${item.po_item_desc ?? '-'}</td>
                                    <td>${item.qty ?? 0}</td>
                                </tr>
                            `;
                        });
                        detailHtml += '</tbody></table>';
                        $('#detailModalBody').html(detailHtml);
                        $('#detailModal').modal('show');
                    }
                },
                error: () => Swal.fire({ title: 'Error', text: 'Failed to load detail', icon: 'error' })
            });
        }

        function cancelPending(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Cancel this pending outbound?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Cancel it!',
                cancelButtonText: 'No'
            }).then((result) => {
                if (!result.value) return;
                $.ajax({
                    url: '{{ route('outbound.pending.destroy') }}',
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id
                    },
                    success: (res) => {
                        if (res?.status) {
                            Swal.fire({ title: 'Success', text: res.message, icon: 'success' })
                                .then(() => location.reload());
                        } else {
                            Swal.fire({ title: 'Error', text: res.message, icon: 'error' });
                        }
                    },
                    error: () => Swal.fire({ title: 'Error', text: 'Request failed', icon: 'error' })
                });
            });
        }

        function convertPending(id) {
            Swal.fire({
                title: 'Convert to Outbound?',
                text: 'You will be redirected to the outbound creation page with the data pre-filled.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes, Convert!',
                cancelButtonText: 'No'
            }).then((result) => {
                if (!result.value) return;
                $.ajax({
                    url: '{{ route('outbound.pending.convert') }}',
                    method: 'GET',
                    data: { id },
                    success: (res) => {
                        if (res?.status) {
                            // Store convert data in sessionStorage and redirect to outbound create
                            sessionStorage.setItem('pendingOutboundConvert', JSON.stringify(res.data));
                            window.location.href = '{{ route('outbound.create') }}?pending_id=' + id;
                        } else {
                            Swal.fire({ title: 'Error', text: res.message, icon: 'error' });
                        }
                    },
                    error: () => Swal.fire({ title: 'Error', text: 'Request failed', icon: 'error' })
                });
            });
        }
    </script>
@endsection
