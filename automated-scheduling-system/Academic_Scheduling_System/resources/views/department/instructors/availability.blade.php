@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Availability for {{ $instructor->first_name }} {{ $instructor->last_name }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Time Slot</th>
                            @foreach($days as $dayId => $dayName)
                                <th>{{ $dayName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dynamicSlots as $slot)
                            <tr>
                                <td class="bg-light fw-bold">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                </td>
                                @foreach($days as $dayId => $dayName)
                                    @php
                                        // Find if a record exists for this specific day/slot
                                        $record = $availabilities->where('day_of_week', $dayId)
                                                                 ->where('time_slot_id', $slot->id)
                                                                 ->first();
                                        
                                        $isMyDept = $record && $record->department_id == auth()->user()->department_id;
                                        $isOtherDept = $record && $record->department_id != auth()->user()->department_id;
                                    @endphp

                                    <td class="availability-cell p-0" 
                                        data-day="{{ $dayId }}" 
                                        data-slot="{{ $slot->id }}"
                                        style="height: 60px; position: relative;">
                                        
                                        @if($isOtherDept)
                                            {{-- LOCKED STATE --}}
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary text-white opacity-75" 
                                                 title="Locked: Busy in another department" 
                                                 style="cursor: not-allowed;">
                                                <small><i class="bi bi-lock-fill"></i> Other Dept</small>
                                            </div>
                                        @else
                                            {{-- CLICKABLE STATE --}}
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center toggle-trigger {{ $isMyDept ? 'bg-danger text-white' : 'hover-bg' }}" 
                                                 style="cursor: pointer;">
                                                @if($isMyDept)
                                                    <small>BUSY</small>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-bg:hover { background-color: #f8f9fa; }
    .availability-cell { transition: all 0.2s; }
</style>

<script>
document.querySelectorAll('.toggle-trigger').forEach(trigger => {
    trigger.addEventListener('click', function() {
        const cell = this.parentElement;
        const day = cell.dataset.day;
        const slot = cell.dataset.slot;

        fetch("{{ route('department.instructors.availability.toggle', $instructor->user_id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ day_of_week: day, time_slot_id: slot })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'added') {
                this.classList.add('bg-danger', 'text-white');
                this.innerHTML = '<small>BUSY</small>';
            } else if (data.status === 'removed') {
                this.classList.remove('bg-danger', 'text-white');
                this.innerHTML = '';
            } else if (data.status === 'error') {
                alert(data.message);
            }
        })
        .catch(err => console.error('Error:', err));
    });
});
</script>
@endsection