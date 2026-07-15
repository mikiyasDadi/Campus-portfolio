@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bold text-dark">Faculty Dashboard</h2>
        <p class="text-muted">Overview of departmental schedules within the {{ $user->faculty->name ?? 'Faculty' }}</p>
    </div>

    <div class="row">
        @forelse($departments as $dept)
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-university text-white py-3">
                        <h5 class="mb-0 fw-bold">{{ $dept->name }}</h5>
                        <small class="opacity-75">{{ $dept->code }}</small>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar3 text-primary me-2"></i>
                                <span class="fw-bold">Class Schedules</span>
                            </div>
                            <p class="small text-muted">View published weekly class timetables for this department.</p>
                            <a href="{{ route('faculty.class-schedules', $dept->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                View Class Schedules
                            </a>
                        </div>

                        <hr>

                        <div class="mt-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-journal-check text-success me-2"></i>
                                <span class="fw-bold">Exam Schedules</span>
                            </div>
                            <p class="small text-muted">View finalized exam timetables for this department.</p>
                            <a href="{{ route('faculty.exam-schedules', $dept->id) }}" class="btn btn-outline-success btn-sm w-100">
                                View Exam Schedules
                            </a>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 py-3">
                        <div class="d-flex justify-content-between small text-muted">
                            <span><i class="bi bi-people me-1"></i> {{ $dept->instructor_count }} Instructors</span>
                            <span><i class="bi bi-book me-1"></i> {{ $dept->course_count }} Courses</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info shadow-sm border-0">
                    <i class="bi bi-info-circle me-2"></i> No departments found assigned to your faculty.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
