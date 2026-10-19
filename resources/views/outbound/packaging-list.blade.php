@extends('layout.index')
@section('title', 'Packing List')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Packing List</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Packing List</a></li>
                        <li class="breadcrumb-item active">List</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Packing List</h4>
                        <a href="{{ route('outbound.packaging-list.create') }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-plus"></i> Create Packing List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="row g-2">
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
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="status">
                                    <option value="">-- All Status --</option>
                                    <option value="draft" {{ request()->get('status') == 'draft' ? 'selected' : '' }}>Draft
                                    </option>
                                    <option value="released"
                                        {{ request()->get('status') == 'released' ? 'selected' : '' }}>Released</option>
                                    <option value="cancelled"
                                        {{ request()->get('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                                    <th>Doc No.</th>
                                    <th>Client</th>
                                    <th>Sales Doc</th>
                                    <th>QTY Item</th>
                                    <th>QTY Total</th>
                                    <th>Delivery Dest</th>
                                    <th class="text-center">Status</th>
                                    <th>Created By</th>
                                    <th>Updated By</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($packingLists as $index => $item)
                                    <tr>
                                        <td>{{ $packingLists->firstItem() + $index }}</td>
                                        <td>{{ $item->doc_no ?? '-' }}</td>
                                        <td>{{ $item->customer->name ?? '-' }}</td>
                                        <td>
                                            @php
                                                $salesDocs = is_string($item->sales_docs)
                                                    ? json_decode($item->sales_docs, true)
                                                    : $item->sales_docs;
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
                                        <td class="text-center">
                                            @php
                                                $badge = match ($item->status) {
                                                    'draft' => 'bg-warning',
                                                    'released' => 'bg-success',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badge }}">{{ ucfirst($item->status) }}</span>
                                        </td>
                                        <td>{{ $item->createdBy->name ?? '-' }}</td>
                                        <td>{{ $item->updatedBy->name ?? '-' }}</td>
                                        <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @if ($item->status === 'draft')
                                                    <a class="btn btn-warning btn-sm"
                                                        href="{{ route('outbound.packaging-list.edit', ['id' => $item->id]) }}"
                                                        title="Edit Draft">
                                                        <i class="mdi mdi-pencil"></i> Edit
                                                    </a>
                                                    <a class="btn btn-primary btn-sm"
                                                        href="{{ route('outbound.packaging-list.download-pdf', ['id' => $item->id]) }}"
                                                        target="_blank" title="Preview / Cetak PDF">
                                                        <i class="mdi mdi-file-pdf-box"></i> PDF
                                                    </a>
                                                    <a class="btn btn-success btn-sm"
                                                        onclick="releasePacking({{ $item->id }})" title="Release">
                                                        <i class="mdi mdi-check-circle"></i> Release
                                                    </a>
                                                    <a class="btn btn-danger btn-sm"
                                                        onclick="deletePacking({{ $item->id }})" title="Delete Draft">
                                                        <i class="mdi mdi-delete"></i>
                                                    </a>
                                                @else
                                                    <a class="btn btn-primary btn-sm"
                                                        href="{{ route('outbound.packaging-list.download-pdf', ['id' => $item->id]) }}"
                                                        target="_blank" title="Cetak / Download PDF">
                                                        <i class="mdi mdi-file-pdf-box"></i> PDF
                                                    </a>
                                                    <span class="text-muted small">
                                                        Released {{ $item->released_at?->format('d M Y H:i') ?? '-' }}
                                                        <br>by {{ $item->releasedBy->name ?? '-' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="mdi mdi-package-variant-closed fs-3 d-block mb-2"></i>
                                                No packing list data
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $packingLists->appends(request()->query())->links() }}
                    </div>
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

        function releasePacking(id) {
            Swal.fire({
                title: 'Release this Packing List?',
                text: 'After release, the draft can no longer be edited.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Release it!',
                cancelButtonText: 'No'
            }).then((result) => {
                if (!result.value) return;
                $.ajax({
                    url: '{{ route('outbound.packaging-list.release') }}',
                    method: 'POST',
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

        function deletePacking(id) {
            Swal.fire({
                title: 'Delete this Packing List draft?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete it!',
                cancelButtonText: 'No'
            }).then((result) => {
                if (!result.value) return;
                $.ajax({
                    url: '{{ route('outbound.packaging-list.destroy') }}',
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
    </script>
@endsection