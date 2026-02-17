@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h3 class="mb-4">Bulk Mark Attendance</h3>

    <form method="POST" action="{{ route('attendances.bulk-mark.store') }}">
        @csrf

        <div class="row g-4">
            <!-- Date -->
            <div class="col-md-4">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', $today) }}" required>
                @error('date') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <!-- Status -->
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Present</option>
                    <option value="absent"  {{ old('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="late"    {{ old('status') == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="leave"   {{ old('status') == 'leave' ? 'selected' : '' }}>On Leave</option>
                    <option value="half_day" {{ old('status') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                </select>
                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <!-- Optional Clock In/Out -->
            <div class="col-md-4">
                <label class="form-label">Clock In (optional)</label>
                <input type="time" name="clock_in" class="form-control" value="{{ old('clock_in') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Clock Out (optional)</label>
                <input type="time" name="clock_out" class="form-control" value="{{ old('clock_out') }}">
            </div>
        </div>

        <!-- Notes -->
        <div class="mt-4">
            <label class="form-label">Notes (applies to all)</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
        </div>

        <!-- Employee Selection -->
        <div class="mt-4">
            <label class="form-label">Select Employees</label>
            <div class="card">
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <div class="row g-3">
                        @foreach($employees as $employee)
                            <div class="col-md-6 col-lg-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="employee_ids[]" value="{{ $employee->id }}"
                                           id="emp-{{ $employee->id }}"
                                           {{ in_array($employee->id, old('employee_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="emp-{{ $employee->id }}">
                                        {{ $employee->full_name }} ({{ $employee->employee_code }})
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @error('employee_ids') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-lg">Mark Attendance for Selected</button>
            <a href="{{ route('attendances.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection