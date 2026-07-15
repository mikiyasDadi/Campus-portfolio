@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Course Catalog</h2>
        <p class="text-muted">Manage course catalog including Year, Semester, ECTS, and Hours</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addCourseModal">
        <i class="bi bi-plus-lg"></i> Add Course
    </button>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('department.courses.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Search Courses</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Code or Name..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Filter Year</label>
                <select name="year" class="form-select">
                    <option value="">All Years</option>
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Filter Semester</label>
                <select name="semester" class="form-select">
                    <option value="">All Semesters</option>
                    <option value="1" {{ request('semester') == 1 ? 'selected' : '' }}>Semester 1</option>
                    <option value="2" {{ request('semester') == 2 ? 'selected' : '' }}>Semester 2</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-filter"></i> Apply Filters
                </button>
                <a href="{{ route('department.courses.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Success Alert --}}
@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
@endif

{{-- Validation Error Alert --}}
@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <div class="fw-bold">Please fix the following errors:</div>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Course Name</th>
                        <th>Year</th>
                        <th>Sem</th>
                        <th>ECTS</th>
                        <th>Hours (L/T/Lab)</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                    <tr>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary fw-bold">{{ $course->course_code }}</span></td>
                        <td>
                            <div class="fw-bold text-dark">{{ $course->course_name }}</div>
                            <small class="text-muted">
                                @if(auth()->user()->department)
                                    {{ auth()->user()->department->name }}
                                @else
                                    <span class="text-warning">Not Assigned</span>
                                @endif
                            </small>
                        </td>
                        <td><span class="badge bg-info bg-opacity-10 text-info">Year {{ $course->year }}</span></td>
                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary">Sem {{ $course->semester }}</span></td>
                        <td>{{ $course->ects }} ECTS</td>
                        <td>{{ $course->hours }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editCourseModal{{ $course->id }}">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            
                            <form action="{{ route('department.courses.destroy', $course->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>

                    <div class="modal fade" id="editCourseModal{{ $course->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title fw-bold">Edit Course: {{ $course->course_code }}</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('department.courses.update', $course->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        @if($errors->has('course_code'))
                                            <div class="text-danger small mb-2 fw-bold">
                                                <i class="bi bi-exclamation-circle"></i> {{ $errors->first('course_code') }}
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Course Code</label>
                                            <input type="text" name="course_code" class="form-control @error('course_code') is-invalid @enderror" value="{{ $course->course_code }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Course Name</label>
                                            <input type="text" name="course_name" class="form-control" value="{{ $course->course_name }}" required>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Year</label>
                                                <select name="year" class="form-select" required>
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <option value="{{ $i }}" {{ $course->year == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Semester</label>
                                                <select name="semester" class="form-select" required>
                                                    <option value="1" {{ $course->semester == 1 ? 'selected' : '' }}>Semester 1</option>
                                                    <option value="2" {{ $course->semester == 2 ? 'selected' : '' }}>Semester 2</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">ECTS</label>
                                                <input type="number" name="ects" class="form-control" value="{{ $course->ects }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Hours (L/T/Lab)</label>
                                                <input type="text" name="hours" class="form-control" value="{{ $course->hours }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary px-4">Update Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-search fs-1 d-block mb-3"></i>
                                <p class="mb-0">No courses found matching your criteria.</p>
                                <a href="{{ route('department.courses.index') }}" class="small">Clear all filters</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="addCourseModalLabel">Add New Course</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('department.courses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    @error('course_code')
                        <div class="alert alert-danger py-2 small border-0 mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $message }}
                        </div>
                    @enderror

                    <div class="mb-3">
                        <label class="form-label fw-bold">Course Code</label>
                        <input type="text" name="course_code" class="form-control @error('course_code') is-invalid @enderror" placeholder="e.g., CS101" value="{{ old('course_code') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Course Name</label>
                        <input type="text" name="course_name" class="form-control" placeholder="e.g., Introduction to Programming" value="{{ old('course_name') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Year</label>
                            <select name="year" class="form-select" required>
                                <option value="" selected disabled>Select Year</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('year') == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Semester</label>
                            <select name="semester" class="form-select" required>
                                <option value="" selected disabled>Select Semester</option>
                                <option value="1" {{ old('semester') == 1 ? 'selected' : '' }}>Semester 1</option>
                                <option value="2" {{ old('semester') == 2 ? 'selected' : '' }}>Semester 2</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">ECTS</label>
                            <input type="number" name="ects" class="form-control" placeholder="6" value="{{ old('ects') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Hours (L/T/Lab)</label>
                            <input type="text" name="hours" class="form-control" placeholder="3/2/2" value="{{ old('hours') }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script to keep modal open if validation fails --}}
@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var myModal = new bootstrap.Modal(document.getElementById('addCourseModal'));
        myModal.show();
    });
</script>
@endif
@endsection