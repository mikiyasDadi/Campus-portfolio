@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-university mb-1">Instructor Dashboard</h2>
            <p class="text-muted">Welcome back, {{ $user->full_name }}!</p>
        </div>
        <a href="{{ route('instructor.download-schedule') }}" target="_blank" class="btn btn-university text-white shadow-sm">
            <i class="bi bi-download me-2"></i>Download Schedule
        </a>
    </div>

    <!-- Next Class Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-university text-white rounded-circle p-3 me-4">
                            <i class="bi bi-clock-fill fs-3"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Next Scheduled Class</h5>
                            @if($nextClass)
                                <div class="d-flex align-items-center gap-3">
                                    <span class="badge bg-university text-white px-3 py-2">
                                        {{ $nextClass['day'] }} at {{ $nextClass['time'] }}
                                    </span>
                                    <span class="fw-bold text-dark">
                                        {{ $nextClass['class']->course_code }} - 
                                        {{ $nextClass['class']->course->course_name ?? 'N/A' }}
                                    </span>
                                    <span class="text-muted">
                                        ({{ $nextClass['class']->type }}, Year {{ $nextClass['class']->year }})
                                    </span>
                                </div>
                            @else
                                <p class="text-muted mb-0">No upcoming classes scheduled.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded shadow-sm" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold px-4" id="timetable-tab" data-bs-toggle="tab" data-bs-target="#timetable" type="button" role="tab">
                <i class="bi bi-calendar3 me-2"></i>My Timetable
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold px-4" id="faculty-tab" data-bs-toggle="tab" data-bs-target="#faculty" type="button" role="tab">
                <i class="bi bi-people me-2"></i>Faculty Overview
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="dashboardTabsContent">
        <!-- My Timetable Tab -->
        <div class="tab-pane fade show active" id="timetable" role="tabpanel">
            <div class="card border-0 shadow-sm">
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
                                            <td class="p-2" style="height: 100px; width: 18%;">
                                                @if(isset($timetable[$day][$period]))
                                                    @php $class = $timetable[$day][$period]; @endphp
                                                    <div class="p-2 rounded h-100 {{ $class->type == 'Lab' ? 'bg-warning-subtle border-warning' : 'bg-primary-subtle border-primary' }} border-start border-4">
                                                        <div class="fw-bold small">{{ $class->course_code }}</div>
                                                        <div class="text-muted" style="font-size: 0.75rem;">
                                                            Year {{ $class->year }} | {{ $class->type }}
                                                        </div>
                                                        <div class="mt-1 small fw-medium text-truncate" title="{{ $class->course->course_name ?? 'N/A' }}">
                                                            {{ $class->course->course_name ?? 'N/A' }}
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
        </div>

        <!-- Faculty Overview Tab -->
        <div class="tab-pane fade" id="faculty" role="tabpanel">
            <div class="row">
                @foreach($facultyOverview as $department)
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="fw-bold mb-0 text-university">
                                    <i class="bi bi-building me-2"></i>{{ $department->name }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">Instructors in this department:</p>
                                <div class="list-group list-group-flush">
                                    @forelse($department->users as $instructor)
                                        <a href="{{ route('instructor.faculty-overview', $department->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <span>{{ $instructor->full_name }}</span>
                                            <i class="bi bi-chevron-right small text-muted"></i>
                                        </a>
                                    @empty
                                        <div class="text-center py-3 text-muted">No instructors found.</div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="card-footer bg-light border-0">
                                <a href="{{ route('instructor.faculty-overview', $department->id) }}" class="btn btn-sm btn-outline-university w-100">
                                    View Full Dept Schedule
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    .btn-university { background-color: #0056b3; border-color: #0056b3; }
    .btn-university:hover { background-color: #004494; border-color: #004494; }
    .btn-outline-university { color: #0056b3; border-color: #0056b3; }
    .btn-outline-university:hover { background-color: #0056b3; color: #fff; }
    .text-university { color: #0056b3; }
    .bg-university { background-color: #0056b3; }
    
    .nav-pills .nav-link.active {
        background-color: #0056b3;
    }
    .nav-pills .nav-link {
        color: #666;
    }
</style>
@endsection
