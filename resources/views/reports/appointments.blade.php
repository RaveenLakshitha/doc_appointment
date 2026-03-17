@extends('layouts.app')

@section('title', 'Appointment Reports')

@section('content')

    <div class="px-4 sm:px-6 lg:px-4 pb-4 sm:py-12 pt-20">
        <div class=" mb-6">
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">Appointment Reports</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Analyze appointment data, track trends, and generate
                detailed reports</p>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filters</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Date Range -->
                <div>
                    <label for="date_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Date Range
                    </label>
                    <input type="text" id="date_range" name="date_range"
                        class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        placeholder="Select date range">
                </div>

                <!-- Department Filter -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Department
                    </label>
                    <select id="department_id" name="department_id"
                        class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Doctor Filter -->
                <div>
                    <label for="doctor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Doctor
                    </label>
                    <select id="doctor_id" name="doctor_id"
                        class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">All Doctors</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" data-department="{{ $doctor->department_id }}">
                                {{ $doctor->first_name }} {{ $doctor->middle_name }} {{ $doctor->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="button" id="generate_report"
                    class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors shadow-sm">
                    Generate Report
                </button>
                <button type="button" id="reset_filters"
                    class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Reset
                </button>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loading" class="hidden text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            <p class="text-muted-foreground mt-3">Loading report data...</p>
        </div>

        <!-- Report Content -->
        <div id="report_content" class="hidden space-y-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="border rounded-lg bg-card text-card-foreground shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Total Appointments</h3>
                        <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold" id="total_count">0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-card text-card-foreground shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Completed</h3>
                        <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="completed_count">0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-card text-card-foreground shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Cancelled</h3>
                        <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400" id="cancelled_count">0</div>
                    </div>
                </div>

                <div class="border rounded-lg bg-card text-card-foreground shadow-sm">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">No Show</h3>
                        <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400" id="no_show_count">0</div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Status Distribution Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status Distribution</h3>
                    <div class="h-64">
                        <canvas id="status_chart"></canvas>
                    </div>
                </div>

                <!-- By Department Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Appointments by Department</h3>
                    <div class="h-64">
                        <canvas id="department_chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Appointments</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Patient</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Doctor</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Date & Time</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody id="recent_appointments" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- By Doctor Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Performance by Doctor</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Doctor</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Department</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Total</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Completed</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Cancelled</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    No Show</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody id="by_doctor_table" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- By Department Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Performance by Department</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Department</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Total</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Completed</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Cancelled</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    No Show</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody id="by_department_table" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let statusChart = null;
                let departmentChart = null;

                // Initialize date range picker
                const dateRangePicker = flatpickr('#date_range', {
                    mode: 'range',
                    dateFormat: 'Y-m-d',
                    defaultDate: [
                        new Date(new Date().setMonth(new Date().getMonth() - 3)),
                        new Date()
                    ]
                });

                // Filter doctors by department
                document.getElementById('department_id').addEventListener('change', function () {
                    const deptId = this.value;
                    const doctorSelect = document.getElementById('doctor_id');
                    const options = doctorSelect.querySelectorAll('option');

                    options.forEach(option => {
                        if (option.value === '') {
                            option.style.display = 'block';
                            return;
                        }

                        const optionDept = option.dataset.department;
                        if (!deptId || optionDept === deptId) {
                            option.style.display = 'block';
                        } else {
                            option.style.display = 'none';
                        }
                    });

                    doctorSelect.value = '';
                });

                // Generate Report
                document.getElementById('generate_report').addEventListener('click', generateReport);

                // Reset Filters
                document.getElementById('reset_filters').addEventListener('click', function () {
                    document.getElementById('department_id').value = '';
                    document.getElementById('doctor_id').value = '';
                    dateRangePicker.setDate([
                        new Date(new Date().setMonth(new Date().getMonth() - 3)),
                        new Date()
                    ]);

                    const doctorOptions = document.querySelectorAll('#doctor_id option');
                    doctorOptions.forEach(opt => opt.style.display = 'block');
                });

                function generateReport() {
                    const dateRange = document.getElementById('date_range').value;
                    const departmentId = document.getElementById('department_id').value;
                    const doctorId = document.getElementById('doctor_id').value;

                    // Show loading
                    document.getElementById('loading').classList.remove('hidden');
                    document.getElementById('report_content').classList.add('hidden');

                    // Build query parameters
                    const params = new URLSearchParams();
                    if (dateRange) params.append('date_range', dateRange);
                    if (departmentId) params.append('department_id', departmentId);
                    if (doctorId) params.append('doctor_id', doctorId);

                    fetch(`{{ route('reports.appointments.summary') }}?${params.toString()}`)
                        .then(response => response.json())
                        .then(data => {
                            updateSummaryCards(data.summary);
                            updateStatusChart(data.status_distribution);
                            updateDepartmentChart(data.by_department);
                            updateRecentAppointments(data.recent);
                            updateByDoctorTable(data.by_doctor);
                            updateByDepartmentTable(data.by_department_table);

                            // Hide loading, show content
                            document.getElementById('loading').classList.add('hidden');
                            document.getElementById('report_content').classList.remove('hidden');
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.getElementById('loading').classList.add('hidden');
                            showNotification('Failed to generate report. Please try again.', 'error');
                        });
                }

                function updateSummaryCards(summary) {
                    document.getElementById('total_count').textContent = summary.total;
                    document.getElementById('completed_count').textContent = summary.completed;
                    document.getElementById('cancelled_count').textContent = summary.cancelled;
                    document.getElementById('no_show_count').textContent = summary.noShow;
                }

                function updateStatusChart(statusData) {
                    const ctx = document.getElementById('status_chart').getContext('2d');

                    if (statusChart) {
                        statusChart.destroy();
                    }

                    statusChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Completed', 'Approved', 'Cancelled', 'Pending'],
                            datasets: [{
                                data: [
                                    statusData.completed,
                                    statusData.approved,
                                    statusData.cancelled,
                                    statusData.pending
                                ],
                                backgroundColor: [
                                    'rgb(34, 197, 94)',
                                    'rgb(59, 130, 246)',
                                    'rgb(249, 115, 22)',
                                    'rgb(234, 179, 8)'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }

                function updateDepartmentChart(deptData) {
                    const ctx = document.getElementById('department_chart').getContext('2d');

                    if (departmentChart) {
                        departmentChart.destroy();
                    }

                    departmentChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: Object.keys(deptData),
                            datasets: [{
                                label: 'Appointments',
                                data: Object.values(deptData),
                                backgroundColor: 'rgb(59, 130, 246)'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }

                function updateRecentAppointments(appointments) {
                    const tbody = document.getElementById('recent_appointments');
                    tbody.innerHTML = '';

                    if (appointments.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No appointments found</td></tr>';
                        return;
                    }

                    appointments.forEach(appt => {
                        const row = `
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">${appt.patient}</td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">${appt.doctor}</td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">${appt.date_time}</td>
                                                    <td class="px-6 py-4">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${appt.status_class}">
                                                            ${appt.status}
                                                        </span>
                                                    </td>
                                                </tr>
                                            `;
                        tbody.innerHTML += row;
                    });
                }

                function updateByDoctorTable(doctors) {
                    const tbody = document.getElementById('by_doctor_table');
                    tbody.innerHTML = '';

                    if (doctors.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No data found</td></tr>';
                        return;
                    }

                    doctors.forEach(doc => {
                        const row = `
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">${doc.doctor}</td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">${doc.department}</td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">${doc.total}</td>
                                                    <td class="px-6 py-4 text-sm text-green-600 dark:text-green-400">${doc.completed}</td>
                                                    <td class="px-6 py-4 text-sm text-orange-600 dark:text-orange-400">${doc.cancelled}</td>
                                                    <td class="px-6 py-4 text-sm text-red-600 dark:text-red-400">${doc.no_show}</td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">${doc.completion_rate}%</td>
                                                </tr>
                                            `;
                        tbody.innerHTML += row;
                    });
                }

                function updateByDepartmentTable(departments) {
                    const tbody = document.getElementById('by_department_table');
                    tbody.innerHTML = '';

                    if (departments.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No data found</td></tr>';
                        return;
                    }

                    departments.forEach(dept => {
                        const row = `
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">${dept.department}</td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">${dept.total}</td>
                                                    <td class="px-6 py-4 text-sm text-green-600 dark:text-green-400">${dept.completed}</td>
                                                    <td class="px-6 py-4 text-sm text-orange-600 dark:text-orange-400">${dept.cancelled}</td>
                                                    <td class="px-6 py-4 text-sm text-red-600 dark:text-red-400">${dept.no_show}</td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">${dept.completion_rate}%</td>
                                                </tr>
                                            `;
                        tbody.innerHTML += row;
                    });
                }

                // Auto-generate report on page load with default date range
                generateReport();
            });
        </script>
    @endpush

@endsection