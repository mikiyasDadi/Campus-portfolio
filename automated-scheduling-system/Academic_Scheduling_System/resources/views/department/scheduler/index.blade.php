@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-primary">Academic Scheduling Portal</h2>
    <p class="text-muted">Configure periods and upload pairings for Class, Lab, or Exam scheduling.</p>
</div>

<ul class="nav nav-pills mb-4 bg-white p-2 rounded shadow-sm" id="schedulerTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ ($activeTab ?? 'class') == 'class' ? 'active' : '' }} fw-bold px-4 text-dark" id="class-tab" data-bs-toggle="pill" data-bs-target="#class-scheduler" type="button" role="tab">
            <i class="bi bi-calendar3 me-2"></i>Class & Lab Scheduler
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ ($activeTab ?? '') == 'exam' ? 'active' : '' }} fw-bold px-4 text-dark" id="exam-tab" data-bs-toggle="pill" data-bs-target="#exam-scheduler" type="button" role="tab">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Exam Scheduler
        </button>
    </li>
</ul>

<div class="tab-content" id="schedulerTabsContent">
    
    {{-- Class/Lab Tab Pane --}}
    <div class="tab-pane fade {{ ($activeTab ?? 'class') == 'class' ? 'show active' : '' }}" id="class-scheduler" role="tabpanel">
        <div class="row">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Step 1: Class/Lab Configuration & Upload</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('department.scheduler.preview') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Year of Study</label>
                                    <select name="year" class="form-select" required>
                                        <option value="" selected disabled>Select Year</option>
                                        @foreach(range(1, 5) as $y)
                                            <option value="{{ $y }}">Year {{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Semester</label>
                                    <select name="semester" class="form-select" required>
                                        <option value="" selected disabled>Select Semester</option>
                                        <option value="1">1st Semester</option>
                                        <option value="2">2nd Semester</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Section/Group</label>
                                    <input type="text" name="section" class="form-control" placeholder="e.g. A, B, 1" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Upload CSV File (Class/Lab)</label>
                                <input type="file" name="csv_file" class="form-control" required>
                                <div class="form-text mt-2">
                                    <strong>Required Format:</strong> <code>course_code, lec_instructor_id, lab_instructor_id</code>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                <i class="bi bi-file-earmark-check me-2"></i> Validate Class Inputs
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="alert alert-info border-0 shadow-sm h-100">
                    <h5 class="alert-heading fw-bold"><i class="bi bi-info-circle-fill me-2"></i> Class Scheduling Rules</h5>
                    <hr>
                    <p class="small">The engine automatically applies these constraints:</p>
                    <ul class="small mb-0">
                        <li><strong>Lectures:</strong> Morning Sessions only (Before Noon).</li>
                        <li><strong>Labs:</strong> Afternoon Sessions only.</li>
                        <li><strong>Tutorials:</strong> Can be scheduled in any session.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Exam Tab Pane --}}
    <div class="tab-pane fade {{ ($activeTab ?? '') == 'exam' ? 'show active' : '' }}" id="exam-scheduler" role="tabpanel">
        <div class="row">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Step 1: Exam Configuration & Upload</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('department.exam-scheduler.process') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Year of Study</label>
                                    <select name="year" class="form-select" required>
                                        <option value="" selected disabled>Select Year</option>
                                        @foreach(range(1, 5) as $y)
                                            <option value="{{ $y }}">Year {{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Semester</label>
                                    <select name="semester" class="form-select" required>
                                        <option value="" selected disabled>Select Semester</option>
                                        <option value="1">1st Semester</option>
                                        <option value="2">2nd Semester</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Section/Group</label>
                                    <input type="text" name="section" class="form-control" placeholder="e.g. A, B, 1" required>
                                </div>
                            </div>

                           <div class="mb-4">
                                <label class="form-label fw-bold">Upload Exam Pairings (CSV)</label>
                                <input type="file" name="csv_file" class="form-control" required>
                                <div class="form-text mt-2 text-primary">
                                    <strong>Required Format:</strong> <code>course_code, inv_1_username, inv_2_username</code>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-info text-white w-100 py-2 fw-bold shadow-sm">
                                <i class="bi bi-clipboard-check me-2"></i> Validate Exam Inputs
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="alert alert-warning border-0 shadow-sm h-100">
                    <h5 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Exam Scheduling Rules</h5>
                    <hr>
                    <p class="small">The exam engine applies specialized constraints:</p>
                    <ul class="small mb-0">
                        <li><strong>Periods:</strong> Only two slots per day (Morning/Afternoon).</li>
                        <li><strong>Availability:</strong> Cross-matches with global staff busy periods.</li>
                        <li><strong>Exclusions:</strong> Respects the "Exam Overlap Exclusion" sets defined previously.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #schedulerTabs .nav-link {
        background-color: #f8f9fa;
        color: #000000 !important;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }
    
    #schedulerTabs .nav-link.active {
        background-color: #e9ecef !important;
        color: #000000 !important;
        border-color: #0d6efd !important;
        border-bottom: 3px solid #0d6efd !important;
    }

    #schedulerTabs .nav-link:hover:not(.active) {
        background-color: #ffffff;
        color: #000000 !important;
    }
</style>
@endsection