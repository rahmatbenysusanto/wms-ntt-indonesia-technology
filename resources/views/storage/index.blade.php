@extends('layout.index')
@section('title', 'Storage')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Storage</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Manajemen Gudang</a></li>
                        <li class="breadcrumb-item active">Storage</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-end gap-2">
                        <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRaw">Add Raw</a>
                        <a class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addArea">Add Area</a>
                        <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addRak">Add Rak</a>
                        <a class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addBin">Add Bin</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Raw</th>
                                    <th>Area</th>
                                    <th>Rak</th>
                                    <th>Bin</th>
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
                                    <td>{{ $item->bin }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a class="btn btn-info btn-sm">Edit</a>
                                            <a class="btn btn-danger btn-sm">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Raw Modals -->
    <div id="addRaw" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Add Raw</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('storage.create') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="raw">
                        <div class="mb-3">
                            <label class="form-label">Raw Name</label>
                            <input type="text" class="form-control" name="raw" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Area Modals -->
    <div id="addArea" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Add Rak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('storage.create') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="area">
                        <div class="mb-3">
                            <label class="form-label">Raw Name</label>
                            <select class="form-control" name="raw">
                                <option>-- Select Area --</option>
                                @foreach($raw as $item)
                                    <option value="{{ $item->raw }}">{{ $item->raw }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Area Name</label>
                            <input type="text" class="form-control" name="area" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Rak Modals -->
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

    <!-- Add Palet Modals -->
    <div id="addBin" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Add Bin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('customer.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="bin">
                        <div class="mb-3">
                            <label class="form-label">Raw Name</label>
                            <select class="form-control" name="raw" id="formBin_raw" onchange="changeRaw('bin', this.value)">
                                <option>-- Select Area --</option>
                                @foreach($raw as $item)
                                    <option value="{{ $item->raw }}">{{ $item->raw }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Area Name</label>
                            <select class="form-control" name="area" id="formBin_area" onchange="changeArea(this.value)">
                                <option>-- Select Area --</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rak Name</label>
                            <select class="form-control" name="rak" id="formBin_rak">
                                <option>-- Select Rak --</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bin Name</label>
                            <input type="text" class="form-control" name="bin" required>
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
    </script>
@endsection
