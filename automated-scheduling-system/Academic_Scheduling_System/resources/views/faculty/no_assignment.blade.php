@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg p-5">
                <i class="bi bi-exclamation-triangle text-warning mb-4" style="font-size: 5rem;"></i>
                <h2 class="fw-bold text-dark">No Faculty Assigned</h2>
                <p class="text-muted mb-4">You are a Faculty Head but have not been assigned to a specific faculty yet. Please contact the administrator to complete your profile setup.</p>
                <div class="d-grid">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary py-3 fw-bold shadow-sm">
                        <i class="bi bi-arrow-left me-2"></i> Go Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
