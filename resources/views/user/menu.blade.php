@extends('layout.index')
@section('title', 'User Menu')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">User</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">User</a></li>
                        <li class="breadcrumb-item">List User</li>
                        <li class="breadcrumb-item active">Menu</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">User Information</h4>
                </div>
                <div class="card-body">
                    <table>
                        <tr>
                            <td class="fw-bold">Username</td>
                            <td class="fw-bold ps-2">:</td>
                            <td class="ps-1">{{ $user->username }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Name</td>
                            <td class="fw-bold ps-2">:</td>
                            <td class="ps-1">{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Email</td>
                            <td class="fw-bold ps-2">:</td>
                            <td class="ps-1">{{ $user->email }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Access Menu User</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Menu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($userHasMenu as $menu)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $menu->type }}</td>
                                    <td>{{ $menu->name }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            @if(count($menu->userHasMenu) != 0)
                                                <input class="form-check-input" type="checkbox" role="switch" onchange="changeMenu('disable', '{{ $menu->id }}')" checked>
                                            @else
                                                <input class="form-check-input" type="checkbox" role="switch" onchange="changeMenu('enable', '{{ $menu->id }}')">
                                            @endif
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
@endsection

@section('js')
    <script>
        function changeMenu(type, menuId) {
            $.ajax({
                url: '{{ route('user.menu.store') }}',
                method: 'POST',
                data:{
                    _token: '{{ @csrf_token() }}',
                    type: type,
                    menuId: menuId,
                    userId: '{{ $user->id }}'
                },
                success: (res) => {
                    console.log(res)
                }
            });
        }
    </script>
@endsection
