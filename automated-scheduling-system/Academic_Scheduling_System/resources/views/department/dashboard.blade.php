@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h1 class="fw-bold">Department Head Portal</h1>
    <p class="text-muted">Manage courses, instructors, and scheduling for your department</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <p class="text-muted small mb-1">Courses</p>
            <h2 class="fw-bold mb-0">{{ $data['courses_count'] }}</h2>
            <p class="text-muted small">Active courses</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <p class="text-muted small mb-1">Instructors</p>
            <h2 class="fw-bold mb-0">{{ $data['instructors_count'] }}</h2>
            <p class="text-muted small">Department staff</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <p class="text-muted small mb-1">Schedules</p>
            <h2 class="fw-bold mb-0">{{ $data['draft_schedules'] }}</h2>
            <p class="text-muted small">Draft schedules</p>
        </div>
    </div>
</div>

@if($data['unread_comments']->count() > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-bell me-2"></i>Notifications</h5>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @foreach($data['unread_comments'] as $comment)
                    <div class="list-group-item p-3 border-0 border-bottom" id="comment-{{ $comment->id }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-info me-2">{{ ucfirst($comment->schedule_type) }} Schedule</span>
                                    <span class="text-muted small">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mb-1 fw-bold text-dark">
                                    Faculty Head ({{ $comment->user->first_name }} {{ $comment->user->last_name }}) commented on Year {{ $comment->year }}, Semester {{ $comment->semester }}:
                                </p>
                                <p class="mb-2 text-muted small">"{{ $comment->comment }}"</p>
                                
                                @php
                                    $route = $comment->schedule_type == 'class' 
                                        ? route('department.scheduler.show', ['year' => $comment->year, 'semester' => $comment->semester, 'section' => $comment->section])
                                        : route('department.exam-scheduler.show', ['year' => $comment->year, 'semester' => $comment->semester, 'section' => $comment->section]);
                                @endphp
                                <a href="{{ $route }}" class="btn btn-sm btn-outline-primary py-0 px-2 small">View Schedule</a>
                            </div>
                            <button class="btn btn-sm btn-light text-muted mark-as-read" data-id="{{ $comment->id }}">
                                <i class="bi bi-check-all me-1"></i> Mark as Read
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.mark-as-read').forEach(button => {
                button.addEventListener('click', function() {
                    const commentId = this.getAttribute('data-id');
                    const item = document.getElementById(`comment-${commentId}`);
                    
                    fetch(`/department/comments/${commentId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            item.classList.add('fade-out');
                            setTimeout(() => {
                                item.remove();
                                if (document.querySelectorAll('.list-group-item').length === 0) {
                                    document.querySelector('.card-header').closest('.card').remove();
                                }
                            }, 300);
                        }
                    });
                });
            });
        });
    </script>
    <style>
        .fade-out {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
    </style>
@endif

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <i class="bi bi-clock-history text-secondary fs-3 mb-3"></i>
            <h5 class="fw-bold">Manage Periods</h5>
            <p class="text-muted small">Set and edit the number and duration of class and lab periods.</p>
            <a href="{{ route('department.periods.index') }}" class="btn btn-primary mt-auto">Manage Periods</a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <i class="bi bi-book text-primary fs-3 mb-3"></i>
            <h5 class="fw-bold">Course Management</h5>
            <p class="text-muted small">Manage course offerings and assignments</p>
            <a href="{{ route('department.courses.index') }}" class="btn btn-primary mt-auto">Manage Courses</a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <i class="bi bi-people text-success fs-3 mb-3"></i>
            <h5 class="fw-bold">Manage Instructors</h5>
            <p class="text-muted small">View and manage department instructors</p>
            <a href="{{ route('department.instructors.index') }}" class="btn btn-primary mt-auto">
                <i class="bi bi-people"></i> Manage Instructors
            </a>
        </div>
    </div>

    {{-- Exam Overlaps Card --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <i class="bi bi-exclamation-octagon text-danger fs-3 mb-3"></i>
            <h5 class="fw-bold">Exam Overlaps</h5>
            <p class="text-muted small">Set courses that should not be scheduled at the same time for exams.</p>
            <a href="{{ route('department.exclusions.index') }}" class="btn btn-primary mt-auto">Set Overlap Exclusion</a>
        </div>
    </div>

    {{-- NEW: Instructor Exam Availability Card --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <i class="bi bi-calendar-check text-info fs-3 mb-3"></i>
            <h5 class="fw-bold">Exam Availability</h5>
            <p class="text-muted small">Set instructor availability for morning and afternoon exam periods.</p>
            <a href="{{ route('department.exam-instructor-avail.index') }}" class="btn btn-primary mt-auto">Set Exam Availability</a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-calendar2-range fs-3"></i>
                </div>
                <h5 class="fw-bold">Class Scheduler</h5>
                <p class="text-muted small">Prepare, upload CSV pairings, and initiate the automatic scheduling engine.</p>
                <a href="{{ route('department.scheduler.index') }}" class="btn btn-outline-primary btn-sm w-100">
                    Open Scheduler
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <i class="bi bi-lock text-warning fs-3 mb-3"></i>
            <h5 class="fw-bold">Locked Schedules</h5>
            <p class="text-muted small">View finalized and locked academic and exam schedules</p>
            <div class="d-grid gap-2 mt-auto">
                <a href="{{ route('department.scheduler.locked') }}" class="btn btn-primary btn-sm">Academic Timetables</a>
                <a href="{{ route('department.exam-scheduler.locked') }}" class="btn btn-outline-info btn-sm">Exam Timetables</a>
            </div>
        </div>
    </div>
</div>
@endsection