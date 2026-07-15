@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold">Department Management</h1>
        <p class="text-muted">Create and manage academic departments</p>
    </div>
    <button class="btn btn-primary px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addDeptModal" style="background-color: #0056b3;">
        <i class="bi bi-plus-lg me-2"></i> Add Department
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
@endif

<div class="card shadow-sm border-0 p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-muted small fw-bold">Code</th>
                    <th class="text-muted small fw-bold">Department Name</th>
                    <th class="text-muted small fw-bold">Faculty</th>
                    <th class="text-muted small fw-bold">Department Head</th>
                    <th class="text-muted small fw-bold text-center">Instructors</th>
                    <th class="text-muted small fw-bold text-center">Courses</th>
                    <th class="text-muted small fw-bold text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                <tr>
                    <td><span class="badge bg-info bg-opacity-10 text-info border border-info px-2 py-1">{{ $dept->code }}</span></td>
                    <td class="fw-bold">{{ $dept->name }}</td>
                    <td>
                        @if($dept->faculty)
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1">{{ $dept->faculty->name }}</span>
                        @else
                            <span class="text-muted small italic">Not Assigned</span>
                        @endif
                    </td>
                    <td class="text-dark">
                        @if($dept->head)
                            <i class="bi bi-person-badge me-1 text-muted"></i>
                            {{ $dept->head->first_name }} {{ $dept->head->last_name }}
                        @else
                            <span class="text-muted small italic">Not Assigned</span>
                        @endif
                    </td>
                    {{-- Updated: Using dynamic users_count from withCount() --}}
                    <td class="text-center">
                        <span class="badge rounded-pill bg-light text-dark border">{{ $dept->users_count ?? 0 }}</span>
                    </td>
                    {{-- Updated: Using dynamic courses_count from withCount() --}}
                    <td class="text-center">
                        <span class="badge rounded-pill bg-light text-dark border">{{ $dept->courses_count ?? 0 }}</span>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-outline-secondary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editDept{{ $dept->id }}">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <form action="{{ route('admin.departments.destroy', $dept) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this department?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>

                <div class="modal fade" id="editDept{{ $dept->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-0">
                                <h5 class="modal-title fw-bold">Edit Department</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.departments.update', $dept) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="modal-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold small">Code</label>
                                            <input type="text" name="code" class="form-control" value="{{ $dept->code }}" required>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-bold small">Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $dept->name }}" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold small">Faculty</label>
                                            <select name="faculty_id" class="form-select shadow-sm" required>
                                                <option value="" disabled>-- Select Faculty --</option>
                                                @foreach($faculties as $faculty)
                                                    <option value="{{ $faculty->id }}" {{ $dept->faculty_id == $faculty->id ? 'selected' : '' }}>
                                                        {{ $faculty->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold small">Department Head</label>
                                            <select name="user_id" class="form-select shadow-sm">
                                                <option value="">-- No Head Assigned --</option>
                                                @foreach($deptHeads as $head)
                                                    <option value="{{ $head->id }}" {{ $dept->user_id == $head->id ? 'selected' : '' }}>
                                                        {{ $head->first_name }} {{ $head->last_name }} ({{ $head->username }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="submit" class="btn btn-primary w-100 fw-bold" style="background-color: #0056b3;">Update Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-building-exclamation fs-2 d-block mb-2"></i>
                        No departments found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addDeptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Add New Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.departments.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Code</label>
                            <input type="text" name="code" class="form-control shadow-sm" placeholder="e.g. CS" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small">Department Name</label>
                            <input type="text" name="name" class="form-control shadow-sm" placeholder="e.g. Computer Science" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Faculty</label>
                            <select name="faculty_id" class="form-select shadow-sm" required>
                                <option value="" selected disabled>-- Select Faculty --</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Assign Department Head</label>
                            <select name="user_id" class="form-select shadow-sm">
                                <option value="" selected>-- Select Head (Optional) --</option>
                                @foreach($deptHeads as $head)
                                    <option value="{{ $head->id }}">
                                        {{ $head->first_name }} {{ $head->last_name }} ({{ $head->username }})
                                    </option>
                                @endforeach
                            </select>
                            @if($deptHeads->isEmpty())
                                <div class="text-danger small mt-2">
                                    <i class="bi bi-exclamation-circle me-1"></i> No users with Role ID 3 found.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" style="background-color: #0056b3;">Save Department</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection