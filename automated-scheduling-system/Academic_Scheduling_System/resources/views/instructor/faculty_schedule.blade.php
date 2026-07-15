@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}" class="text-university">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Faculty Overview</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-university mb-1">{{ $department->name }} Faculty Schedule</h2>
            <p class="text-muted small">Viewing all instructors' classes in the department.</p>
        </div>
        <a href="{{ route('instructor.dashboard') }}" class="btn btn-outline-university btn-sm shadow-sm px-3">
            <i class="bi bi-arrow-left me-2"></i>Back to My Schedule
        </a>
    </div>

    @foreach($schedules as $instructorId => $instructorClasses)
        @php $instructor = $instructorClasses->first()->instructor; @endphp
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header bg-university text-white py-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-person-badge me-2"></i>{{ $instructor->full_name ?? 'Instructor ID: ' . $instructorId }}
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="bg-light text-center">
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
                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                    </td>
                                    @foreach($days as $day)
                                        @php $class = $instructorClasses->where('day', $day)->where('period', $period)->first(); @endphp
                                        <td class="p-2" style="height: 80px; width: 18%;">
                                            @if($class)
                                                <div class="p-2 rounded h-100 {{ $class->type == 'Lab' ? 'bg-warning-subtle border-warning' : 'bg-primary-subtle border-primary' }} border-start border-4">
                                                    <div class="fw-bold small">{{ $class->course_code }}</div>
                                                    <div class="text-muted" style="font-size: 0.7rem;">
                                                        Year {{ $class->year }} | {{ $class->type }}
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
    @endforeach

    @if($schedules->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
                <h5 class="fw-bold text-muted">No schedules found for this department.</h5>
                <p class="text-muted mb-0">Locked schedules will appear here once they are finalized.</p>
            </div>
        </div>
    @endif
</div>

<style>
    .btn-outline-university { color: #0056b3; border-color: #0056b3; }
    .btn-outline-university:hover { background-color: #0056b3; color: #fff; }
    .text-university { color: #0056b3; text-decoration: none; }
    .bg-university { background-color: #0056b3; }
</style>
@endsection
