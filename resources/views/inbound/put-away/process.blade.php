@extends('layout.index')
@section('title', 'Put Away')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Put Away process</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item active">Put Away process</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">List Item</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="text-center">Item</th>
                                    <th  class="text-center">Type</th>
                                    <th>Material</th>
                                    <th>Po Item Desc</th>
                                    <th>Prod Hierarchy Desc</th>
                                    <th class="text-center">QTY</th>
                                    <th>Data Storage Location</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $product['item'] }}</td>
                                    <td class="text-center"><span class="badge bg-info-subtle text-info">Parent</span></td>
                                    <td>{{ $product['sku'] }}</td>
                                    <td>{{ $product['name'] }}</td>
                                    <td>{{ $product['type'] }}</td>
                                    <td class="text-center fw-bold">{{ number_format($product['qty']) }}</td>
                                    <td>{{ $product['location'] }}</td>
                                    <td>
                                        @if($product['location'] == '')
                                            <a class="btn btn-info btn-sm" onclick="setLocation('{{ $product['id'] }}')">Set Location</a></td>
                                        @endif
                                </tr>
                                @foreach($product['child'] as $child)
                                    <tr>
                                        <td></td>
                                        <td class="text-center">{{ $child['item'] }}</td>
                                        <td class="text-center"></td>
                                        <td>{{ $child['sku'] }}</td>
                                        <td>{{ $child['name'] }}</td>
                                        <td>{{ $child['type'] }}</td>
                                        <td class="text-center fw-bold">{{ number_format($child['qty']) }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Set Location Modals -->
    <div id="setLocationModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Set Location Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('inbound.put-away.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="setLocationId">
                        <input type="hidden" name="number" value="{{ request()->get('number') }}">
                        <div class="mb-3">
                            <label class="form-label">Raw</label>
                            <select class="form-control" onchange="changeRaw(this.value)" id="raw">
                                <option value="">-- Select Raw --</option>
                                @foreach($storageRaw as $raw)
                                    <option value="{{ $raw->raw }}">{{ $raw->raw }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Area</label>
                            <select class="form-control" id="area" onchange="changeArea(this.value)">

                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rak</label>
                            <select class="form-control" id="rak" onchange="changeRak(this.value)">

                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bin</label>
                            <select class="form-control" id="bin" name="bin">

                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Set Location</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function setLocation(id) {
            document.getElementById('setLocationId').value = id;
            $('#setLocationModal').modal('show');
        }

        function changeRaw(raw) {
            $.ajax({
                url: '{{ route('storage.find.area') }}',
                method: 'GET',
                data: {
                    raw: raw
                },
                success: (res) => {
                    const data = res.data;
                    let html = '<option value="">-- Select Area --</option>';

                    data.forEach((item) => {
                        html += `<option value="${item.area}">${item.area}</option>`;
                    });

                    document.getElementById('area').innerHTML = html;
                }
            });
        }

        function changeArea(area) {
            $.ajax({
                url: '{{ route('storage.find.rak') }}',
                method: 'GET',
                data: {
                    raw: document.getElementById('raw').value,
                    area: area
                },
                success: (res) => {
                    const data = res.data;
                    let html = '<option value="">-- Select Rak --</option>';

                    data.forEach((item) => {
                        html += `<option value="${item.rak}">${item.rak}</option>`;
                    });

                    document.getElementById('rak').innerHTML = html;
                }
            });
        }

        function changeRak(rak) {
            $.ajax({
                url: '{{ route('storage.find.bin') }}',
                method: 'GET',
                data: {
                    raw: document.getElementById('raw').value,
                    area: document.getElementById('area').value,
                    rak: rak
                },
                success: (res) => {
                    console.log(document.getElementById('raw').value)
                    console.log(document.getElementById('area').value)
                    console.log(document.getElementById('rak').value)
                    const data = res.data;
                    console.log(data);
                    let html = '<option value="">-- Select Bin --</option>';

                    data.forEach((item) => {
                        html += `<option value="${item.id}">${item.bin}</option>`;
                    });

                    document.getElementById('bin').innerHTML = html;
                }
            });
        }
    </script>
@endsection
