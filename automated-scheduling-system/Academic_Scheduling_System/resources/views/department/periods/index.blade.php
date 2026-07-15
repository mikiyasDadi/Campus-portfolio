@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white p-3">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i> Manage Period Settings</h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted">Set the default durations for your department's schedule.</p>

                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('department.periods.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-bold">Class Period Duration (Minutes)</label>
                            <input type="number" name="class_duration" class="form-control" value="{{ old('class_duration', $department->class_duration) }}" required>
                            <div class="form-text">Standard lecture time (e.g., 50 or 60).</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Lab Period Duration (Minutes)</label>
                            <input type="number" name="lab_duration" class="form-control" value="{{ old('lab_duration', $department->lab_duration) }}" required>
                            <div class="form-text">Duration for practical sessions (e.g., 100 or 120).</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Total Periods per Day</label>
                            <input type="number" name="total_periods" class="form-control" value="{{ old('total_periods', $department->total_periods) }}" required>
                            <div class="form-text">How many time slots are available in a day?</div>
                        </div>

                        <hr>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">Save Period Settings</button>
                        </div>
                    </form>

                    <div class="mt-5 border-top pt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-calendar3 me-2"></i> Schedule Preview</h5>
                            <span class="badge bg-light text-dark border">Morning: 8:00 AM | Afternoon: 2:00 PM</span>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 15%;">Period</th>
                                        <th style="width: 30%;">Start Time</th>
                                        <th style="width: 30%;">End Time</th>
                                        <th style="width: 25%;">Session</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $startTime = \Carbon\Carbon::createFromTime(8, 0); 
                                        $classDur = $department->class_duration ?? 50;
                                        $totalPer = $department->total_periods ?? 0;
                                    @endphp

                                    @if($totalPer > 0)
                                        @for($i = 1; $i <= $totalPer; $i++)
                                            @php
                                                // If we reach Period 5, force start time to 2:00 PM
                                                if ($i == 5) {
                                                    $startTime = \Carbon\Carbon::createFromTime(14, 0); 
                                                }
                                                
                                                $endTime = $startTime->copy()->addMinutes($classDur);
                                            @endphp
                                            <tr>
                                                <td class="fw-bold bg-light">#{{ $i }}</td>
                                                <td>{{ $startTime->format('h:i A') }}</td>
                                                <td>{{ $endTime->format('h:i A') }}</td>
                                                <td>
                                                    @if($i < 5)
                                                        <span class="badge bg-info text-dark">Morning</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">Afternoon</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @php
                                                // Next period starts exactly when current one ends (0 minutes break)
                                                $startTime = $endTime->copy();
                                            @endphp
                                        @endfor
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center py-3 text-muted italic">
                                                Please set the total periods to see a schedule preview.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="alert alert-info border-0 small mt-2">
                            <i class="bi bi-info-circle-fill me-1"></i> 
                            Periods 1-4 are scheduled back-to-back starting at 8:00 AM. Period 5 and onwards begin the afternoon session starting at 2:00 PM.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection