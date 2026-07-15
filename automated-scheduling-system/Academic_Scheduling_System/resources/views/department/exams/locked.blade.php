@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Locked Exam Schedules</h2>
            <p class="text-muted mb-0">View or remove finalized departmental exam timetables</p>
        </div>
        <a href="{{ route('department.scheduler.index', ['activeTab' => 'exam']) }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Generate New Exam Schedule
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if($groups->isEmpty())
        <div class="card border-0 shadow-sm p-5 text-center">
            <i class="bi bi-calendar-x text-muted mb-3" style="font-size: 3rem;"></i>
            <h5 class="text-muted">No locked exam schedules found for your department.</h5>
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
                                <i class="bi bi-book me-1"></i> Total Exams: {{ $group->total_exams }}
                            </p>
                            <p class="small text-muted mb-4">
                                <i class="bi bi-clock-history me-1"></i> Last updated: {{ \Carbon\Carbon::parse($group->latest_update)->format('M d, Y') }}
                            </p>

                            <div class="d-grid gap-2">
                                <a href="{{ route('department.exam-scheduler.show', ['year' => $group->year, 'semester' => $group->semester, 'section' => $group->section]) }}" 
                                   class="btn btn-outline-info">
                                    <i class="bi bi-eye me-1"></i> View Exam Timetable
                                </a>
                                
                                <form action="{{ route('department.exam-scheduler.destroyGroup') }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this entire exam schedule? This will also release all invigilators.')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="year" value="{{ $group->year }}">
                                    <input type="hidden" name="semester" value="{{ $group->semester }}">
                                    <input type="hidden" name="section" value="{{ $group->section }}">
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-trash me-1"></i> Delete Schedule
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .card { transition: transform 0.2s ease; }
    .card:hover { transform: translateY(-3px); }
</style>
@endsection
