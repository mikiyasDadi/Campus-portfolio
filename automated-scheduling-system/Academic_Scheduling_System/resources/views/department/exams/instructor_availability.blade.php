@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Instructor Exam Availability</h2>
            <p class="text-muted small">
                <span class="badge bg-success">Toggle On</span> = Available | 
                <span class="badge bg-danger">Toggle Off</span> = Unavailable | 
                <i class="bi bi-lock-fill"></i> = Manually Locked by another Dept
            </p>
        </div>
        <button type="submit" form="availForm" class="btn btn-primary px-4 shadow-sm fw-bold">Save Changes</button>
    </div>

    <form action="{{ route('department.exam-instructor-avail.update') }}" method="POST" id="availForm">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="bg-light text-center">
                        <tr>
                            <th rowspan="2" class="ps-3 text-start" style="min-width: 220px;">Instructor Name</th>
                            @for($i = 1; $i <= $totalDays; $i++)
                                <th colspan="2" class="border-bottom-0">Day {{ $i }}</th>
                            @endfor
                        </tr>
                        <tr>
                            @for($i = 1; $i <= $totalDays; $i++)
                                <th class="x-small py-1 bg-white">M</th>
                                <th class="x-small py-1 bg-white">A</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($instructors as $instructor)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">{{ $instructor->first_name }} {{ $instructor->last_name }}</div>
                                    <div class="x-small text-muted">{{ $instructor->username }}</div>
                                </td>
                                @for($d = 1; $d <= $totalDays; $d++)
                                    @foreach(['morning', 'afternoon'] as $p)
                                        @php
                                            // 1. Find your own department's record
                                            $myPref = $instructor->examAvailabilities
                                                ->where('day_number', $d)
                                                ->where('period', $p)
                                                ->where('department_id', $deptId)
                                                ->first();

                                            // 2. Find if another department has specifically marked them UNAVAILABLE
                                            $otherConflict = $instructor->examAvailabilities
                                                ->where('day_number', $d)
                                                ->where('period', $p)
                                                ->where('department_id', '!=', $deptId)
                                                ->where('is_available', false) 
                                                ->first();

                                            // A cell is ONLY locked if someone else says "Unavailable". 
                                            // Otherwise, you have full control.
                                            $isLocked = !is_null($otherConflict);
                                            $isChecked = $myPref ? $myPref->is_available : true;
                                        @endphp
                                        
                                        <td class="text-center p-0 {{ $isLocked ? 'bg-light' : '' }}">
                                            @if(!$isLocked)
                                                <div class="form-check form-switch d-flex justify-content-center py-3">
                                                    {{-- Hidden input ensures '0' is sent if checkbox is unchecked --}}
                                                    <input type="hidden" name="availability[{{$instructor->id}}][{{$d}}][{{$p}}]" value="0">
                                                    <input class="form-check-input availability-toggle" type="checkbox" 
                                                           name="availability[{{$instructor->id}}][{{$d}}][{{$p}}]" 
                                                           value="1" {{ $isChecked ? 'checked' : '' }}>
                                                </div>
                                            @else
                                                <div class="py-2" title="Locked: Instructor marked unavailable by another department">
                                                    <div class="lock-indicator bg-danger"></div>
                                                    <i class="bi bi-lock-fill text-muted" style="font-size: 0.7rem;"></i>
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<style>
    .x-small { font-size: 0.7rem; font-weight: bold; color: #6c757d; }
    
    /* Customizing the Toggle Appearance */
    .availability-toggle { 
        width: 2.5em !important; 
        height: 1.25em !important; 
        cursor: pointer;
        border-color: #dee2e6;
    }
    /* Green when available */
    .availability-toggle:checked { 
        background-color: #198754 !important; 
        border-color: #198754 !important; 
    }
    /* Red when unavailable */
    .availability-toggle:not(:checked) { 
        background-color: #dc3545 !important; 
        border-color: #dc3545 !important; 
    }

    .lock-indicator { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
</style>
@endsection