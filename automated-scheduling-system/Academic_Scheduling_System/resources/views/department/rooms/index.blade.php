@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold text-primary mb-0">Room Management</h3>
                <p class="text-muted">Register and manage exam rooms for your department</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                <i class="bi bi-plus-lg me-2"></i>Register New Room
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4" style="width: 100px;">Weight</th>
                                    <th>Room Name</th>
                                    <th>Room Type</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rooms as $room)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-light text-primary border">{{ $room->order_weight }}</span>
                                        </td>
                                        <td class="fw-bold text-dark">{{ $room->name }}</td>
                                        <td>
                                            @if($room->type == 'hall')
                                                <span class="badge bg-info text-white"><i class="bi bi-building me-1"></i> Hall</span>
                                            @else
                                                <span class="badge bg-secondary text-white"><i class="bi bi-door-open me-1"></i> Normal Room</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-outline-primary me-1" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editRoomModal{{ $room->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('department.rooms.destroy', $room) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this room?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editRoomModal{{ $room->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Room</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form action="{{ route('department.rooms.update', $room) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-body text-start">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Room Name</label>
                                                                    <input type="text" name="name" class="form-control" value="{{ $room->name }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Room Type</label>
                                                                    <select name="type" class="form-select" required>
                                                                        <option value="normal" {{ $room->type == 'normal' ? 'selected' : '' }}>Normal Room</option>
                                                                        <option value="hall" {{ $room->type == 'hall' ? 'selected' : '' }}>Hall</option>
                                                                    </select>
                                                                    <div class="form-text small">Normal rooms will split the class into two.</div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Sequential Weight</label>
                                                                    <input type="number" name="order_weight" class="form-control" value="{{ $room->order_weight }}" required>
                                                                    <div class="form-text small">Lower weight rooms are used first.</div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Update Room</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="bi bi-door-closed text-muted fs-1 mb-3 d-block"></i>
                                            <p class="text-muted">No rooms registered yet. Click the button above to start.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Register New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('department.rooms.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Room Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Room 101" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Room Type</label>
                        <select name="type" class="form-select" required>
                            <option value="normal">Normal Room</option>
                            <option value="hall">Hall</option>
                        </select>
                        <div class="form-text small text-info">
                            <i class="bi bi-info-circle me-1"></i> Normal rooms will split students and invigilators.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sequential Weight</label>
                        <input type="number" name="order_weight" class="form-control" value="0" required>
                        <div class="form-text small">Determines the order of room allocation.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Register Room</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
