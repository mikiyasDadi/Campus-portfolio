@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">

    {{-- CASE 1: VIEWING A SPECIFIC GRID (When $meta is present) --}}
    @if(isset($meta) && isset($schedule))
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-primary">Saved Academic Timetable</h2>
                <p class="text-muted">
                    <i class="bi bi-shield-lock me-1"></i>
                    Finalized schedule for <strong>Year {{ $meta['year'] }}</strong>, 
                    <strong>Semester {{ $meta['semester'] }}</strong>
                </p>
            </div>
            <div class="d-print-none">
                <button onclick="window.print()" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-printer me-1"></i> Print PDF
                </button>
                <a href="{{ route('department.scheduler.locked') }}" class="btn btn-secondary px-4">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 text-center">
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
                                            @if($cell['type'] == 'Lab') border-danger bg-danger bg-opacity-10 
                                            @elseif($cell['type'] == 'Tut') border-warning bg-warning bg-opacity-10
                                            @else border-primary bg-primary bg-opacity-10 
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

    {{-- CASE 2: LIST VIEW (Management Dashboard) --}}
    @else
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">Locked Schedules</h2>
                <p class="text-muted mb-0">View or remove finalized departmental timetables</p>
            </div>
            {{-- OPTION: Redirect to Generate (Scheduler Index) --}}
            <a href="{{ route('department.scheduler.index') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Generate New Schedule
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        @if(!isset($groups) || $groups->isEmpty())
            <div class="card border-0 shadow-sm p-5 text-center">
                <i class="bi bi-calendar-x text-muted mb-3" style="font-size: 3rem;"></i>
                <h5 class="text-muted">No locked schedules found for your department.</h5>
            </div>
        @else
            <div class="row">
                @foreach($groups as $group)
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100 border-top border-4 border-primary">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h4 class="fw-bold mb-0">Year {{ $group->year }}</h4>
                                        <div class="d-flex gap-2 mt-1">
                                            <span class="badge bg-light text-primary border">Semester {{ $group->semester }}</span>
                                            <span class="badge bg-light text-success border">Section {{ $group->section }}</span>
                                        </div>
                                    </div>
                                    <i class="bi bi-shield-lock-fill text-success fs-3"></i>
                                </div>
                                
                                <p class="small text-muted mb-4">
                                    <i class="bi bi-clock-history me-1"></i> Last updated: {{ \Carbon\Carbon::parse($group->latest_update)->format('M d, Y') }}
                                </p>

                                <div class="d-grid gap-2">
                                    {{-- View Option --}}
                                    <a href="{{ route('department.scheduler.show', ['year' => $group->year, 'semester' => $group->semester, 'section' => $group->section]) }}" 
                                       class="btn btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> View Grid
                                    </a>

                                    {{-- OPTION: Delete Schedule --}}
                                    <form action="{{ route('department.scheduler.destroyGroup') }}" method="POST" 
                                          onsubmit="return confirm('Are you sure? This will delete the entire Year {{ $group->year }} Sem {{ $group->semester }} Section {{ $group->section }} schedule.');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="year" value="{{ $group->year }}">
                                        <input type="hidden" name="semester" value="{{ $group->semester }}">
                                        <input type="hidden" name="section" value="{{ $group->section }}">
                                        <button type="submit" class="btn btn-link text-danger btn-sm w-100 mt-2 text-decoration-none">
                                            <i class="bi bi-trash me-1"></i> Delete & Unlock
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>

<style>
    .table-bordered td, .table-bordered th { border: 1px solid #dee2e6 !important; }
    .bg-opacity-10 { --bs-bg-opacity: 0.1; }
    .card { transition: transform 0.2s ease; }
    .card:hover { transform: translateY(-3px); }
    @media print {
        @page { size: landscape; }
        .navbar, .sidebar, .d-print-none { display: none !important; }
        .bg-primary { background-color: #0d6efd !important; color: #fff !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection