@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-university mb-1">Student Academic Dashboard</h2>
            <p class="text-muted">Welcome, {{ $user->full_name }} (Year {{ $year }}, Semester {{ $semester }}, Section {{ $section }})</p>
        </div>
        <a href="{{ route('student.download-schedule') }}" target="_blank" class="btn btn-university text-white shadow-sm">
            <i class="bi bi-download me-2"></i>Download My Schedule
        </a>
    </div>

    <!-- Happening Now Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm {{ $currentClass ? 'bg-primary text-white' : 'bg-light' }}">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 me-4 {{ $currentClass ? 'bg-white text-primary' : 'bg-university text-white' }}">
                            <i class="bi bi-play-circle-fill fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">Happening Now</h5>
                            @if($currentClass)
                                <div class="d-md-flex align-items-center justify-content-between">
                                    <div>
                                        <h4 class="mb-0 fw-bold">
                                            {{ $currentClass['class']->course->course_name ?? 'N/A' }} 
                                            <small class="fs-6 fw-normal">({{ $currentClass['class']->course_code }})</small>
                                        </h4>
                                        <p class="mb-0 opacity-75">
                                            {{ $currentClass['class']->type }} with {{ $currentClass['class']->instructor->full_name ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="text-md-end mt-3 mt-md-0">
                                        <div class="badge bg-white text-primary px-3 py-2 fs-6">
                                            Ends at {{ $currentClass['end_time'] }}
                                        </div>
                                        <p class="mb-0 mt-1 small opacity-75">
                                            Ends in {{ $currentClass['remaining_minutes'] }} minutes
                                        </p>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted mb-0">No active class at this time.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Timetable -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="fw-bold mb-0 text-university">
                <i class="bi bi-calendar-week me-2"></i>Weekly Timetable
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="bg-university text-white text-center">
                        <tr>
                            <th style="width: 100px;">Time</th>
                            @foreach($days as $day)
                                <th>{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timeSlots as $index => $slot)
                            @php $period = $index + 1; @endphp
                            <tr>
                                <td class="text-center bg-light fw-bold small">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}<br>
                                    <span class="text-muted fw-normal">-</span><br>
                                    {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                </td>
                                @foreach($days as $day)
                                    <td class="p-2" style="height: 120px; width: 18%;">
                                        @if(isset($timetable[$day][$period]))
                                            @php $class = $timetable[$day][$period]; @endphp
                                            <div class="p-2 rounded h-100 {{ $class->type == 'Lab' ? 'bg-warning-subtle border-warning' : ($class->type == 'Tutorial' ? 'bg-success-subtle border-success' : 'bg-primary-subtle border-primary') }} border-start border-4">
                                                <div class="fw-bold small text-truncate" title="{{ $class->course->course_name ?? 'N/A' }}">
                                                    {{ $class->course->course_name ?? 'N/A' }}
                                                </div>
                                                <div class="text-muted" style="font-size: 0.7rem;">
                                                    {{ $class->course_code }} | {{ $class->type }}
                                                </div>
                                                <div class="mt-2 small fw-medium">
                                                    <i class="bi bi-person text-muted me-1"></i>
                                                    {{ $class->instructor->full_name ?? 'N/A' }}
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Exam Schedule Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="fw-bold mb-0 text-danger">
                <i class="bi bi-file-earmark-text me-2"></i>Upcoming Exam Schedules
            </h5>
        </div>
        <div class="card-body">
            @if($examSchedules->isNotEmpty())
                <div class="row">
                    @foreach($examSchedules as $exam)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 border-start border-danger border-4 shadow-sm">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-1 text-danger">{{ $exam->course->course_name }}</h6>
                                    <p class="text-muted small mb-2">{{ $exam->course->course_code }}</p>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-calendar-event me-2 text-muted"></i>
                                        <span>{{ \Carbon\Carbon::parse($exam->exam_date)->format('F d, Y') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-clock me-2 text-muted"></i>
                                        <span>Period {{ $exam->period }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt me-2 text-muted"></i>
                                        <span>{{ $exam->room_name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-muted mb-3 d-block"></i>
                    <p class="text-muted mb-0">No exams have been published yet for this semester.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .btn-university { background-color: #0056b3; border-color: #0056b3; }
    .btn-university:hover { background-color: #004494; border-color: #004494; }
    .text-university { color: #0056b3; }
    .bg-university { background-color: #0056b3; }
    
    .bg-primary-subtle { background-color: #e7f1ff; }
    .bg-warning-subtle { background-color: #fff3cd; }
    .bg-success-subtle { background-color: #d1e7dd; }
</style>
@endsection
