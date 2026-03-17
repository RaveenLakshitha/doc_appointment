<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\Therapist\AppointmentController as TherapistAppointmentController;
use App\Http\Controllers\PrimaryTherapist\PatientController as PrimaryPatientController;
use App\Http\Controllers\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\AppointmentRequestController;
use App\Http\Controllers\AppointmentPrescriptionController;
use App\Http\Controllers\Counter\InvoiceController;
use App\Http\Controllers\HR\DashboardController;
use App\Http\Controllers\Patient\BookingController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\PatientBillingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\LeaveRequestsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MedicationTemplateCategoryController;
use App\Http\Controllers\MedicineTemplateController;

// ---------------------- NEW CONTROLLERS ----------------------
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\AmbulanceCallController;
use App\Http\Controllers\AmbulanceController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\BillingInvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UnitOfMeasureController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\DropdownController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\Report\AppointmentReportController;
use App\Http\Controllers\Report\FinancialReportController;
use App\Http\Controllers\Report\PatientVisitReportController;
use App\Http\Controllers\Report\InventoryReportController;
use App\Http\Controllers\SettingsController;
use Illuminate\Http\Request;
use App\Http\Controllers\LanguageController;

use Illuminate\Support\Facades\Route;

// ========================
// GUEST ROUTES
// ========================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
});

// ========================
// AUTHENTICATED ROUTES
// ========================
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ========================
    // ADMIN: FULL ACCESS TO EVERYTHING
    // ========================
    Route::middleware(['auth', 'role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // ---------- USER MANAGEMENT ----------
            Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');
            Route::get('users/datatable', [UserController::class, 'datatable'])->name('users.datatable');
            Route::resource('users', UserController::class);


            Route::resource('roles', RoleController::class)->except(['show']);

            // ---------- SPECIALIZATIONS ----------
            Route::resource('specializations', SpecializationController::class);
            Route::patch('specializations/{specialization}', [SpecializationController::class, 'update'])->name('specializations.update');
            Route::post('specializations/bulk-delete', [SpecializationController::class, 'bulkDelete'])
                ->name('specializations.bulkDelete');

            // ---------- AMBULANCE ----------
            Route::resource('ambulance-calls', AmbulanceCallController::class);
            Route::resource('ambulances', AmbulanceController::class);
            Route::post('ambulances/bulk-delete', [AmbulanceController::class, 'bulkDelete'])
                ->name('ambulances.bulkDelete');




            // ---------- SERVICES OFFERED ----------
            Route::resource('services', ServiceController::class);
            Route::post('services/bulk-delete', [ServiceController::class, 'bulkDelete'])
                ->name('services.bulkDelete');



            // ---------- STAFF ----------
            Route::resource('staff', StaffController::class);
            Route::get('staff/data', [StaffController::class, 'data'])->name('staff.data');
            Route::post('staff/bulk-delete', [StaffController::class, 'bulkDelete'])
                ->name('staff.bulkDelete');

            // ---------- REPORTS ----------
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('appointments', [AppointmentReportController::class, 'index'])
                    ->name('appointments');
                Route::get('financial', [FinancialReportController::class, 'index'])
                    ->name('financial');
                Route::get('patient-visits', [PatientVisitReportController::class, 'index'])
                    ->name('patient-visits');
                Route::get('inventory', [InventoryReportController::class, 'index'])
                    ->name('inventory');
            });



            Route::get('/primary/patients', [PrimaryPatientController::class, 'index'])
                ->name('primary.patients.index');

            Route::get('/counter/invoices', [InvoiceController::class, 'index'])
                ->name('counter.invoices.index');

            Route::get('/hr/dashboard', [DashboardController::class, 'index'])
                ->name('hr.dashboard');

            Route::get('/patient/book', [BookingController::class, 'index'])
                ->name('patient.book');
        });

    // ========================
    // PRIMARY THERAPIST
    // ========================
    Route::middleware('role:primary-therapist')
        ->prefix('primary')
        ->name('primary.')
        ->group(function () {
            Route::get('/patients', [PrimaryPatientController::class, 'index'])
                ->name('patients.index');
        });

    // ========================
    // COUNTER
    // ========================
    Route::middleware('role:counter')
        ->prefix('counter')
        ->name('counter.')
        ->group(function () {
            Route::get('/invoices', [InvoiceController::class, 'index'])
                ->name('invoices.index');
        });

    // ========================
    // HR
    // ========================
    Route::middleware('role:hr')
        ->prefix('hr')
        ->name('hr.')
        ->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])
                ->name('dashboard');
        });

    // ========================
    // PATIENT
    // ========================
    Route::middleware('role:patient')
        ->prefix('patient')
        ->name('patient.')
        ->group(function () {
            Route::get('/book', [BookingController::class, 'index'])
                ->name('book');
        });
});

// ========================
// ROOT REDIRECT
// ========================
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::get('/patients/datatable', [PatientController::class, 'datatable'])
    ->name('patients.datatable')
    ->middleware(['auth', 'role:admin|primary-therapist']);

Route::post('/patients/bulk-delete', [PatientController::class, 'bulkDelete'])
    ->name('patients.bulkDelete')
    ->middleware(['auth', 'role:admin']);

Route::middleware(['auth', 'role:admin|primary-therapist'])->group(function () {

    // Custom routes MUST come BEFORE resource routes
    Route::get('/patients/filters', [PatientController::class, 'filters'])
        ->name('patients.filters');

    Route::get('/patients/export/excel', [PatientController::class, 'exportExcel'])
        ->name('patients.export.excel');
    Route::get('/patients/export/csv', [PatientController::class, 'exportCsv'])
        ->name('patients.export.csv');
    Route::get('/patients/export/pdf', [PatientController::class, 'exportPdf'])
        ->name('patients.export.pdf');

    // This must be last!
    Route::resource('patients', PatientController::class);
});


Route::middleware(['auth', 'role:admin|primary-therapist'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::get('/categories/{category}/details', [CategoryController::class, 'details'])
        ->name('categories.details');
});

Route::post('/categories/bulk-delete', [CategoryController::class, 'bulkDelete'])
    ->name('categories.bulkDelete')
    ->middleware(['auth', 'role:admin']);

Route::middleware(['auth', 'role:admin|primary-therapist'])->group(function () {


    Route::get('inventory/datatable', [InventoryItemController::class, 'datatable'])
        ->name('inventory.datatable');

    Route::get('inventory/filters', [InventoryItemController::class, 'filters'])
        ->name('inventory.filters');

    Route::post('inventory/bulk-delete', [InventoryItemController::class, 'bulkDelete'])
        ->name('inventory.bulkDelete');

    Route::resource('inventory', InventoryItemController::class)
        ->parameter('inventory', 'inventoryitem');

    Route::get('services/datatable', [ServiceController::class, 'datatable'])
        ->name('services.datatable');

    Route::post('services/bulk-delete', [ServiceController::class, 'bulkDelete'])
        ->name('services.bulkDelete');

    Route::resource('services', ServiceController::class);
});


// ---------- PHARMACY ----------
Route::get('medicines-datatable', [MedicineController::class, 'datatable'])
    ->name('medicines.datatable');

Route::delete('medicines/bulk-delete', [MedicineController::class, 'bulkDelete'])
    ->name('medicines.bulkDelete');

Route::resource('medicines', MedicineController::class);

Route::get('/pos', [BillingInvoiceController::class, 'pos'])->name('invoices.pos');
Route::post('/pos/sale', [BillingInvoiceController::class, 'posStore'])->name('invoices.pos.store');
Route::get('/invoices', [BillingInvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoices/datatable', [BillingInvoiceController::class, 'datatable'])->name('invoices.datatable');
Route::get('/invoices/filters', [BillingInvoiceController::class, 'filters'])->name('invoices.filters');
Route::get('/invoices/{invoice}', [BillingInvoiceController::class, 'show'])->name('invoices.index');
Route::get('/invoices/{invoice}/print', [BillingInvoiceController::class, 'print'])->name('invoices.print');

Route::get('/patients/{patient}/billing/datatable', [PatientBillingController::class, 'data'])
    ->name('patients.billing.datatable');

Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::get('/payments/datatable', [PaymentController::class, 'datatable'])->name('payments.datatable');

// For recording payment on an invoice (already used in invoice show page)
Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('invoices.payments.store');

// ---------- INVENTORY ----------
Route::middleware(['auth', 'role:admin|primary-therapist'])->group(function () {
    Route::resource('unit-of-measures', UnitOfMeasureController::class);
    Route::resource('suppliers', SupplierController::class);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/unit-of-measures/bulk-delete', [UnitOfMeasureController::class, 'bulkDelete'])
        ->name('unit-of-measures.bulkDelete');

    Route::post('/suppliers/bulk-delete', [SupplierController::class, 'bulkDelete'])
        ->name('suppliers.bulkDelete');
});

Route::prefix('medicine-templates')
    ->as('medicine-templates.')
    ->group(function () {

        Route::get('/datatable', [MedicineTemplateController::class, 'datatable'])
            ->name('datatable');

        // routes/web.php
        Route::get('/medicine-templates/{id}/medications', [MedicineTemplateController::class, 'getMedications'])->name('medications');

        Route::get('/filters', [MedicineTemplateController::class, 'filters'])
            ->name('filters');

        Route::delete('/bulk-delete', [MedicineTemplateController::class, 'bulkDelete'])
            ->name('bulkDelete');

        // Correct parameter name: medicine_template (not medicationTemplate)
        Route::resource('', MedicineTemplateController::class)
            ->parameters(['' => 'medicine_template']);

        Route::prefix('categories')
            ->as('categories.')
            ->group(function () {
                Route::post('/', [MedicationTemplateCategoryController::class, 'store'])
                    ->name('store');

                Route::put('/{category}', [MedicationTemplateCategoryController::class, 'update'])
                    ->name('update');

                Route::delete('/{category}', [MedicationTemplateCategoryController::class, 'destroy'])
                    ->name('destroy');
            });
    });



// ---------- PRESCRIPTIONS ----------

Route::get('prescriptions-datatable', [PrescriptionController::class, 'datatable'])
    ->name('prescriptions.datatable');

Route::delete('prescriptions/bulk-delete', [PrescriptionController::class, 'bulkDelete'])
    ->name('prescriptions.bulkDelete');

Route::resource('prescriptions', PrescriptionController::class);

// Appointment-specific prescription creation
Route::get('appointments/{appointment}/prescription/create', [AppointmentPrescriptionController::class, 'create'])
    ->name('appointments.prescription.create');

Route::post('appointments/{appointment}/prescription', [AppointmentPrescriptionController::class, 'store'])
    ->name('appointments.prescription.store');


Route::get('/doctors/datatable', [DoctorController::class, 'datatable'])
    ->name('doctors.datatable')
    ->middleware(['auth', 'role:admin|primary-therapist']);

Route::get('/doctors/filters', [DoctorController::class, 'filters'])
    ->name('doctors.filters')
    ->middleware(['auth', 'role:admin|primary-therapist']);

Route::post('/doctors/bulk-delete', [DoctorController::class, 'bulkDelete'])
    ->name('doctors.bulkDelete')
    ->middleware(['auth', 'role:admin']);

// Then the resource route (must be last)
Route::middleware(['auth', 'role:admin|primary-therapist'])
    ->resource('doctors', DoctorController::class);

Route::post('/therapists/bulk-delete', [TherapistController::class, 'bulkDelete'])
    ->name('therapists.bulkDelete')
    ->middleware(['auth', 'role:admin']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/api/patients/search', [PatientSearchController::class, 'search']);
    Route::get('/api/appointments/slots', [AppointmentSlotController::class, 'index']);
});

Route::middleware(['auth', 'role:admin|primary-therapist'])
    ->resource('specializations', SpecializationController::class)->except(['show']);

Route::post('/specializations/bulk-delete', [SpecializationController::class, 'bulkDelete'])
    ->name('specializations.bulkDelete')
    ->middleware(['auth', 'role:admin']);

Route::get('/specializations/datatable', [SpecializationController::class, 'datatable'])
    ->name('specializations.datatable');


// ---------- DEPARTMENTS ----------
Route::resource('departments', DepartmentController::class)->except(['show']);

// DataTables AJAX endpoint
Route::get('/departments/datatable', [DepartmentController::class, 'datatable'])
    ->name('departments.datatable');

// Bulk delete
Route::post('/departments/bulk-delete', [DepartmentController::class, 'bulkDelete'])
    ->name('departments.bulkDelete');
// routes/web.php

Route::middleware(['auth', 'role:admin'])->name('settings.')->group(function () {
    Route::get('/settings/general', [SettingsController::class, 'general'])
        ->name('general');

    Route::get('/settings', [SettingsController::class, 'general'])
        ->name('index');

    Route::get('/settings/edit', [SettingsController::class, 'edit'])
        ->name('edit');

    Route::put('/settings/general', [SettingsController::class, 'update'])
        ->name('update');
});


Route::get('rooms-datatable', [RoomController::class, 'datatable'])->name('rooms.datatable');
Route::get('rooms-filters', [RoomController::class, 'filters'])->name('rooms.filters');
Route::post('rooms/bulk-delete', [RoomController::class, 'bulkDelete'])->name('rooms.bulkDelete');
Route::resource('rooms', RoomController::class);

Route::get('doctor-schedules/calendar', [DoctorScheduleController::class, 'calendar'])
    ->name('doctor-schedules.calendar');

Route::get('doctor-schedules/calendar-events', [DoctorScheduleController::class, 'calendarEvents'])
    ->name('doctor-schedules.calendar-events');
Route::get('doctor-schedules-datatable', [DoctorScheduleController::class, 'datatable'])->name('doctor-schedules.datatable');
Route::get('doctor-schedules-filters', [DoctorScheduleController::class, 'filters'])->name('doctor-schedules.filters');
Route::post('doctor-schedules/bulk-delete', [DoctorScheduleController::class, 'bulkDelete'])->name('doctor-schedules.bulkDelete');

// NEW: Doctor's current serving queue screen
Route::get('/doctor/queue/{doctor}', [DoctorScheduleController::class, 'currentQueue'])
    ->name('doctor.current-queue')
    ->whereNumber('doctor');
Route::resource('doctor-schedules', DoctorScheduleController::class);


Route::get('employees-datatable', [EmployeeController::class, 'datatable'])->name('employees.datatable');
Route::get('employees-filters', [EmployeeController::class, 'filters'])->name('employees.filters');
Route::post('employees/bulk-delete', [EmployeeController::class, 'bulkDelete'])->name('employees.bulkDelete');
Route::resource('employees', EmployeeController::class);

Route::get('attendances-datatable', [AttendanceController::class, 'datatable'])->name('attendances.datatable');
Route::get('attendances-filters', [AttendanceController::class, 'filters'])->name('attendances.filters');
Route::post('attendances/bulk-delete', [AttendanceController::class, 'bulkDelete'])->name('attendances.bulkDelete');
Route::resource('attendances', AttendanceController::class);

Route::get('timesheets-datatable', [TimesheetController::class, 'datatable'])->name('timesheets.datatable');
Route::get('timesheets-filters', [TimesheetController::class, 'filters'])->name('timesheets.filters');
Route::post('timesheets/bulk-delete', [TimesheetController::class, 'bulkDelete'])->name('timesheets.bulkDelete');
Route::resource('timesheets', TimesheetController::class);

Route::get('leave-requests-datatable', [LeaveRequestsController::class, 'datatable'])->name('leave-requests.datatable');
Route::get('leave-requests-filters', [LeaveRequestsController::class, 'filters'])->name('leave-requests.filters');
Route::post('leave-requests/bulk-delete', [LeaveRequestsController::class, 'bulkDelete'])->name('leave-requests.bulkDelete');
Route::resource('leave-requests', LeaveRequestsController::class);

Route::post('/language', [LanguageController::class, 'switch'])
    ->name('language.switch');

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('dropdowns', [DropdownController::class, 'index'])->name('admin.dropdowns.index');
    Route::post('dropdowns', [DropdownController::class, 'store'])->name('admin.dropdowns.store');
    Route::put('dropdowns/{option}', [DropdownController::class, 'update'])->name('admin.dropdowns.update');
    Route::delete('dropdowns/{option}', [DropdownController::class, 'destroy'])->name('admin.dropdowns.destroy');
});

Route::middleware(['auth', 'role:admin|staff|doctor'])->group(function () {

    // DataTables & Filters
    Route::get('/appointments/datatable', [AppointmentController::class, 'datatable'])
        ->name('appointments.datatable');

    Route::get('/appointments/filters', [AppointmentController::class, 'filters'])
        ->name('appointments.filters');

    Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])
        ->name('appointments.calendar');

    Route::get('/appointments/calendar-events', [AppointmentController::class, 'calendarEvents'])
        ->name('appointments.calendar.events');

    Route::get('/appointments/doctors/by-specialization/{specialization_id}', [AppointmentController::class, 'getDoctorsBySpecialization'])->name('appointments.doctors.by_specialization');

    Route::get('/available-slots/{doctor}', [AppointmentController::class, 'availableSlots'])
        ->name('appointments.availableSlots');

    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
        ->name('appointments.cancel');

    Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])
        ->name('appointments.complete');

    Route::get('/appointments/{appointment}/ticket', [AppointmentController::class, 'ticket'])
        ->name('appointments.ticket')
        ->whereNumber('appointment');

    Route::get('/appointments/pending', [AppointmentController::class, 'pending'])
        ->name('appointments.pending');

    Route::patch(
        '/appointments/{appointment}/assign',
        [AppointmentController::class, 'assignDoctor']
    )
        ->name('appointments.assign');

    // Individual actions (approve, reject, assign)
    Route::patch('/appointments/{appointment}/approve', [AppointmentController::class, 'approve'])
        ->name('appointments.approve');
    Route::patch('/appointments/{appointment}/reject', [AppointmentController::class, 'reject'])
        ->name('appointments.reject');

    Route::get(
        '/doctors/{doctor}/available-slots',
        [AppointmentController::class, 'availableSlots']
    )
        ->name('doctors.available-slots');

    Route::get('appointments/{appointment}/treatments/edit', [AppointmentController::class, 'editTreatments'])
        ->name('appointments.treatments.edit');

    Route::patch('appointments/{appointment}/treatments', [AppointmentController::class, 'updateTreatments'])
        ->name('appointments.treatments.update');

    Route::get('treatments/{treatment}/price', [AppointmentController::class, 'getTreatmentPrice'])
        ->name('treatments.price');

    Route::patch('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])
        ->name('appointments.complete');

    Route::get('doctors/{doctor}/specialization', [AppointmentController::class, 'getDoctorSpecialization']);

    Route::resource('appointments', AppointmentController::class);
});


Route::middleware(['auth'])->group(function () {
    Route::resource('treatments', TreatmentController::class)->except(['show']);

    // Extra routes
    Route::patch('treatments/{treatment}/toggle-active', [TreatmentController::class, 'toggleActive'])
        ->name('treatments.toggle-active');

    Route::get('treatments/{treatment}/details', [TreatmentController::class, 'getTreatment'])
        ->name('treatments.details');
});

Route::middleware('auth')->group(function () {
    Route::get('/queues/daily', [QueueController::class, 'dailyQueueOverview'])
        ->name('queues.daily');
});

// For marking completed
Route::post('/queues/daily/complete/{appointment}', [QueueController::class, 'complete'])
    ->name('queues.complete');

// For updating queue number
Route::post('/queues/daily/update-queue/{appointment}', [QueueController::class, 'updateQueueNumber'])
    ->name('queues.update-queue');


Route::get('/appointment-requests/datatable', [AppointmentRequestController::class, 'datatable'])
    ->name('appointment_requests.datatable')
    ->middleware(['auth', 'role:admin|primary-therapist']);

Route::get('/appointment-requests/filters', [AppointmentRequestController::class, 'filters'])
    ->name('appointment_requests.filters')
    ->middleware(['auth', 'role:admin|primary-therapist']);

Route::post('/appointment-requests/bulk-delete', [AppointmentRequestController::class, 'bulkDelete'])
    ->name('appointment_requests.bulkDelete')
    ->middleware(['auth', 'role:admin']);
Route::get('/doctors/{doctor}/available-times', [AppointmentRequestController::class, 'availableTimes'])
    ->name('doctors.available_times');
Route::patch('appointment-requests/{appointmentRequest}/approve', [AppointmentRequestController::class, 'approve'])->name('appointment_requests.approve');
Route::patch('appointment-requests/{appointmentRequest}/reject', [AppointmentRequestController::class, 'reject'])->name('appointment_requests.reject');
Route::patch('appointment-requests/{appointmentRequest}/cancel', [AppointmentRequestController::class, 'cancel'])->name('appointment_requests.cancel');
Route::middleware(['auth', 'role:admin|primary-therapist'])
    ->resource('appointment_requests', AppointmentRequestController::class);

