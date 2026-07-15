@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bold">Exam Overlap Exclusion Sets</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <form action="{{ route('department.exclusions.store') }}" method="POST">
                @csrf
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Select Courses for New Set</h5>
                    <button type="submit" class="btn btn-primary shadow-sm px-4 fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Create Exclusion Set
                    </button>
                </div>

                <div style="max-height: 75vh; overflow-y: auto; padding-right: 10px;">
                    @foreach($courses as $deptYearLabel => $semesters)
                        <div class="card border-0 shadow-sm mb-4 border-top border-primary border-4">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0 fw-bold text-primary">
                                    <i class="bi bi-journal-check me-2"></i>{{ strtoupper($deptYearLabel) }}
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                @foreach($semesters as $semester => $courseList)
                                    <div class="px-4 py-3 border-bottom">
                                        <div class="x-small fw-bold text-uppercase text-secondary mb-2">Semester {{ $semester }}</div>
                                        <div class="row row-cols-1 g-2">
                                            @foreach($courseList as $course)
                                                <div class="col">
                                                    <div class="form-check p-2 rounded hover-check">
                                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="course_ids[]" value="{{ $course->id }}" id="c{{ $course->id }}">
                                                        <label class="form-check-label small d-block" for="c{{ $course->id }}">
                                                            <span class="fw-bold">{{ $course->course_code }}</span> — {{ $course->course_name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>
        </div>

        <div class="col-lg-5">
            <h5 class="fw-bold mb-3">Active Exclusion Sets</h5>
            <div style="max-height: 80vh; overflow-y: auto;">
                @forelse($exclusionSets as $set)
                    <div class="card mb-3 border-0 shadow-sm border-start border-4 border-info">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">{{ $set->set_name }}</h6>
                                <form action="{{ route('department.exclusions.destroy', $set->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" onclick="return confirm('Delete this set?')">
                                        <i class="bi bi-trash3 fs-5"></i>
                                    </button>
                                </form>
                            </div>

                            <table class="table table-sm table-borderless mb-0">
                                <thead class="x-small text-muted text-uppercase border-bottom">
                                    <tr>
                                        <th>Course</th>
                                        <th>Year</th>
                                        <th>Department</th>
                                    </tr>
                                </thead>
                               {{-- Find the table body in the Right Column (Active Exclusion Sets) --}}
<tbody>
    @foreach($set->courses as $c)
        <tr>
            <td class="small fw-bold py-2">{{ $c->course_code }}</td>
            <td class="small">Year {{ $c->year }}</td>
            <td class="small text-muted">
                {{-- Changed 'department_name' to 'name' --}}
                {{ $c->department->name ?? 'N/A' }}
            </td>
        </tr>
    @endforeach
</tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted bg-white rounded shadow-sm">
                        <i class="bi bi-shield-slash fs-1 opacity-25"></i>
                        <p class="mt-2">No exclusion sets created yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .x-small { font-size: 0.7rem; }
    .hover-check:hover { background-color: #f8faff; cursor: pointer; }
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
</style>
@endsection