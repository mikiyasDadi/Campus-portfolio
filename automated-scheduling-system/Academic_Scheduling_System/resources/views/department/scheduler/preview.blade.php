@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Class and Lab Validation Preview</h2>
            <p class="text-muted">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3">Year {{ $request->year }}</span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 ms-1">
                    {{ $request->semester == 1 ? '1st' : '2nd' }} Semester
                </span>
            </p>
        </div>
        <a href="{{ route('department.scheduler.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Upload
        </a>
    </div>

    @php
        // Check if there are any errors in the preview data to prevent submission
        $hasErrors = collect($previewData)->contains(function($item) {
            return $item['status'] !== 'Ready';
        });
    @endphp

    @if(count($previewErrors) > 0 || $hasErrors)
        <div class="alert alert-danger border-0 shadow-sm mb-4 p-3">
            <div class="d-flex">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div>
                    <h5 class="alert-heading fw-bold">Validation Issues Detected</h5>
                    <p class="small mb-0">Please fix the highlighted errors in your CSV and re-upload before initiating the scheduler.</p>
                    @if(count($previewErrors) > 0)
                        <ul class="mb-0 small mt-2">
                            @foreach($previewErrors as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Course (Code & Name)</th>
                        <th>Hours (L/T/P)</th>
                        <th>Lec/Tut Instructor</th>
                        <th>Lab Instructor</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($previewData as $item)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-primary">{{ $item['csv_course_id'] }}</div>
                        <div class="text-dark small">{{ $item['course_name'] }}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">L:{{ $item['hours']['lec'] }}</span>
                        <span class="badge bg-light text-dark border">T:{{ $item['hours']['tut'] }}</span>
                        <span class="badge bg-light text-dark border">P:{{ $item['hours']['lab'] }}</span>
                    </td>
                    <td>
                        <div class="fw-bold">{{ $item['lec_name'] ?? '---' }}</div>
                        <code class="small text-muted">{{ $item['csv_lec_inst'] }}</code>
                    </td>
                    <td>
                        @if($item['hours']['lab'] > 0)
                            @if($item['lab_name'])
                                <div class="fw-bold">{{ $item['lab_name'] }}</div>
                                <code class="small text-muted">{{ $item['csv_lab_inst'] }}</code>
                            @else
                                <span class="text-danger fw-bold small"><i class="bi bi-x-circle me-1"></i> Required</span>
                            @endif
                        @else
                            <span class="text-muted small italic">Not Required</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item['status'] == 'Ready')
                            <span class="badge bg-success px-3 rounded-pill">Ready</span>
                        @elseif($item['status'] == 'Mismatch')
                            <span class="badge bg-danger px-3 rounded-pill">Mismatch</span>
                        @else
                            <span class="badge bg-warning text-dark px-3 rounded-pill">Error</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-white py-4 text-end border-top">
            <form action="{{ route('department.scheduler.generate') }}" method="POST" id="generateForm">
                @csrf
                <input type="hidden" name="year" value="{{ $request->year }}">
                <input type="hidden" name="semester" value="{{ $request->semester }}">
                <input type="hidden" name="section" value="{{ $request->section }}">
                <input type="hidden" name="schedule_payload" value="{{ json_encode($previewData) }}">

                @if($hasErrors)
                    <button type="button" class="btn btn-secondary px-5 py-2 disabled" data-bs-toggle="tooltip" title="Fix errors to proceed">
                        <i class="bi bi-lock-fill me-2"></i> Fix Errors to Initiate
                    </button>
                @else
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm" id="submitBtn">
                        <span id="btnText"><i class="bi bi-cpu me-2"></i> Initiate Scheduler</span>
                        <span id="btnLoader" class="d-none">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Generating Schedule...
                        </span>
                    </button>
                @endif
            </form>
        </div>
    </div>
</div>

{{-- Simple Script to handle the loading state --}}
<script>
    document.getElementById('generateForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const text = document.getElementById('btnText');
        const loader = document.getElementById('btnLoader');
        
        btn.classList.add('disabled');
        text.classList.add('d-none');
        loader.classList.remove('d-none');
    });
</script>
@endsection