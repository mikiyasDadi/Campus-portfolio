@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Manage Instructors</h2>
        <p class="text-muted mb-0">
            Register and manage teaching staff for {{ auth()->user()->department->name ?? 'Information Technology' }}
        </p>
    </div>
    <div class="d-flex gap-2">
       <form action="{{ route('department.instructors.reset_all') }}" method="POST" 
             onsubmit="return confirm('Are you sure? This will clear all existing time preferences for ALL instructors.')">
            @csrf
            <button type="submit" class="btn btn-outline-danger px-4 shadow-sm">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset All Availability
            </button>
        </form>

        <button class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addInstructorModal">
            <i class="bi bi-person-plus-fill me-2"></i> Add Instructor
        </button>
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

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Employee ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Dept Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($instructors as $instructor)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-primary">{{ $instructor->username ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $instructor->first_name }} {{ $instructor->last_name }}</div>
                        </td>
                        <td>{{ $instructor->email ?? 'N/A' }}</td>
                        <td>
                            {{-- Fixed Badge Logic --}}
                            @if($instructor->departments_count > 1)
                                <span class="badge bg-info bg-opacity-10 text-info">Shared Across Depts</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success">Internal</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('department.instructors.availability', ['instructor' => $instructor->id]) }}" 
                               class="btn btn-sm btn-outline-info me-1">
                                <i class="bi bi-calendar3"></i> Availability
                            </a>

                            <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $instructor->id }}">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            
                            <form action="{{ route('department.instructors.destroy', ['instructor' => $instructor->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove instructor from your department roster?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove from Department">
                                    <i class="bi bi-person-dash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- Edit Modal --}}
                    <div class="modal fade" id="editModal{{ $instructor->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title fw-bold">Edit Instructor Details</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('department.instructors.update', ['instructor' => $instructor->id]) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">First Name</label>
                                                <input type="text" name="first_name" class="form-control" value="{{ $instructor->first_name }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Last Name</label>
                                                <input type="text" name="last_name" class="form-control" value="{{ $instructor->last_name }}" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Email Address</label>
                                            <input type="email" name="email" class="form-control" value="{{ $instructor->email ?? '' }}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary px-4">Update Profile</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-3 opacity-25"></i>
                            No instructors linked to this department yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Instructor Modal --}}
<div class="modal fade" id="addInstructorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Add Instructor to Roster</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('department.instructors.store') }}" method="POST" id="instructorForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-secondary py-2 small mb-4">
                        <i class="bi bi-search me-1"></i> <strong>Note:</strong> Linking uses the Employee ID.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Employee ID / Username</label>
                        <div class="input-group">
                            <input type="text" name="username" id="search_username" class="form-control" placeholder="e.g. INST-101" required>
                            <button class="btn btn-outline-secondary" type="button" id="checkExisting">Check ID</button>
                        </div>
                        <div id="id_feedback" class="form-text mt-2"></div>
                    </div>
                    <div id="newInstructorFields">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Last Name</label>
                                <input type="text" name="last_name" id="last_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Add to Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('checkExisting').addEventListener('click', function() {
        const username = document.getElementById('search_username').value;
        const feedback = document.getElementById('id_feedback');
        
        if(!username) return alert('Enter an ID first');

        fetch(`/department/instructors/search?username=${username}`)
            .then(res => res.json())
            .then(data => {
                if(data.exists) {
                    feedback.innerHTML = `<span class="text-success"><i class="bi bi-check-circle"></i> Found: <strong>${data.first_name}</strong>.</span>`;
                    document.getElementById('first_name').value = data.first_name;
                    document.getElementById('last_name').value = data.last_name;
                    document.getElementById('email').value = data.email;
                    document.getElementById('first_name').readOnly = true;
                    document.getElementById('last_name').readOnly = true;
                    document.getElementById('email').readOnly = true;
                } else {
                    feedback.innerHTML = `<span class="text-muted">New instructor. Please fill details.</span>`;
                    document.getElementById('first_name').readOnly = false;
                    document.getElementById('last_name').readOnly = false;
                    document.getElementById('email').readOnly = false;
                }
            });
    });
</script>
@endsection