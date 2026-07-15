@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h1 class="fw-bold">Faculty Head Assignments</h1>
    <p class="text-muted">Assign Faculty Heads (Role 2) to their respective Faculties</p>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
@endif

<div class="card shadow-sm border-0 p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>Faculty Head Name</th>
                    <th>Email</th>
                    <th>Current Faculty Assignment</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facultyHeads as $head)
                <tr>
                    <td class="fw-bold">{{ $head->first_name }} {{ $head->last_name }}</td>
                    <td>{{ $head->email }}</td>
                    <td>
                        @if($head->faculty)
                            <span class="badge bg-primary px-3">{{ $head->faculty->name }}</span>
                        @else
                            <span class="text-danger small">Unassigned</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <form action="{{ route('admin.assign-faculty', $head->id) }}" method="POST" class="d-flex justify-content-end gap-2">   @csrf
                            <select name="faculty_id" class="form-select form-select-sm" style="width: 200px;" required>
                                <option value="" disabled selected>Select Faculty...</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ $head->faculty_id == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name }} ({{ $faculty->code }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-dark">Assign</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No Faculty Heads found (Role ID 2).</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection