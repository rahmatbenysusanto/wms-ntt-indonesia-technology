@extends('layout.index')
@section('title', 'Storage Rak')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Storage Rak</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a>Storage</a></li>
                        <li class="breadcrumb-item active">Storage Rak</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Storage Rak</h4>
                        <a class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRak">Create Rak</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Raw</th>
                            <th>Area</th>
                            <th>Rak</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($storage as $index => $item)
                            <tr>
                                <td>{{ $storage->firstItem() + $index }}</td>
                                <td>{{ $item->raw }}</td>
                                <td>{{ $item->area }}</td>
                                <td>{{ $item->rak }}</td>
                                <td>
                                    <a class="btn btn-danger btn-sm" onclick="deleteStorage(`{{ $item->id }}`)">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end mt-2">
                        @if ($storage->hasPages())
                            <ul class="pagination">
                                @if ($storage->onFirstPage())
                                    <li class="disabled"><span>&laquo; Previous</span></li>
                                @else
                                    <li><a href="{{ $storage->previousPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="prev">&laquo; Previous</a></li>
                                @endif

                                @foreach ($storage->links()->elements as $element)
                                    @if (is_string($element))
                                        <li class="disabled"><span>{{ $element }}</span></li>
                                    @endif

                                    @if (is_array($element))
                                        @foreach ($element as $page => $url)
                                            @if ($page == $storage->currentPage())
                                                <li class="active"><span>{{ $page }}</span></li>
                                            @else
                                                <li><a href="{{ $url }}&per_page={{ request('per_page', 10) }}">{{ $page }}</a></li>
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach

                                @if ($storage->hasMorePages())
                                    <li><a href="{{ $storage->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" rel="next">Next &raquo;</a></li>
                                @else
                                    <li class="disabled"><span>Next &raquo;</span></li>
                                @endif
                            </ul>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="addRak" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Add Rak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('storage.create') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="rak">
                        <div class="mb-3">
                            <label class="form-label">Raw Name</label>
                            <select class="form-control" name="raw" onchange="changeRaw('rak', this.value)">
                                <option>-- Select Area --</option>
                                @foreach($raw as $item)
                                    <option value="{{ $item->raw }}">{{ $item->raw }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Area Name</label>
                            <select class="form-control" name="area" id="formRak_area">
                                <option>-- Select Area --</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rak Name</label>
                            <input type="text" class="form-control" name="rak" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function changeRaw(type, value) {
            $.ajax({
                url: '{{ route('storage.find.area') }}',
                method: 'GET',
                data: {
                    raw: value
                },
                success: (res) => {
                    const data = res.data;

                    let html = '<option value="">-- Select Area --</option>';

                    data.forEach((item) => {
                        html += `<option value="${item.area}">${item.area}</option>`;
                    });

                    if (type === 'rak') {
                        document.getElementById('formRak_area').innerHTML = html;
                    } else {
                        document.getElementById('formBin_area').innerHTML = html;
                    }
                }
            });
        }

        function changeArea(value) {
            console.info(document.getElementById('formBin_raw').value);
            console.info(value);

            $.ajax({
                url: '{{ route('storage.find.rak') }}',
                method: 'GET',
                data: {
                    raw: document.getElementById('formBin_raw').value,
                    area: value
                },
                success: (res) => {
                    const data = res.data;

                    let html = '<option value="">-- Select Rak --</option>';

                    data.forEach((item) => {
                        html += `<option value="${item.id}">${item.rak}</option>`;
                    });

                    document.getElementById('formBin_rak').innerHTML = html;
                }
            });
        }

        function deleteStorage(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "Delete Storage",
                icon: "warning",
                showCancelButton: true,
                customClass: {
                    confirmButton: "btn btn-primary w-xs me-2 mt-2",
                    cancelButton: "btn btn-danger w-xs mt-2"
                },
                confirmButtonText: "Yes, Delete it!",
                buttonsStyling: false,
                showCloseButton: true
            }).then(function(t) {
                if (t.value) {

                    $.ajax({
                        url: '{{ route('storage.delete') }}',
                        method: 'GET',
                        data: {
                            id: id
                        },
                        success: (res => {
                            Swal.fire({
                                title: 'Success',
                                text: 'Delete Storage Success',
                                icon: 'success'
                            }).then((i) => {
                                window.location.reload();
                            });
                        })
                    });

                }
            });
        }
    </script>
@endsection
