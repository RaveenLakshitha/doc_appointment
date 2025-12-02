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
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Report\AppointmentReportController;
use App\Http\Controllers\Report\FinancialReportController;
use App\Http\Controllers\Report\PatientVisitReportController;
use App\Http\Controllers\Report\InventoryReportController;
use App\Http\Controllers\SettingController;
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
            Route::resource('users', UserController::class);
            Route::get('users/data', [UserController::class, 'data'])->name('users.data');
            Route::post('users/deletebyselection', [UserController::class, 'deletebyselection'])
                ->name('users.deletebyselection');

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

            // Roles & Permissions (you may already have this under users, but keeping separate)
            Route::resource('roles-permissions', RolePermissionController::class);

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

            // ---------- SETTINGS ----------
            Route::get('settings/general', [SettingController::class, 'general'])
                ->name('settings.general');
            Route::post('settings/general', [SettingController::class, 'saveGeneral'])
                ->name('settings.general.save');

            Route::get('settings/working-hours', [SettingController::class, 'workingHours'])
                ->name('settings.working-hours');
            Route::post('settings/working-hours', [SettingController::class, 'saveWorkingHours'])
                ->name('settings.working-hours.save');

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

Route::middleware(['auth', 'role:admin|primary-therapist'])
     ->resource('patients', PatientController::class);

     Route::get('/patients/export/excel', [PatientController::class, 'exportExcel'])
     ->name('patients.export.excel');

Route::get('/patients/export/csv', [PatientController::class, 'exportCsv'])
     ->name('patients.export.csv');

Route::get('/patients/export/pdf', [PatientController::class, 'exportPdf'])
     ->name('patients.export.pdf');

    
    Route::middleware(['auth', 'role:admin|primary-therapist'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::get('/categories/{category}/details', [CategoryController::class, 'details'])
         ->name('categories.details');
});

    Route::post('/categories/bulk-delete', [CategoryController::class, 'bulkDelete'])
        ->name('categories.bulkDelete')
        ->middleware(['auth', 'role:admin']);
    
    Route::middleware(['auth', 'role:admin|primary-therapist'])->group(function () {

    // This creates /inventoryitems/{inventoryitem}
    Route::resource('inventoryitems', InventoryItemController::class);

    Route::post('inventoryitems/bulk-delete', [InventoryItemController::class, 'bulkDelete'])
        ->name('inventoryitems.bulk-delete');

    // Use {inventoryitem} to match resource binding
    Route::get('inventoryitems/{inventoryitem}/details', [InventoryItemController::class, 'details'])
        ->name('inventoryitems.details');
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



Route::prefix('medication-templates')->name('medication-templates.')->group(function () {

    Route::get('/', [MedicationTemplateController::class, 'index'])
        ->name('index');

    Route::get('/create', [MedicationTemplateController::class, 'create'])
        ->name('create');

    Route::post('/', [MedicationTemplateController::class, 'store'])
        ->name('store');

    Route::get('/{template}', [MedicationTemplateController::class, 'show'])
        ->name('show');

    Route::get('/{template}/edit', [MedicationTemplateController::class, 'edit'])
        ->name('edit');

    Route::put('/{template}', [MedicationTemplateController::class, 'update'])
        ->name('update');

    Route::delete('/{template}', [MedicationTemplateController::class, 'destroy'])
        ->name('destroy');

    Route::post('/bulk-delete', [MedicationTemplateController::class, 'bulkDelete'])
        ->name('bulkDelete');

    // Nested categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::post('/', [MedicationTemplateCategoryController::class, 'store'])
            ->name('store');

        Route::put('/{category}', [MedicationTemplateCategoryController::class, 'update'])
            ->name('update');

        Route::delete('/{category}', [MedicationTemplateCategoryController::class, 'destroy'])
            ->name('destroy');
    });
});

Route::middleware(['auth', 'role:admin|primary-therapist'])
->resource('doctors', DoctorController::class);

Route::post('/doctors/bulk-delete', [DoctorController::class, 'bulkDelete'])
    ->name('doctors.bulkDelete')
    ->middleware(['auth', 'role:admin']);

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
    ->resource('specializations', SpecializationController::class);

Route::post('/specializations/bulk-delete', [SpecializationController::class, 'bulkDelete'])
    ->name('specializations.bulkDelete')
    ->middleware(['auth', 'role:admin']);
// routes/web.php

Route::post('/language', [LanguageController::class, 'switch'])
    ->name('language.switch');