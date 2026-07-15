<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\Department\InstructorController;
use App\Http\Controllers\Department\InstructorAvailabilityController;
use App\Http\Controllers\Department\ClassSchedulerController;
use App\Http\Controllers\Department\PeriodController;
use App\Http\Controllers\Department\ExamExclusionController;
use App\Http\Controllers\Department\ExamInstructorAvailabilityController;
use App\Http\Controllers\Department\ExamSchedulerController;
use App\Http\Controllers\FacultyHeadController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;


/*
|--------------------------------------------------------------------------
| Public & Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () { return view('welcome'); });

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');

    /* --- Admin Routes --- */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [DashboardController::class, 'users'])->name('users');
        Route::patch('/users/{user}/role', [DashboardController::class, 'updateUserDetails'])->name('users.update-role');
        Route::patch('/users/{user}/toggle', [DashboardController::class, 'toggleUserStatus'])->name('users.toggle-status');
        Route::post('/users/import', [DashboardController::class, 'importOfficialRecords'])->name('users.import');
        
        Route::get('/users/faculty-assignments', [DashboardController::class, 'facultyAssignments'])->name('users.faculty-assignments');
        Route::post('/assign-faculty/{id}', [DashboardController::class, 'assignFaculty'])->name('assign-faculty');

        Route::get('/departments', [DashboardController::class, 'departments'])->name('departments');
        Route::post('/departments', [DashboardController::class, 'storeDepartment'])->name('departments.store');
        Route::patch('/departments/{department}', [DashboardController::class, 'updateDepartment'])->name('departments.update');
        Route::delete('/departments/{department}', [DashboardController::class, 'destroyDepartment'])->name('departments.destroy');
    });

    /* --- Role Dashboards --- */
    Route::get('/faculty/dashboard', [FacultyHeadController::class, 'dashboard'])->name('faculty.dashboard');
    Route::get('/faculty/departments/{department}/class-schedules', [FacultyHeadController::class, 'classSchedules'])->name('faculty.class-schedules');
    Route::get('/faculty/departments/{department}/exam-schedules', [FacultyHeadController::class, 'examSchedules'])->name('faculty.exam-schedules');
    Route::get('/faculty/departments/{department}/class-schedules/{year}/{semester}/{section}', [FacultyHeadController::class, 'showClassSchedule'])->name('faculty.class-schedules.show');
    Route::get('/faculty/departments/{department}/exam-schedules/{year}/{semester}/{section}', [FacultyHeadController::class, 'showExamSchedule'])->name('faculty.exam-schedules.show');
    Route::post('/faculty/comments', [FacultyHeadController::class, 'storeComment'])->name('faculty.comments.store');
    Route::post('/department/comments/{id}/read', [FacultyHeadController::class, 'markCommentRead'])->name('department.comments.read');
    
    Route::get('/department/dashboard', [DashboardController::class, 'departmentDashboard'])->name('department.dashboard');

    /* --- Instructor Routes --- */
    Route::prefix('instructor')->name('instructor.')->group(function () {
        Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/download-schedule', [InstructorDashboardController::class, 'downloadSchedule'])->name('download-schedule');
        Route::get('/faculty-overview/{department}', [InstructorDashboardController::class, 'facultySchedule'])->name('faculty-overview');
    });

    /* --- Student Routes --- */
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/download-schedule', [StudentDashboardController::class, 'downloadSchedule'])->name('download-schedule');
    });
});

/*
|--------------------------------------------------------------------------
| Department Head Routes (Prefix: /department)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('department')->name('department.')->group(function () {
    
    // Course Management
    Route::resource('courses', CourseController::class)->except(['create', 'show', 'edit']);

    // Room Management
    Route::resource('rooms', \App\Http\Controllers\Department\RoomController::class)->except(['create', 'show', 'edit']);

    // Period Management
    Route::get('/periods', [PeriodController::class, 'index'])->name('periods.index');
    Route::put('/periods', [PeriodController::class, 'update'])->name('periods.update');

    // Scheduler
    
Route::get('/scheduler', [ClassSchedulerController::class, 'index'])->name('scheduler.index');

// Use match to allow both the initial POST and subsequent GET (refresh)
Route::match(['get', 'post'], '/scheduler/preview', [ClassSchedulerController::class, 'preview'])->name('scheduler.preview');
Route::post('/scheduler/generate', [ClassSchedulerController::class, 'generate'])->name('scheduler.generate');
    
// The Generator Hub
    Route::get('/class-scheduler', [ClassSchedulerController::class, 'index'])->name('scheduler.index');
    
// ADD THIS LINE HERE:
    Route::delete('/scheduler/destroy-group', [ClassSchedulerController::class, 'destroyGroup'])->name('scheduler.destroyGroup');

    // The Locked Schedules List (This is what your Dashboard button will link to)
    Route::get('/scheduler/locked', [ClassSchedulerController::class, 'lockedIndex'])->name('scheduler.locked');
    
    // Individual Timetable View
    Route::get('/scheduler/show/{year}/{semester}/{section}', [ClassSchedulerController::class, 'show'])->name('scheduler.show');

// ADD THIS LINE:
    Route::post('/scheduler/store', [ClassSchedulerController::class, 'store'])->name('scheduler.store');
   



   // Exam Exclusions
    Route::get('exam-exclusions', [ExamExclusionController::class, 'index'])->name('exclusions.index');
    Route::post('exam-exclusions', [ExamExclusionController::class, 'store'])->name('exclusions.store');
    // Ensure the {id} parameter is present here
    Route::delete('exam-exclusions/{id}', [ExamExclusionController::class, 'destroy'])->name('exclusions.destroy');

    // Existing Scheduler Index (The Hub)
    Route::get('scheduler/exam-hub', [ExamSchedulerController::class, 'index'])->name('exam-scheduler.index');

    // New Exam Scheduler Routes
    Route::get('exam-scheduler', [ExamSchedulerController::class, 'examInput'])->name('exam-scheduler.input');
    Route::match(['get', 'post'], 'exam-scheduler/process', [ExamSchedulerController::class, 'processCsv'])->name('exam-scheduler.process');
    Route::match(['get', 'post'], 'exam-scheduler/initiate', [ExamSchedulerController::class, 'initiateEngine'])->name('exam-scheduler.initiate');
    Route::post('exam-scheduler/save', [ExamSchedulerController::class, 'saveSchedule'])->name('exam-scheduler.save');
    Route::get('exam-scheduler/locked', [ExamSchedulerController::class, 'lockedIndex'])->name('exam-scheduler.locked');
    Route::get('exam-scheduler/show/{year}/{semester}/{section}', [ExamSchedulerController::class, 'show'])->name('exam-scheduler.show');
    Route::delete('exam-scheduler/destroy-group', [ExamSchedulerController::class, 'destroyGroup'])->name('exam-scheduler.destroyGroup');


// Instructor Exam Availability Routes
    Route::get('exam-instructor-availability', [ExamInstructorAvailabilityController::class, 'index'])
        ->name('exam-instructor-avail.index');
        
    Route::post('exam-instructor-availability', [ExamInstructorAvailabilityController::class, 'update'])
        ->name('exam-instructor-avail.update');



    // --- INSTRUCTORS & AVAILABILITY ---
    
    // 1. Search (Must be above resource)
    Route::get('instructors/search', [InstructorController::class, 'search'])->name('instructors.search');

    // 2. Reset All
    Route::post('instructors/availability/reset-all', [InstructorAvailabilityController::class, 'resetAll'])
        ->name('instructors.reset_all');

    // 3. Individual Instructor Availability (FIX: Removed problematic user_id binding)
    Route::get('instructors/{instructor}/availability', [InstructorAvailabilityController::class, 'index'])
        ->name('instructors.availability');

    Route::post('instructors/{instructor}/availability/toggle', [InstructorAvailabilityController::class, 'toggle'])
        ->name('instructors.availability.toggle');

    Route::post('instructors/{instructor}/availability/reset', [InstructorAvailabilityController::class, 'reset'])
        ->name('instructors.availability.reset');

    // 4. Standard Instructor CRUD (FIX: Using standard ID binding)
    Route::resource('instructors', InstructorController::class);




});