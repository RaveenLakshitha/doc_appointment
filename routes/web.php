<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\AppointmentRequestController;
use App\Http\Controllers\AppointmentPrescriptionController;
use App\Http\Controllers\Counter\InvoiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\PatientBillingController;
use App\Http\Controllers\DoctorPanelPrescriptionController;
use App\Http\Controllers\DoctorPanelScheduleController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\DoctorPanelCalendarController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\LeaveRequestsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MedicineTemplateController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\BillingInvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UnitOfMeasureController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\DropdownController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AppointmentReportController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\PatientVisitReportController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AgeGroupController;
use App\Http\Controllers\EmployeeLeaveEntitlementController;
use App\Http\Controllers\WhatsAppWebhookController;

// Guest / Public routes
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
});


    
    Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
        Route::match(['get', 'post'], 'webhook', [WhatsAppWebhookController::class, 'handle'])
            ->name('webhook');
    });

// All authenticated routes
Route::middleware(['auth'])->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'))->name('home');

    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // ─────────────────────────────────────────────
    // Shared – admin + primary-therapist + doctor + staff
    // ─────────────────────────────────────────────
    Route::middleware('role:admin|primary-therapist|doctor|staff')->group(function () {

        // Appointments
        Route::get('/appointments/datatable',           [AppointmentController::class, 'datatable'])    ->name('appointments.datatable');
        Route::get('/appointments/filters',             [AppointmentController::class, 'filters'])      ->name('appointments.filters');
        Route::get('/appointments/calendar',            [AppointmentController::class, 'calendar'])     ->name('appointments.calendar');
        Route::get('/appointments/calendar-events',     [AppointmentController::class, 'calendarEvents'])->name('appointments.calendar.events');
        Route::get('/appointments/doctors/by-specialization/{specialization_id}', 
                                                        [AppointmentController::class, 'getDoctorsBySpecialization'])
                                                        ->name('appointments.doctors.by_specialization');
        Route::get('/doctors/{doctor}/available-slots', 
        [AppointmentController::class, 'availableSlots'])
        ->name('doctors.available-slots');
        Route::post('/appointments/{appointment}/cancel',   [AppointmentController::class, 'cancel'])   ->name('appointments.cancel');
        Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
        Route::get('/appointments/{appointment}/ticket',    [AppointmentController::class, 'ticket'])   ->name('appointments.ticket');
        Route::get('/appointments/pending',             [AppointmentController::class, 'pending'])      ->name('appointments.pending');
        Route::patch('/appointments/{appointment}/assign',  [AppointmentController::class, 'assignDoctor'])->name('appointments.assign');
        Route::patch('/appointments/{appointment}/approve', [AppointmentController::class, 'approve'])  ->name('appointments.approve');
        Route::patch('/appointments/{appointment}/reject',  [AppointmentController::class, 'reject'])   ->name('appointments.reject');
        Route::get('appointments/{appointment}/treatments/edit',   [AppointmentController::class, 'editTreatments'])  ->name('appointments.treatments.edit');
        Route::patch('appointments/{appointment}/treatments',      [AppointmentController::class, 'updateTreatments'])->name('appointments.treatments.update');
        Route::get('/doctors/{doctor}/treatments', [AppointmentController::class, 'getTreatments'])->name('appointments.treatments');
        Route::get('treatments/{treatment}/price',      [AppointmentController::class, 'getTreatmentPrice'])->name('treatments.price');
        Route::resource('appointments',                 AppointmentController::class);

        Route::middleware(['auth', 'role:doctor'])->group(function () {
            Route::get('/doctor-panel/calendar', [DoctorPanelCalendarController::class, 'index'])
                ->name('doctor-panel.calendar');
            Route::get('/doctor-panel/calendar-events', [DoctorPanelCalendarController::class, 'events'])
                ->name('doctor-panel.calendar.events');

            Route::get('/doctor-panel/prescriptions', [DoctorPanelPrescriptionController::class, 'index'])
                ->name('doctor-panel.prescriptions.index');

            Route::get('/doctor-panel/prescriptions/datatable', [DoctorPanelPrescriptionController::class, 'datatable'])
                ->name('doctor-panel.prescriptions.datatable');

            // Optional: show single prescription (if needed)
            Route::get('/doctor-panel/prescriptions/{prescription}', [DoctorPanelPrescriptionController::class, 'show'])
                ->name('doctor-panel.prescriptions.show');
                
            Route::get('/doctor-panel/schedule-calendar', [DoctorPanelScheduleController::class, 'calendar'])
            ->name('doctor-panel.schedule-calendar');

            Route::get('/doctor-panel/schedule-events', [DoctorPanelScheduleController::class, 'calendarEvents'])
                ->name('doctor-panel.schedule-events');

            Route::get('/doctor-panel/appointment-calendar', [DoctorPanelPrescriptionController::class, 'appointmentCalendar'])
                ->name('doctor-panel.appointment-calendar');

            Route::get('/doctor-panel/queue', [DoctorPanelPrescriptionController::class, 'queue'])
                ->name('doctor-panel.queue');
        });


        // Patients
        Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
        Route::get('/patients/{patient}/select2', [PatientController::class, 'select2Single']);
        Route::get('/patients/datatable',               [PatientController::class, 'datatable'])        ->name('patients.datatable');

        Route::post('/patients/bulk-delete',            [PatientController::class, 'bulkDelete'])       ->name('patients.bulkDelete');
     
        Route::get('/patients/filters',                 [PatientController::class, 'filters'])          ->name('patients.filters');
        Route::get('/patients/export/excel',            [PatientController::class, 'exportExcel'])      ->name('patients.export.excel');
        Route::get('/patients/export/csv',              [PatientController::class, 'exportCsv'])        ->name('patients.export.csv');
        Route::get('/patients/export/pdf',              [PatientController::class, 'exportPdf'])        ->name('patients.export.pdf');
        Route::resource('patients',                     PatientController::class);

        // Doctors & Schedules
        Route::get('/doctors/datatable',                [DoctorController::class, 'datatable'])         ->name('doctors.datatable');
        Route::get('/doctors/filters',                  [DoctorController::class, 'filters'])           ->name('doctors.filters');
        Route::post('/doctors/bulk-delete',             [DoctorController::class, 'bulkDelete'])        ->name('doctors.bulkDelete');
        Route::resource('doctors',                      DoctorController::class);

        Route::get('doctor-schedules/calendar',         [DoctorScheduleController::class, 'calendar'])        ->name('doctor-schedules.calendar');
        Route::get('doctor-schedules/calendar-events',  [DoctorScheduleController::class, 'calendarEvents'])   ->name('doctor-schedules.calendar-events');
        Route::get('doctor-schedules-datatable',        [DoctorScheduleController::class, 'datatable'])       ->name('doctor-schedules.datatable');
        Route::get('doctor-schedules-filters',          [DoctorScheduleController::class, 'filters'])         ->name('doctor-schedules.filters');
        Route::post('doctor-schedules/bulk-delete', [DoctorScheduleController::class, 'bulkDelete'])->name('doctor-schedules.bulkDelete');
        Route::get('/doctor/queue/{doctor}',            [DoctorScheduleController::class, 'currentQueue'])    ->name('doctor.current-queue');
        Route::resource('doctor-schedules',             DoctorScheduleController::class);

        // Queue
        Route::get('/queues/daily',                     [QueueController::class, 'dailyQueueOverview'])      ->name('queues.daily');
        Route::post('/queues/daily/complete/{appointment}', [QueueController::class, 'complete'])            ->name('queues.complete');
        Route::post('/queues/daily/update-queue/{appointment}', [QueueController::class, 'updateQueueNumber'])->name('queues.update-queue');
    });

    // ─────────────────────────────────────────────
    // Admin-only section ─ everything under /admin
    // ─────────────────────────────────────────────
    Route::middleware('role:admin')
     ->group(function () {

    // Users & Roles
    Route::post('users/bulk-delete',                [UserController::class, 'bulkDelete'])           ->name('users.bulkDelete');
    Route::get('users/datatable',                   [UserController::class, 'datatable'])            ->name('users.datatable');
    Route::resource('users',                        UserController::class);

    Route::resource('roles',                        RoleController::class)->except(['show']);

    // Specializations
    Route::post('specializations/bulk-delete',      [SpecializationController::class, 'bulkDelete']) ->name('specializations.bulkDelete');
    Route::get('/specializations/datatable', [SpecializationController::class, 'datatable'])->name('specializations.datatable');

    Route::resource('specializations',              SpecializationController::class)->except(['show']);
    
    Route::post('age-groups/bulk-delete',   [AgeGroupController::class, 'bulkDelete'])->name('age-groups.bulkDelete');
    Route::get('/age-groups/datatable',     [AgeGroupController::class, 'datatable'])->name('age-groups.datatable');

    Route::resource('age-groups', AgeGroupController::class)->except(['show']);

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {

    Route::get('appointments', [AppointmentReportController::class, 'index'])
        ->name('appointments');

    Route::get('appointments/summary', [AppointmentReportController::class, 'summary'])
        ->name('appointments.summary');

    Route::get('appointments/data', [AppointmentReportController::class, 'data'])
        ->name('appointments.data');

    Route::get('inventory', [InventoryReportController::class, 'index'])->name('inventory');
    Route::get('inventory/summary', [InventoryReportController::class, 'summary'])->name('inventory.summary');

    Route::get('financial', [FinancialReportController::class, 'index'])->name('financial');
    Route::get('summary', [FinancialReportController::class, 'summary'])->name('financial.summary');
    });


    // Settings
    Route::get('settings/general',                  [SettingsController::class, 'general'])  ->name('settings.general');
    Route::get('settings',                          [SettingsController::class, 'general'])  ->name('settings.index');
    Route::get('settings/edit',                     [SettingsController::class, 'edit'])     ->name('settings.edit');
    Route::put('settings/general',                  [SettingsController::class, 'update'])   ->name('settings.update');

    // Dropdowns
    Route::get('dropdowns',                         [DropdownController::class, 'index'])    ->name('dropdowns.index');
    Route::post('dropdowns',                        [DropdownController::class, 'store'])    ->name('dropdowns.store');
    Route::put('dropdowns/{option}',                [DropdownController::class, 'update'])->name('dropdowns.update');
    Route::delete('dropdowns/{option}',             [DropdownController::class, 'destroy'])->name('dropdowns.destroy');

    // Categories
    Route::get('categories/datatable', [CategoryController::class, 'datatable'])->name('categories.datatable');
    Route::post('categories/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('categories.bulkDelete');
    Route::get('categories/{category}/details', [CategoryController::class, 'details'])->name('categories.details');
    Route::resource('categories', CategoryController::class);

    // Units of Measure
    Route::get('unit-of-measures/datatable', [UnitOfMeasureController::class, 'datatable'])->name('unit-of-measures.datatable');
    Route::post('unit-of-measures/bulk-delete', [UnitOfMeasureController::class, 'bulkDelete'])->name('unit-of-measures.bulkDelete');
    Route::resource('unit-of-measures', UnitOfMeasureController::class);

    // Suppliers
    Route::get('suppliers/datatable', [SupplierController::class, 'datatable'])->name('suppliers.datatable');
    Route::post('suppliers/bulk-delete', [SupplierController::class, 'bulkDelete'])->name('suppliers.bulkDelete');
    Route::resource('suppliers', SupplierController::class);

    // Departments
    Route::get('departments/datatable',             [DepartmentController::class, 'datatable']) ->name('departments.datatable');
    Route::post('departments/bulk-delete',          [DepartmentController::class, 'bulkDelete'])->name('departments.bulkDelete');
    Route::resource('departments',                  DepartmentController::class)->except(['show']);

     // Services
    Route::get('services/datatable',                [ServiceController::class, 'datatable'])            ->name('services.datatable');
    Route::post('services/bulk-delete',             [ServiceController::class, 'bulkDelete'])           ->name('services.bulkDelete');
    Route::resource('services',                     ServiceController::class);
});

    // ─────────────────────────────────────────────
    // Counter + Admin (billing / POS / invoices)
    // ─────────────────────────────────────────────
    Route::middleware('role:admin|counter')->group(function () {

        Route::get('/pos',                              [BillingInvoiceController::class, 'pos'])           ->name('invoices.pos');
        Route::post('/pos/sale',                        [BillingInvoiceController::class, 'posStore'])       ->name('invoices.pos.store');
        Route::get('/pos/doctor/{doctor}/treatments', [BillingInvoiceController::class, 'getDoctorTreatments'])
            ->name('pos.doctor.treatments');
        Route::get('/invoices',                         [BillingInvoiceController::class, 'index'])         ->name('invoices.index');
        Route::get('/invoices/datatable',               [BillingInvoiceController::class, 'datatable'])      ->name('invoices.datatable');
        Route::get('/invoices/filters',                 [BillingInvoiceController::class, 'filters'])        ->name('invoices.filters');
        Route::get('/invoices/{invoice}',               [BillingInvoiceController::class, 'show'])           ->name('invoices.show');
        Route::get('invoices/{invoice}/print', [BillingInvoiceController::class, 'printHtml'])->name('invoices.print');

        Route::post('/invoices/{invoice}/payments',     [PaymentController::class, 'store'])                 ->name('invoices.payments.store');

        Route::get('/payments',                         [PaymentController::class, 'index'])                 ->name('payments.index');
        Route::get('/payments/datatable',               [PaymentController::class, 'datatable'])             ->name('payments.datatable');

        Route::get('/patients/{patient}/billing/datatable', [PatientBillingController::class, 'data'])       ->name('patients.billing.datatable');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/cash-registers', [CashRegisterController::class, 'index'])->name('cash-registers.index');
        Route::get('/cash-registers/datatable', [CashRegisterController::class, 'datatable'])->name('cash-registers.datatable');
        Route::get('/cash-registers/{cashRegister}', [CashRegisterController::class, 'show'])->name('cash-registers.show');

        Route::post('/cash-registers/open', [CashRegisterController::class, 'open'])->name('cash-registers.open');
        Route::post('/cash-registers/{cashRegister}/close', [CashRegisterController::class, 'close'])->name('cash-registers.close');

        Route::get('/pos/cash-registers/current', [CashRegisterController::class, 'current'])->name('cash-registers.current');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    // ─────────────────────────────────────────────
    // Pharmacy / Medicines (admin + counter + possibly doctor)
    // ─────────────────────────────────────────────
    Route::middleware('role:admin|counter|doctor')->group(function () {
        Route::get('medicines-datatable',               [MedicineController::class, 'datatable'])           ->name('medicines.datatable');
        Route::delete('medicines/bulk-delete',          [MedicineController::class, 'bulkDelete'])          ->name('medicines.bulkDelete');
        Route::resource('medicines',                    MedicineController::class);

        Route::get('prescriptions-datatable',           [PrescriptionController::class, 'datatable'])       ->name('prescriptions.datatable');
        Route::delete('prescriptions/bulk-delete',      [PrescriptionController::class, 'bulkDelete'])      ->name('prescriptions.bulkDelete');
        Route::resource('prescriptions',                PrescriptionController::class);

        Route::get('appointments/{appointment}/prescription/create', [AppointmentPrescriptionController::class, 'create'])
                                                        ->name('appointments.prescription.create');
        Route::post('appointments/{appointment}/prescription',       [AppointmentPrescriptionController::class, 'store'])
                                                        ->name('appointments.prescription.store');
    });

    // ─────────────────────────────────────────────
    // Inventory & Services (admin + primary-therapist)
    // ─────────────────────────────────────────────
    Route::middleware('role:admin|primary-therapist')->group(function () {
        Route::get('inventory/datatable',               [InventoryItemController::class, 'datatable'])      ->name('inventory.datatable');
        Route::get('inventory/filters',                 [InventoryItemController::class, 'filters'])        ->name('inventory.filters');
        Route::post('inventory/bulk-delete',            [InventoryItemController::class, 'bulkDelete'])     ->name('inventory.bulkDelete');
        Route::resource('inventory',                    InventoryItemController::class)
            ->parameter('inventory', 'inventoryitem');

    });

    // Rooms, Employees, Attendance, Leave Requests – admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('rooms-datatable',                   [RoomController::class, 'datatable'])               ->name('rooms.datatable');
        Route::post('rooms/bulk-delete',                [RoomController::class, 'bulkDelete'])              ->name('rooms.bulkDelete');
        Route::resource('rooms',                        RoomController::class);

        // Treatments
         Route::get('treatments/datatable',      [TreatmentController::class, 'datatable'])->name('treatments.datatable');
        Route::post('treatments/bulk-delete', [TreatmentController::class, 'bulkDelete'])->name('treatments.bulkDelete');
        Route::resource('treatments', TreatmentController::class);

        Route::get('employees-datatable',               [EmployeeController::class, 'datatable'])           ->name('employees.datatable');
        Route::get('employees-filters',                 [EmployeeController::class, 'filters'])             ->name('employees.filters');
        Route::post('employees/bulk-delete',            [EmployeeController::class, 'bulkDelete'])          ->name('employees.bulkDelete');
        Route::resource('employees',                    EmployeeController::class);

        // Attendance
        Route::get('attendances-datatable',             [AttendanceController::class, 'datatable'])         ->name('attendances.datatable');
        Route::post('/attendance/self-check-in',  [AttendanceController::class, 'selfCheckIn'])
        ->name('attendance.self-check-in');

        Route::post('/attendance/self-check-out', [AttendanceController::class, 'selfCheckOut'])
            ->name('attendance.self-check-out');
        Route::get('attendances-filters',               [AttendanceController::class, 'filters'])           ->name('attendances.filters');
        Route::post('attendances/bulk-delete',          [AttendanceController::class, 'bulkDelete'])        ->name('attendances.bulkDelete');
        Route::get('/attendances/bulk-mark', [AttendanceController::class, 'bulkMarkForm'])
        ->name('attendances.bulk-mark')
        ->middleware('can:attendance.create');

        Route::post('/attendances/bulk-mark', [AttendanceController::class, 'bulkMarkStore'])
            ->name('attendances.bulk-mark.store')
            ->middleware('can:attendance.create');
        Route::resource('attendances',                  AttendanceController::class);

        // Leave Types (HR/Admin)
        Route::get('leave-types-datatable',             [LeaveTypeController::class, 'datatable'])          ->name('leave-types.datatable');
        Route::resource('leave-types',                  LeaveTypeController::class);

        // Leave Requests
        Route::get('leave-requests-datatable',          [LeaveRequestController::class, 'datatable'])       ->name('leave-requests.datatable');
        Route::post('leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
        Route::post('leave-requests/{leaveRequest}/reject',  [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
        Route::resource('leave-requests',               LeaveRequestController::class);

        // Employee Leave Entitlements
        Route::get('leave-entitlements-datatable',      [EmployeeLeaveEntitlementController::class, 'datatable'])->name('leave-entitlements.datatable');
        Route::post('leave-entitlements/bulk-delete',   [EmployeeLeaveEntitlementController::class, 'bulkDelete'])->name('leave-entitlements.bulkDelete');
        Route::resource('leave-entitlements',           EmployeeLeaveEntitlementController::class);
    });

    // Language switch (available to all logged-in users)
    Route::post('/language', [LanguageController::class, 'switch'])->name('language.switch');

    // Medicine Templates
    Route::prefix('medicine-templates')->as('medicine-templates.')->group(function () {
        Route::get('/datatable',                    [MedicineTemplateController::class, 'datatable'])     ->name('datatable');
        Route::get('/{id}/medications',             [MedicineTemplateController::class, 'getMedications'])->name('medications');
        Route::get('/filters',                      [MedicineTemplateController::class, 'filters'])       ->name('filters');
        Route::delete('/bulk-delete',               [MedicineTemplateController::class, 'bulkDelete'])     ->name('bulkDelete');
        Route::resource('',                         MedicineTemplateController::class)
            ->parameters(['' => 'medicine_template']);
    });

    // Appointment Requests (admin + primary-therapist)
    Route::middleware('role:admin|primary-therapist')->group(function () {
        Route::get('/appointment-requests/datatable',   [AppointmentRequestController::class, 'datatable'])   ->name('appointment_requests.datatable');
        Route::get('/appointment-requests/filters',     [AppointmentRequestController::class, 'filters'])     ->name('appointment_requests.filters');
        Route::post('/appointment-requests/bulk-delete',[AppointmentRequestController::class, 'bulkDelete'])  ->name('appointment_requests.bulkDelete');
        Route::get('/doctors/{doctor}/available-times', [AppointmentRequestController::class, 'availableTimes'])->name('doctors.available_times');
        Route::patch('appointment-requests/{appointmentRequest}/approve', [AppointmentRequestController::class, 'approve'])->name('appointment_requests.approve');
        Route::patch('appointment-requests/{appointmentRequest}/reject',  [AppointmentRequestController::class, 'reject']) ->name('appointment_requests.reject');
        Route::patch('appointment-requests/{appointmentRequest}/cancel',  [AppointmentRequestController::class, 'cancel']) ->name('appointment_requests.cancel');
        Route::resource('appointment_requests',         AppointmentRequestController::class);
    });

    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

});