@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Class Schedules - {{ $department->name }}</h2>
            <p class="text-muted mb-0">View finalized departmental class timetables</p>
        </div>
        <a href="{{ route('faculty.dashboard') }}" class="btn btn-outline-primary shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    @if($groups->isEmpty())
        <div class="card border-0 shadow-sm p-5 text-center">
            <i class="bi bi-calendar-x text-muted mb-3" style="font-size: 3rem;"></i>
            <h5 class="text-muted">No locked class schedules found for this department.</h5>
        </div>
    @else
        <div class="row">
            @foreach($groups as $group)
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm h-100 border-top border-4 border-info">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h4 class="fw-bold mb-0">Year {{ $group->year }}</h4>
                                    <div class="d-flex gap-2 mt-1">
                                        <span class="badge bg-light text-info border">Semester {{ $group->semester }}</span>
                                        <span class="badge bg-light text-success border">Section {{ $group->section }}</span>
                                    </div>
                                </div>
                                <i class="bi bi-shield-lock-fill text-success fs-3"></i>
                            </div>
                            
                            <p class="small text-muted mb-2">
                                <i class="bi bi-book me-1"></i> Total Classes: {{ $group->total_classes }}
                            </p>
                            <p class="small text-muted mb-4">
                                <i class="bi bi-clock-history me-1"></i> Last updated: {{ \Carbon\Carbon::parse($group->latest_update)->format('M d, Y') }}
                            </p>

                            <div class="d-grid gap-2">
                                <a href="{{ route('faculty.class-schedules.show', ['department' => $department->id, 'year' => $group->year, 'semester' => $group->semester, 'section' => $group->section]) }}" 
                                   class="btn btn-outline-info">
                                    <i class="bi bi-eye me-1"></i> View Class Timetable
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
