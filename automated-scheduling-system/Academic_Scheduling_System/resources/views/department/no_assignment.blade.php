@extends('layouts.admin')

@section('content')
<div class="container text-center py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <i class="bi bi-exclamation-triangle text-warning display-1"></i>
            <h2 class="fw-bold mt-4">Department Not Assigned</h2>
            <p class="text-muted">Your account is registered as a Department Head, but you haven't been assigned to a specific department yet. Please contact the System Administrator to finalize your setup.</p>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary px-4 fw-bold">Logout</button>
            </form>
        </div>
    </div>
</div>
@endsection