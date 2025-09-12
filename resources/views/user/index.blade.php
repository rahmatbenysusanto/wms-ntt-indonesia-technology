@extends('layout.index')
@section('title', 'User')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">User</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">User</a></li>
                        <li class="breadcrumb-item active">List User</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List User</h4>
                        <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">Create User</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th class="text-center">Role</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $index => $user)
                                <tr>
                                    <td>{{ $users->firstItem() + $index }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td class="text-center">
                                        @if($user->role == 'inbound')
                                            <span class="badge bg-success-subtle text-success">Inbound</span>
                                        @elseif($user->role == 'outbound')
                                            <span class="badge bg-warning-subtle text-warning">Outbound</span>
                                        @else
                                            <span class="badge bg-info-subtle text-info">Mobile</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d F Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('user.menu', ['id' => $user->id]) }}" class="btn btn-primary btn-sm">Menu</a>
                                            <a class="btn btn-info btn-sm" onclick="editUser('{{ $user->id }}')">Edit</a>
                                            <a class="btn btn-danger btn-sm" onclick="deleteUser('{{ $user->id }}')">Delete</a>
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

    <!-- Create User Modals -->
    <div id="createUserModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('user.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label required">Username</label>
                            <input type="text" class="form-control" name="username" required placeholder="Username">
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Name</label>
                            <input type="text" class="form-control" name="name" required placeholder="Name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Email</label>
                            <input type="email" class="form-control" name="email" required placeholder="admin@mail.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Role</label>
                            <select class="form-control" name="role" required>
                                <option value="">-- Select Role --</option>
                                <option value="inbound">Inbound</option>
                                <option value="outbound">Outbound</option>
                                <option value="mobile">Mobile</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Password</label>
                            <input type="text" class="form-control" name="password" required placeholder="********">
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary ">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editUserModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('user.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="idUser">
                        <div class="mb-3">
                            <label class="form-label required">Username</label>
                            <input type="text" class="form-control" name="username" id="username" required placeholder="Username">
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Name</label>
                            <input type="text" class="form-control" name="name" id="name" required placeholder="Name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Email</label>
                            <input type="email" class="form-control" name="email" id="email" required placeholder="admin@mail.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Role</label>
                            <select class="form-control" name="role" id="role" required>

                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Password</label>
                            <input type="text" class="form-control" name="password" required value="********">
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary ">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function editUser(id) {
            $.ajax({
                url: '{{ route('user.find') }}',
                method: 'GET',
                data: {
                    id: id
                },
                success: (res) => {
                    const data = res.data;

                    document.getElementById('username').value = data.username;
                    document.getElementById('name').value = data.name;
                    document.getElementById('email').value = data.email;
                    document.getElementById('role').innerHTML = `
                        <option value="inbound" ${data.role === 'inbound' ? 'selected' : ''}>Inbound</option>
                        <option value="outbound" ${data.role === 'outbound' ? 'selected' : ''}>Outbound</option>
                        <option value="mobile" ${data.role === 'mobile' ? 'selected' : ''}>Mobile</option>
                    `;
                    document.getElementById('idUser').innerHTML = data.id;

                    $('#editUserModal').modal('show');
                }
            });
        }

        function deleteUser(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "Delete User",
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
                        url: '{{ route('user.delete') }}',
                        method: 'GET',
                        data: {
                            id: id
                        },
                        success: (res => {
                            Swal.fire({
                                title: 'Success',
                                text: 'Delete User Success',
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
