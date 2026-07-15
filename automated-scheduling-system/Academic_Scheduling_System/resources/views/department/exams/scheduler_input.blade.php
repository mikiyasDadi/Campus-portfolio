@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-primary">Validate Exam Inputs & Resources</h2>
    <p class="text-muted">Target: Year {{ $selection['year'] }} - Semester {{ $selection['semester'] }}</p>
</div>

{{-- ENHANCED EXPLICIT CONSTRAINT NOTIFICATION BOX --}}
@if(isset($warnings) && count($warnings) > 0)
    <div class="alert alert-warning border-start border-4 border-warning shadow-sm py-3 mb-4">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
            <h6 class="mb-0 fw-bold">Pre-flight Constraint Check & Warnings</h6>
        </div>
        <ul class="mb-0 small fw-medium">
            @foreach($warnings as $warning)
                <li class="mb-1 text-dark">{{ $warning }}</li>
            @endforeach
        </ul>
        <div class="mt-3 p-2 bg-light rounded-3 small border">
            <i class="bi bi-info-circle-fill me-1 text-primary"></i> 
            <strong>Note:</strong> The system is scanning for <strong>Cross-Departmental Overlaps</strong>. 
            Missing exclusion rules or constraints may result in student clashes with courses from other departments.
        </div>
    </div>
@endif

<form action="{{ route('department.exam-scheduler.initiate') }}" method="POST">
    @csrf
    <input type="hidden" name="year" value="{{ $selection['year'] ?? '' }}">
    <input type="hidden" name="semester" value="{{ $selection['semester'] ?? '' }}">
    <input type="hidden" name="section" value="{{ $selection['section'] ?? '' }}">

    <div class="row g-4">
        {{-- Left Column: Global Resources --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-door-open me-2"></i>Global Resources</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-primary">
                            <i class="bi bi-calendar-event me-1"></i> Exam Start Date
                        </label>
                        <input type="date" name="start_date" class="form-control border-primary" 
                               value="{{ $savedParams['start_date'] ?? date('Y-m-d', strtotime('tomorrow')) }}" required>
                        <div class="form-text small">The system will generate a 10-day schedule (excluding weekends) starting from this date.</div>
                    </div>

                    <div class="alert alert-info border-0 shadow-sm small py-3 mb-4">
                        <h6 class="fw-bold mb-1"><i class="bi bi-info-circle-fill me-1"></i> Resource Note</h6>
                        The engine will automatically use the <strong>{{ \App\Models\Room::where('department_id', auth()->user()->department_id)->count() }} rooms</strong> registered in your <a href="{{ route('department.rooms.index') }}" class="fw-bold text-decoration-none">Room Management</a> tab.
                    </div>

                    <hr>

                    @if(collect($processedData)->every('ready', true))
                        <button type="submit" class="btn btn-success w-100 py-3 fw-bold shadow-sm">
                            <i class="bi bi-cpu-fill me-2"></i> INITIATE SCHEDULER
                        </button>
                    @else
                        <div class="alert alert-danger mb-0 small border-0 shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Resolve Critical Errors in the table to proceed.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Course Table --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">Course Assignments</h5>
                    <span class="badge bg-primary px-3">{{ count($processedData) }} Courses</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-uppercase small fw-bold">
                                    <th class="ps-4">Course Info</th>
                                    <th>Invigilators</th>
                                    <th style="width: 150px;">Duration</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($processedData as $index => $row)
                                    <tr class="{{ !$row['ready'] ? 'table-danger' : '' }}">
                                        <td class="ps-4">
                                            <div class="fw-bold">{{ $row['course_code'] }}</div>
                                            <div class="small text-muted">{{ $row['course_name'] }}</div>
                                            
                                            {{-- INVISIBLE DATA CARRIERS --}}
                                            <input type="hidden" name="courses[{{ $index }}][course_id]" value="{{ $row['course_id'] }}">
                                            <input type="hidden" name="courses[{{ $index }}][course_code]" value="{{ $row['course_code'] }}">
                                            <input type="hidden" name="courses[{{ $index }}][course_name]" value="{{ $row['course_name'] }}">
                                            <input type="hidden" name="courses[{{ $index }}][inv1_name]" value="{{ $row['inv1_name'] }}">
                                            <input type="hidden" name="courses[{{ $index }}][inv2_name]" value="{{ $row['inv2_name'] }}">
                                        </td>
                                        <td>
                                            <div class="small">
                                                <i class="bi bi-person me-1"></i> {{ $row['inv1_name'] ?? '???' }}<br>
                                                <i class="bi bi-person-fill me-1"></i> {{ $row['inv2_name'] ?? '???' }}
                                            </div>
                                            <input type="hidden" name="courses[{{ $index }}][inv1_id]" value="{{ $row['inv1_id'] }}">
                                            <input type="hidden" name="courses[{{ $index }}][inv2_id]" value="{{ $row['inv2_id'] }}">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <select name="courses[{{ $index }}][hours]" class="form-select form-select-sm">
                                                    @foreach(range(1, 4) as $h) 
                                                        <option value="{{ $h }}" {{ $h == 2 ? 'selected' : '' }}>{{ $h }}h</option> 
                                                    @endforeach
                                                </select>
                                                <select name="courses[{{ $index }}][mins]" class="form-select form-select-sm">
                                                    @foreach(['00','15','30','45'] as $m) 
                                                        <option value="{{ $m }}">{{ $m }}m</option> 
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($row['ready'])
                                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                            @else
                                                <span class="badge bg-danger">ERROR</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection