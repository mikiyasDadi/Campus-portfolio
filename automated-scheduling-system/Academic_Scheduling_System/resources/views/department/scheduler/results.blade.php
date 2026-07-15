@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">{{ ($meta['is_faculty_view'] ?? false) ? 'Departmental Timetable' : 'Generated Timetable' }}</h2>
            <p class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Review the conflict-free schedule for <strong>Year {{ $meta['year'] }}</strong>, 
                <strong>Semester {{ $meta['semester'] }}</strong>
                @if($meta['dept_name'] ?? false)
                    — <strong>{{ $meta['dept_name'] }}</strong>
                @endif
            </p>
        </div>
        <div class="d-print-none d-flex gap-2">
            @if($meta['is_faculty_view'] ?? false)
                <a href="{{ route('faculty.class-schedules', request()->route('department')) }}" class="btn btn-outline-primary shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            @endif

            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> Print PDF
            </button>
            
            @if(!($meta['is_locked'] ?? false))
                <form action="{{ route('department.scheduler.store') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="schedule_data" value="{{ json_encode($schedule) }}">
                    <input type="hidden" name="year" value="{{ $meta['year'] }}">
                    <input type="hidden" name="semester" value="{{ $meta['semester'] }}">
                    <input type="hidden" name="section" value="{{ $meta['section'] }}">
                    <button type="submit" class="btn btn-success px-4 shadow-sm">
                        Confirm & Save Schedule <i class="bi bi-check-circle ms-1"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 text-center">
                {{-- FIX APPLIED: Changed to Primary theme --}}
                <thead class="bg-primary text-white">
                    <tr>
                        <th style="width: 120px; vertical-align: middle;">Period</th>
                        @foreach($days as $day)
                            <th style="vertical-align: middle;">{{ $day }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @for($p = 1; $p <= $total_periods; $p++)
                    <tr>
                        <td class="fw-bold bg-light border-end">
                            <span class="d-block text-primary">Period {{ $p }}</span>
                        </td>
                        @foreach($days as $day)
                            @php 
                                $slotKey = "{$day}_{$p}";
                                $cell = $schedule[$slotKey] ?? null;
                            @endphp
                            <td class="p-2" style="min-width: 160px; height: 110px;">
                                @if($cell)
                                    <div class="h-100 p-2 rounded shadow-sm border-start border-4 
                                        @if($cell['type'] == 'Lab') 
                                            border-danger bg-danger bg-opacity-10 
                                        @elseif($cell['type'] == 'Tut')
                                            border-warning bg-warning bg-opacity-10
                                        @else
                                            border-primary bg-primary bg-opacity-10 
                                        @endif">
                                        
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <span class="badge {{ $cell['type'] == 'Lab' ? 'bg-danger' : ($cell['type'] == 'Tut' ? 'bg-warning text-dark' : 'bg-primary') }} mb-1">
                                                {{ $cell['type'] }}
                                            </span>
                                            <small class="text-muted fw-bold">#{{ $cell['course_code'] }}</small>
                                        </div>

                                        <div class="fw-bold text-dark small text-start mb-1" style="line-height: 1.2;">
                                            {{ Str::limit($cell['course_name'], 40) }}
                                        </div>
                                        
                                        <div class="pt-1 border-top mt-1">
                                            <div class="small text-muted text-start text-truncate">
                                                <i class="bi bi-person text-secondary me-1"></i>{{ $cell['instructor'] }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <span class="text-muted opacity-50 small">---</span>
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4 p-3 bg-light rounded border d-print-none">
        <h6 class="fw-bold"><i class="bi bi-lightbulb me-2"></i>Defense Note:</h6>
        <p class="small text-muted mb-0">
            The algorithm has distributed <strong>{{ count($schedule) }}</strong> task units across the available 
            <strong>{{ count($days) * $total_periods }}</strong> slots, ensuring no instructor or time-slot conflicts exist.
        </p>
    </div>

    @if($meta['is_faculty_view'] ?? false)
        <div class="mt-4 card border-0 shadow-sm d-print-none">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-chat-dots me-2"></i>Leave a Comment</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('faculty.comments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="department_id" value="{{ $meta['department_id'] }}">
                    <input type="hidden" name="year" value="{{ $meta['year'] }}">
                    <input type="hidden" name="semester" value="{{ $meta['semester'] }}">
                    <input type="hidden" name="schedule_type" value="{{ $meta['schedule_type'] }}">
                    
                    <div class="mb-3">
                        <label for="comment" class="form-label text-muted small">Your comments or feedback for the Department Head</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Enter your comments here..." required></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i> Send Comment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<style>
    .table-bordered td, .table-bordered th {
        border: 1px solid #dee2e6 !important;
    }
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
    @media print {
        @page { size: landscape; }
        .navbar, .sidebar, .d-print-none, .btn-outline-secondary { display: none !important; }
        .container-fluid { padding: 0 !important; margin: 0 !important; }
        .card { border: none !important; }
        {{-- Ensuring header color prints correctly --}}
        .bg-primary { background-color: #0d6efd !important; color: #fff !important; -webkit-print-color-adjust: exact; }
        .table { width: 100% !important; border-collapse: collapse !important; }
        .bg-danger, .bg-warning { 
            -webkit-print-color-adjust: exact !important; 
            print-color-adjust: exact !important; 
        }
    }
</style>
@endsection