<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Scheduling System | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* Base styles */
        body { 
            background-color: #f4f7f6; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        /* Shared Blue Brand Style */
        .bg-university { background-color: #0056b3; }
        
        /* Header styling */
        .navbar-custom { background-color: #0056b3; padding: 0.5rem 1.5rem; }
        .nav-link { 
            color: rgba(255,255,255,0.8) !important; 
            font-weight: 500; 
            transition: all 0.3s ease;
        }
        .nav-link:hover, .dropdown-item:hover { color: #fff !important; background-color: rgba(255,255,255,0.1); }
        .nav-link.active { 
            color: #fff !important; 
            border-bottom: 2px solid #fff; 
        }
        
        /* Dropdown Styling */
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 8px;
        }
        .dropdown-item {
            font-weight: 500;
            color: #333;
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
        }
        .dropdown-item.active {
            background-color: #0056b3;
            color: #fff !important;
        }
        
        /* User Profile styles */
        .user-badge { 
            background-color: #fff; 
            color: #0056b3; 
            font-weight: bold; 
            font-size: 0.7rem; 
            padding: 2px 8px; 
            border-radius: 4px; 
            text-transform: uppercase;
        }
        
        main { flex: 1; }

        .footer-custom {
            background-color: #0056b3;
            color: rgba(255,255,255,0.9);
        }

        .modal-content { border-radius: 12px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom shadow-sm sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand text-white d-flex align-items-center" href="{{ route('dashboard') }}">
                <i class="bi bi-mortarboard-fill fs-3 me-2"></i>
                <div class="d-none d-sm-block">
                    <div class="fw-bold lh-1">Academic Scheduling System</div>
                    <div style="font-size: 0.65rem; opacity: 0.8;">University Management Platform</div>
                </div>
            </a>
            
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <i class="bi bi-list fs-2"></i>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <div class="d-flex align-items-center ms-auto gap-4">
                    <ul class="navbar-nav flex-row gap-2">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') || request()->routeIs('department.dashboard') || request()->routeIs('faculty.dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        
                        @if(Auth::user()->role_id == 2)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('faculty.dashboard') ? 'active' : '' }}" href="{{ route('faculty.dashboard') }}">Faculty Departments</a>
                            </li>
                        @endif

                        @if(Auth::user()->role_id == 1)
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->is('admin/users*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Users
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.users') && !request()->is('*/faculty-assignments') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                                            <i class="bi bi-people me-2"></i> Management
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('admin.users.faculty-assignments') ? 'active' : '' }}" href="{{ route('admin.users.faculty-assignments') }}">
                                            <i class="bi bi-person-badge me-2"></i> Faculty Assignments
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('admin/departments*') ? 'active' : '' }}" href="{{ route('admin.departments') }}">Departments</a>
                            </li>
                        @endif

                        @if(Auth::user()->role_id == 3)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('department.instructors.index') ? 'active' : '' }}" href="{{ route('department.instructors.index') }}">My Instructors</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('department.scheduler.index') ? 'active' : '' }}" href="{{ route('department.scheduler.index') }}">Scheduling Portal</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('department.courses.index') ? 'active' : '' }}" href="{{ route('department.courses.index') }}">Courses</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('department.rooms.index') ? 'active' : '' }}" href="{{ route('department.rooms.index') }}">Rooms</a>
                            </li>
                        @endif
                    </ul>

                    <div class="d-flex align-items-center gap-3 border-start ps-4">
                        <div class="text-white text-end d-none d-lg-block">
                            <div class="small fw-bold lh-1">{{ Auth::user()->full_name }}</div>
                            <span class="user-badge">
                                @if(Auth::user()->role_id == 1) ADMIN
                                @elseif(Auth::user()->role_id == 2) FACULTY HEAD
                                @elseif(Auth::user()->role_id == 3) DEPT HEAD
                                @elseif(Auth::user()->role_id == 4) INSTRUCTOR
                                @elseif(Auth::user()->role_id == 5) STUDENT
                                @endif
                            </span>
                        </div>
                        <button type="button" class="btn btn-light btn-sm fw-bold shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#logoutConfirmationModal">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li><i class="bi bi-x-circle me-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer-custom py-4 mt-auto shadow-lg">
        <div class="container text-center">
            <span class="small">© {{ date('Y') }} <span class="text-white fw-bold">Academic Scheduling System</span></span>
        </div>
    </footer>

    <div class="modal fade" id="logoutConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-circle text-warning fs-1"></i>
                    </div>
                    <h5 class="fw-bold">Are you sure?</h5>
                    <p class="text-muted mb-0">You are about to end your session.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light px-4 fw-bold border" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>