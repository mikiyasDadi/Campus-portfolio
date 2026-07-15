@extends('layouts.admin')

@section('content')
<style>
    /* Animation for the top Stat Cards */
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important;
    }

    /* Animation for the larger Action Cards */
    .action-card {
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid transparent !important;
    }
    .action-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 20px rgba(0, 86, 179, 0.15) !important;
        border-color: rgba(0, 86, 179, 0.3) !important;
    }
    /* Change the icon background color on hover */
    .action-card:hover .icon-box {
        background-color: #0056b3 !important;
    }
    .action-card:hover .icon-box i {
        color: white !important;
    }
</style>

<div class="mb-4">
    <h1 class="fw-bold">Dashboard</h1>
    <p class="text-muted">Welcome back, Admin! Manage your system overview here.</p>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-sm p-4 bg-white rounded-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted fw-bold small mb-1 text-uppercase">Total Users</p>
                    <h2 class="fw-bold mb-0">{{ $totalUsers }}</h2>
                </div>
                <div class="bg-light p-2 rounded">
                    <i class="bi bi-people text-primary fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card stat-card border-0 shadow-sm p-4 bg-white rounded-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted fw-bold small mb-1 text-uppercase">Departments</p>
                    <h2 class="fw-bold mb-0">{{ $totalDepartments }}</h2>
                </div>
                <div class="bg-light p-2 rounded">
                    <i class="bi bi-building text-primary fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card action-card border-0 shadow-sm h-100 p-4 bg-white rounded-3" onclick="window.location='{{ route('admin.users') }}'">
            <div class="icon-box bg-primary bg-opacity-10 p-3 rounded-3 mb-3" style="width: fit-content; transition: all 0.3s ease;">
                <i class="bi bi-person-gear text-primary fs-4"></i>
            </div>
            <h5 class="fw-bold">User Management</h5>
            <p class="text-muted small mb-4">Assign roles or suspend system users to maintain security.</p>
            <div class="mt-auto">
                <a href="{{ route('admin.users') }}" class="btn w-100 py-2 fw-bold text-white" style="background-color: #0056b3;">
                    Go to Users
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card action-card border-0 shadow-sm h-100 p-4 bg-white rounded-3" onclick="window.location='{{ route('admin.departments') }}'">
            <div class="icon-box bg-primary bg-opacity-10 p-3 rounded-3 mb-3" style="width: fit-content; transition: all 0.3s ease;">
                <i class="bi bi-diagram-3 text-primary fs-4"></i>
            </div>
            <h5 class="fw-bold">Manage Departments</h5>
            <p class="text-muted small mb-4">Create, update, or remove academic department structures.</p>
            <div class="mt-auto">
                <a href="{{ route('admin.departments') }}" class="btn w-100 py-2 fw-bold text-white" style="background-color: #0056b3;">
                    View Departments
                </a>
            </div>
        </div>
    </div>
</div>
@endsection