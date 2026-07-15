<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Academic Scheduling System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-custom { background-color: #0056b3; padding: 0.8rem 2rem; }
        .card-auth { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .btn-university { background-color: #0056b3; color: white; font-weight: bold; border-radius: 8px; padding: 10px; transition: 0.3s; }
        .btn-university:hover { background-color: #004494; color: white; }
    </style>
</head>
<body>
    <nav class="navbar navbar-custom mb-5 shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand text-white fw-bold">
                <i class="bi bi-mortarboard-fill"></i> Academic Scheduling System
            </span>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card card-auth p-4 bg-white">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold mb-1">University Login</h3>
                        <p class="text-muted small">Authenticate to access your dashboard</p>
                        @if(session('success'))
    <div class="alert alert-success border-0 small fw-bold">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
    </div>
@endif
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small">ID Number</label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Enter your ID" required autofocus>
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        
                        <button type="submit" class="btn btn-university w-100 mb-3">Login</button>
                        
                        <div class="text-center">
                            <span class="text-muted small">New to the system?</span>
                            <a href="{{ route('register') }}" class="small fw-bold text-decoration-none" style="color: #0056b3;">Register with ID</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>