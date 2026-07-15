@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold">User Management</h1>
        <p class="text-muted">Manage system access and filter users by their specific academic roles.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-arrow-up me-2"></i> Import Records
        </button>
    </div>
</div>

<div class="mb-4">
    <div class="btn-group shadow-sm p-1 bg-white rounded-3">
        <a href="{{ route('admin.users', ['role' => 'all']) }}" class="btn {{ !request('role') || request('role') == 'all' ? 'btn-primary' : 'btn-light' }} px-4 fw-bold">All</a>
        <a href="{{ route('admin.users', ['role' => 2]) }}" class="btn {{ request('role') == 2 ? 'btn-primary' : 'btn-light' }} px-3 fw-bold">Faculty Heads</a>
        <a href="{{ route('admin.users', ['role' => 3]) }}" class="btn {{ request('role') == 3 ? 'btn-primary' : 'btn-light' }} px-3 fw-bold">Dept Heads</a>
        <a href="{{ route('admin.users', ['role' => 4]) }}" class="btn {{ request('role') == 4 ? 'btn-primary' : 'btn-light' }} px-3 fw-bold">Instructors</a>
        <a href="{{ route('admin.users', ['role' => 5]) }}" class="btn {{ request('role') == 5 ? 'btn-primary' : 'btn-light' }} px-3 fw-bold">Students</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm border-0 p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-muted small fw-bold">Full Name</th>
                    <th class="text-muted small fw-bold">ID Number</th>
                    <th class="text-muted small fw-bold">Role Assignment</th>
                    <th class="text-muted small fw-bold text-center">Status</th>
                    <th class="text-muted small fw-bold text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    {{-- Concatenating First and Last Name --}}
                    <td class="fw-bold text-dark">
                        {{ $user->first_name }} {{ $user->last_name }}
                        @if(!$user->first_name && !$user->last_name)
                            <span class="text-danger small fw-normal italic">Name Missing</span>
                        @endif
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $user->username }}</span></td>
                    <td>
                        @if($user->role_id == 5)
                            <div class="d-flex align-items-center text-muted">
                                <i class="bi bi-lock-fill me-2 small"></i>
                                <span class="small fw-bold text-uppercase">Student (Fixed Role)</span>
                            </div>
                        @else
                            <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <select name="role_id" onchange="this.form.submit()" class="form-select form-select-sm d-inline-block w-auto border-0 bg-light fw-bold">
                                    <option value="1" {{ $user->role_id == 1 ? 'selected' : '' }}>Admin</option>
                                    <option value="2" {{ $user->role_id == 2 ? 'selected' : '' }}>Faculty Head</option>
                                    <option value="3" {{ $user->role_id == 3 ? 'selected' : '' }}>Dept Head</option>
                                    <option value="4" {{ $user->role_id == 4 ? 'selected' : '' }}>Instructor</option>
                                </select>
                            </form>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $user->status == 'active' ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $user->status == 'active' ? 'text-success' : 'text-danger' }} border px-3 py-2 rounded-pill">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn {{ $user->status == 'active' ? 'btn-outline-danger' : 'btn-outline-success' }} btn-sm px-3 fw-bold">
                                {{ $user->status == 'active' ? 'Suspend' : 'Activate' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-person-exclamation fs-2 d-block mb-2"></i>
                        No users found for this category.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Updated Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Import Official Records</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 small mb-4">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Expected CSV Format:</strong><br>
                        <code>id_number, email, first_name, last_name, role_id, department_id</code>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-control shadow-sm" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm" style="background-color: #0056b3;">Start Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection