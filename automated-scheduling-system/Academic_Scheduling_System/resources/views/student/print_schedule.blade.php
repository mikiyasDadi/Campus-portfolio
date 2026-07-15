<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Schedule - {{ $user->full_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .btn-print { display: none; }
            body { background-color: #fff; padding: 0; }
            .container { max-width: 100%; margin: 0; padding: 0; }
            table { border-collapse: collapse !important; }
            th, td { border: 1px solid #000 !important; }
            .bg-university { background-color: #000 !important; color: #fff !important; }
            .bg-primary-subtle { background-color: #eee !important; border-left: 5px solid #000 !important; }
            .bg-warning-subtle { background-color: #ddd !important; border-left: 5px solid #666 !important; }
            .bg-success-subtle { background-color: #f1f1f1 !important; border-left: 5px solid #333 !important; }
        }
        
        .bg-university { background-color: #0056b3; color: #fff; }
        .bg-primary-subtle { background-color: #e7f1ff; border-left: 5px solid #0d6efd; }
        .bg-warning-subtle { background-color: #fff3cd; border-left: 5px solid #ffc107; }
        .bg-success-subtle { background-color: #d1e7dd; border-left: 5px solid #198754; }
        
        table th, table td {
            text-align: center;
            vertical-align: middle;
            font-size: 0.8rem;
            padding: 8px !important;
        }
        
        .class-card {
            padding: 5px;
            border-radius: 4px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
        }
    </style>
</head>
<body class="bg-light p-4">
    <div class="container bg-white shadow-sm p-5 rounded">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1">Academic Timetable</h1>
                <p class="text-muted mb-0">Student: <strong>{{ $user->full_name }}</strong> | Year {{ $year }} | Semester {{ $semester }}</p>
                <p class="text-muted small">Department: {{ $user->department->name ?? 'N/A' }}</p>
            </div>
            <button onclick="window.print()" class="btn btn-primary btn-print">
                Print My Schedule
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="bg-university">
                    <tr>
                        <th style="width: 100px;">Time</th>
                        @foreach($days as $day)
                            <th>{{ $day }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeSlots as $index => $slot)
                        @php $period = $index + 1; @endphp
                        <tr>
                            <td class="bg-light fw-bold">
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}<br>
                                -<br>
                                {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                            </td>
                            @foreach($days as $day)
                                <td style="height: 100px; width: 17%;">
                                    @if(isset($timetable[$day][$period]))
                                        @php $class = $timetable[$day][$period]; @endphp
                                        <div class="class-card {{ $class->type == 'Lab' ? 'bg-warning-subtle' : ($class->type == 'Tutorial' ? 'bg-success-subtle' : 'bg-primary-subtle') }}">
                                            <div class="fw-bold">{{ $class->course->course_name ?? 'N/A' }}</div>
                                            <div class="small">{{ $class->course_code }} | {{ $class->type }}</div>
                                            <div class="mt-1 small fw-medium">
                                                <i class="bi bi-person text-muted me-1"></i>
                                                {{ $class->instructor->full_name ?? 'N/A' }}
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 small text-muted border-top pt-3">
            <p class="mb-1"><strong>Note:</strong> Blue = Lecture, Yellow = Lab, Green = Tutorial.</p>
            <p class="mb-0">Academic Scheduling System © {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
