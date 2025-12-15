<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\Therapist\AppointmentController as TherapistAppointmentController;
use App\Http\Controllers\PrimaryTherapist\PatientController as PrimaryPatientController;
use App\Http\Controllers\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Counter\InvoiceController;
use App\Http\Controllers\HR\DashboardController;
use App\Http\Controllers\Patient\BookingController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MedicationTemplateCategoryController;
use App\Http\Controllers\MedicationTemplateController;

// ---------------------- NEW CONTROLLERS ----------------------
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\AmbulanceCallController;
use App\Http\Controllers\AmbulanceController;
use App\Http\Controllers\PharmacyController;
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
use App\Http\Controllers\AttendanceController;
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

            // Doctor Schedule (separate resource – can be nested under doctors if you prefer)
            Route::resource('doctor-schedules', DoctorScheduleController::class);
            Route::post('doctor-schedules/bulk-delete', [DoctorScheduleController::class, 'bulkDelete'])
                ->name('doctor-schedules.bulkDelete');

            // ---------- SPECIALIZATIONS ----------
            Route::resource('specializations', SpecializationController::class);
            Route::post('specializations/bulk-delete', [SpecializationController::class, 'bulkDelete'])
                ->name('specializations.bulkDelete');

            // ---------- PRESCRIPTIONS ----------
            Route::resource('prescriptions', PrescriptionController::class);
            Route::get('prescriptions/data', [PrescriptionController::class, 'data'])
                ->name('prescriptions.data');
            Route::post('prescriptions/bulk-delete', [PrescriptionController::class, 'bulkDelete'])
                ->name('prescriptions.bulkDelete');

            // ---------- AMBULANCE ----------
            Route::resource('ambulance-calls', AmbulanceCallController::class);
            Route::resource('ambulances', AmbulanceController::class);
            Route::post('ambulances/bulk-delete', [AmbulanceController::class, 'bulkDelete'])
                ->name('ambulances.bulkDelete');

            // ---------- PHARMACY ----------
            Route::resource('pharmacy', PharmacyController::class)->parameters([
                'pharmacy' => 'medicine'   // optional – rename URL segment if you like
            ]);

            // ---------- BILLING ----------
            Route::resource('invoices', BillingInvoiceController::class);
            Route::get('invoices/data', [BillingInvoiceController::class, 'data'])
                ->name('invoices.data');
            Route::post('invoices/bulk-delete', [BillingInvoiceController::class, 'bulkDelete'])
                ->name('invoices.bulkDelete');

            Route::resource('payments', PaymentController::class);
            Route::post('payments/bulk-delete', [PaymentController::class, 'bulkDelete'])
                ->name('payments.bulkDelete');

            // ---------- DEPARTMENTS ----------
            Route::resource('departments', DepartmentController::class);
            Route::post('departments/bulk-delete', [DepartmentController::class, 'bulkDelete'])
                ->name('departments.bulkDelete');

            // ---------- SERVICES OFFERED ----------
            Route::resource('services', ServiceController::class);
            Route::post('services/bulk-delete', [ServiceController::class, 'bulkDelete'])
                ->name('services.bulkDelete');


            
            // ---------- STAFF ----------
            Route::resource('staff', StaffController::class);
            Route::get('staff/data', [StaffController::class, 'data'])->name('staff.data');
            Route::post('staff/bulk-delete', [StaffController::class, 'bulkDelete'])
                ->name('staff.bulkDelete');


            // ---------- ATTENDANCE ----------
            Route::resource('attendance', AttendanceController::class);
            Route::post('attendance/bulk-delete', [AttendanceController::class, 'bulkDelete'])
                ->name('attendance.bulkDelete');

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

            
            // ---------- EXISTING ADMIN FEATURES (unchanged) ----------
            Route::get('/therapist/appointments', [TherapistAppointmentController::class, 'index'])
                ->name('therapist.appointments.index');

            Route::get('/primary/patients', [PrimaryPatientController::class, 'index'])
                ->name('primary.patients.index');

            Route::resource('appointments', AdminAppointmentController::class);

            Route::get('/counter/invoices', [InvoiceController::class, 'index'])
                ->name('counter.invoices.index');

            Route::get('/hr/dashboard', [DashboardController::class, 'index'])
                ->name('hr.dashboard');

            Route::get('/patient/book', [BookingController::class, 'index'])
                ->name('patient.book');
        });

    // ========================
    // THERAPIST
    // ========================
    Route::middleware('role:therapist')
        ->prefix('therapist')
        ->name('therapist.')
        ->group(function () {
            Route::get('/appointments', [TherapistAppointmentController::class, 'index'])
                ->name('appointments.index');
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

Route::prefix('medication-templates')
    ->as('medication-templates.')
    ->group(function () {

    Route::get('/datatable', [MedicationTemplateController::class, 'datatable'])
        ->name('datatable');

    Route::get('/filters', [MedicationTemplateController::class, 'filters'])
        ->name('filters');

    Route::delete('/bulk-delete', [MedicationTemplateController::class, 'bulkDelete'])
        ->name('bulkDelete');

    Route::resource('', MedicationTemplateController::class)
        ->parameters(['' => 'medicationTemplate']);

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

// Appointments – admin | primary-therapist
Route::middleware(['auth', 'role:admin|primary-therapist'])->group(function () {

    // Custom routes FIRST
    Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])
         ->name('appointments.calendar');

    Route::get('/appointments/calendar-events', [AppointmentController::class, 'calendarEvents'])
         ->name('appointments.calendar.events');

    // Then the resource (which has the greedy {appointment} route)
    Route::resource('appointments', AppointmentController::class)
         ->parameters(['appointments' => 'appointment']);

    // Bulk delete (admin only)
    Route::post('/appointments/bulk-delete', [AppointmentController::class, 'bulkDelete'])
         ->name('appointments.bulkDelete')
         ->middleware('role:admin');
});

    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/api/patients/search', [PatientSearchController::class, 'search']);
    Route::get('/api/appointments/slots', [AppointmentSlotController::class, 'index']);
    });


    Route::middleware(['auth', 'role:admin|primary-therapist'])
    ->resource('departments', DepartmentController::class);

    Route::post('/departments/bulk-delete', [DepartmentController::class, 'bulkDelete'])
        ->name('departments.bulkDelete')
        ->middleware(['auth', 'role:admin']);

    Route::middleware(['auth', 'role:admin|primary-therapist'])
    ->resource('specializations', SpecializationController::class)->except(['show']);

Route::post('/specializations/bulk-delete', [SpecializationController::class, 'bulkDelete'])
    ->name('specializations.bulkDelete')
    ->middleware(['auth', 'role:admin']);

    Route::get('/specializations/datatable', [SpecializationController::class, 'datatable'])
    ->name('specializations.datatable');
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


Route::post('/language', [LanguageController::class, 'switch'])
    ->name('language.switch');