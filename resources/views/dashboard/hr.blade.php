@extends('layouts.app')

@section('title', 'HR Dashboard')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">Welcome, {{ auth()->user()->name }} (HR)</h2>

    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Pending Leave Requests</h5>
                    <p class="card-text display-6 text-primary">12</p>
                    <a href="{{ route('leave-requests.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Today's Attendance</h5>
                    <p class="card-text display-6 text-success">78%</p>
                    <small>Present employees</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Active Employees</h5>
                    <p class="card-text display-6 text-info">45</p>
                    <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-info">Manage Staff</a>
                </div>
            </div>
        </div>
    </div>

    <!-- More sections: recent activity, charts, quick links, etc. -->
</div>
@endsection