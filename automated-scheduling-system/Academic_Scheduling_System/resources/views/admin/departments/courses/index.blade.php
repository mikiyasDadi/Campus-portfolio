@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Course Catalog</h2>
        <p class="text-muted">Manage course catalog including ECTS, lecture, tutorial, and lab hours</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
        <i class="bi bi-plus-lg"></i> Add Course
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Course Name</th>
                        <th>Department</th>
                        <th>ECTS</th>
                        <th>Hours (L/T/Lab)</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                    <tr>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $course->course_code }}</span></td>
                        <td>{{ $course->course_name }}</td>
                        <td class="text-muted">{{ auth()->user()->department->name }}</td>
                        <td>{{ $course->ects }} ECTS</td>
                        <td>{{ $course->hours }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil-square"></i> Edit</button>
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                        </td>
                    </tr>
                    @endforeach
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
                    <div class="mb-3">
                        <label class="form-label fw-bold">Course Code</label>
                        <input type="text" name="course_code" class="form-control" placeholder="e.g., CS101" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Course Name</label>
                        <input type="text" name="course_name" class="form-control" placeholder="e.g., Introduction to Programming" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">ECTS</label>
                            <input type="number" name="ects" class="form-control" placeholder="6" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Hours (L/T/Lab)</label>
                            <input type="text" name="hours" class="form-control" placeholder="3/2/2" required>
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
@endsection