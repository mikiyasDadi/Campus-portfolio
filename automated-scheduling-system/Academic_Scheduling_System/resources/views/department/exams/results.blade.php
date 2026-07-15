@extends('layouts.admin')

@section('content')
<div class="container-fluid px-2">
    <div class="d-flex justify-content-between align-items-center mb-2 d-print-none">
        <div>
            <h4 class="fw-bold text-primary mb-0">{{ ($meta['is_faculty_view'] ?? false) ? 'Exam Timetable' : 'Generated Exam Timetable' }}</h4>
            <p class="text-muted small mb-0">
                Year {{ $meta['year'] }}, Sem {{ $meta['semester'] }} {{ isset($meta['section']) ? '(Section ' . $meta['section'] . ')' : '' }}
            </p>
        </div>
        <div class="d-flex gap-1">
            @if($meta['is_faculty_view'] ?? false)
                <a href="{{ route('faculty.exam-schedules', request()->route('department')) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-arrow-left"></i>
                </a>
            @endif

            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-printer"></i>
            </button>
            @if(!($meta['is_locked'] ?? false))
                <form action="{{ route('department.exam-scheduler.save') }}" method="POST">
                    @csrf
                    <input type="hidden" name="schedule_data" value="{{ json_encode($schedule) }}">
                    <input type="hidden" name="exam_dates" value="{{ json_encode($examDates) }}">
                    <input type="hidden" name="year" value="{{ $meta['year'] }}">
                    <input type="hidden" name="semester" value="{{ $meta['semester'] }}">
                    <input type="hidden" name="section" value="{{ $meta['section'] ?? '' }}">
                    <button type="submit" class="btn btn-sm btn-success px-3">
                        Save <i class="bi bi-check-circle ms-1"></i>
                    </button>
                </form>
            @else
                <form action="{{ route('department.exam-scheduler.destroyGroup') }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this locked schedule? All invigilators will be released.')">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="year" value="{{ $meta['year'] }}">
                    <input type="hidden" name="semester" value="{{ $meta['semester'] }}">
                    <input type="hidden" name="section" value="{{ $meta['section'] ?? '' }}">
                    <button type="submit" class="btn btn-outline-danger shadow-sm">
                        <i class="bi bi-trash me-2"></i>Delete Published Schedule
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show d-print-none">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 text-center" style="min-width: 1600px;">
                <thead class="bg-primary text-white" style="font-size: 0.75rem;">
                    <tr>
                        <th style="width: 100px; vertical-align: middle;" class="bg-primary shadow-sm">Period</th>
                        @for($d = 1; $d <= $total_days; $d++)
                            @php $anyExamsThisDay = collect($schedule)->where('day_number', $d)->count() > 0; @endphp
                            <th style="vertical-align: middle; padding: 10px 5px;" class="{{ !$anyExamsThisDay ? 'opacity-50' : '' }}">
                                <div class="fw-bold">Day {{ $d }}</div>
                                <div class="x-small fw-normal">{{ isset($examDates[$d]) ? date('M d', strtotime($examDates[$d])) : '' }}</div>
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach(['morning' => 'Morning', 'afternoon' => 'Afternoon'] as $pKey => $pLabel)
                        <tr>
                            <td class="fw-bold bg-light border-end" style="font-size: 0.75rem; height: 120px;">
                                <span class="text-primary">{{ $pLabel }}</span>
                            </td>
                            @for($d = 1; $d <= $total_days; $d++)
                                @php
                                    $exams = collect($schedule)->where('day_number', $d)->where('period', $pKey);
                                    $hasExams = $exams->count() > 0;
                                    $anyExamsThisDay = collect($schedule)->where('day_number', $d)->count() > 0;
                                @endphp
                                <td class="p-2 {{ !$anyExamsThisDay ? 'bg-light bg-opacity-10' : '' }}" 
                                    style="vertical-align: top; width: 150px;">
                                    
                                    @if($hasExams)
                                        <div class="d-flex flex-column gap-2">
                                            @foreach($exams as $exam)
                                                <div class="p-2 rounded shadow-sm border-start border-3 border-primary bg-primary bg-opacity-10 text-start" style="font-size: 0.7rem; min-height: 80px;">
                                                    <div class="fw-bold text-primary mb-1" style="font-size: 0.65rem; line-height: 1;">
                                                        #{{ $exam['course_code'] }}
                                                    </div>
                                                    
                                                    <div class="fw-bold text-dark mb-1 small" style="line-height: 1.2; display: block;" title="{{ $exam['course_name'] }}">
                                                        {{ Str::limit($exam['course_name'], 50) }}
                                                    </div>

                                                    <div class="pt-1 border-top mt-1" style="font-size: 0.65rem; opacity: 0.9;">
                                                        <div class="mb-1 text-truncate"><i class="bi bi-geo-alt me-1"></i>{{ $exam['room_name'] }}</div>
                                                        <div class="text-info text-truncate">
                                                            <i class="bi bi-people-fill me-1"></i>{{ explode(' ', $exam['inv1_name'])[0] }}, {{ explode(' ', $exam['inv2_name'])[0] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 opacity-25">
                                            <span style="font-size: 0.7rem;">---</span>
                                        </div>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
                    <input type="hidden" name="section" value="{{ $meta['section'] ?? '' }}">
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
        padding: 2px !important;
    }
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
    .x-small { font-size: 0.6rem; }
    @media print {
        @page { size: landscape; margin: 0.5cm; }
        .navbar, .sidebar, .d-print-none, .btn-outline-secondary { display: none !important; }
        .container-fluid { padding: 0 !important; margin: 0 !important; }
        .card { border: none !important; }
        .bg-primary { background-color: #0d6efd !important; color: #fff !important; -webkit-print-color-adjust: exact; }
        .table { width: 100% !important; border-collapse: collapse !important; table-layout: fixed; }
        .table th, .table td { font-size: 0.6rem !important; padding: 1px !important; }
    }
</style>
@endsection
