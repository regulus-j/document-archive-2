<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentWorkflowController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PlanSelectionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrialController;
use App\Http\Controllers\UserManualController;
use App\Http\Controllers\UserManagedController;
use App\Http\Controllers\AdminDashboardController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:super-admin'])
    ->name('admin.dashboard');

Route::get('/trial', [TrialController::class, 'start'])->name('trial.start');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//----------------------------------------------------------------------------------------------------------------

Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
Route::get('/plans/select', [PlanSelectionController::class, 'select'])->name('plans.select');
Route::get('/plans/view', [PlanController::class, 'index'])->name('subscriptions.plans'); // Added missing route
Route::get('/register/{plan}', [PlanController::class, 'register'])->name('plans.register');
Route::post('/plans/{plan}/subscribe', [PlanController::class, 'subscribe'])->name('plans.subscribe');

Route::middleware(['auth'])->group(function () {
    Route::post('/plans/store', [PlanSelectionController::class, 'store'])->name('plans.store');

    // Subscription management for company admins
    Route::get('/subscriptions/status', [SubscriptionController::class, 'showStatus'])->name('subscriptions.status');
    Route::post('/subscriptions/cancel-request', [SubscriptionController::class, 'requestCancellation'])->name('subscriptions.request-cancellation');
    Route::post('/subscriptions/upgrade-request', [SubscriptionController::class, 'requestUpgrade'])->name('subscriptions.request-upgrade');

    // Payment routes
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create/{plan}', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/{plan}', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

    // Subscription routes
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::patch('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/subscriptions/{subscription}/activate', [SubscriptionController::class, 'activate'])->name('subscriptions.activate');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('addresses', AddressController::class);
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users-index');
});

//--------------------------------------------------------------------------------------------------------------------

Route::middleware(['auth', 'role:super-admin'])->prefix('admin')->name('admin.')->group(function () {       
    // Subscription Management
    Route::get('/subscriptions', [SubscriptionController::class, 'indexAdmin'])->name('subscriptions.index');
    Route::get('/subscriptions/assign', [SubscriptionController::class, 'assignForm'])->name('subscriptions.assign.form');
    Route::post('/subscriptions/assign', [SubscriptionController::class, 'assign'])->name('subscriptions.assign');
    Route::post('/subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew'])->name('subscriptions.renew');
    
    // User Management
    Route::get('/users/registered', [UserController::class, 'showRegistered'])->name('users.registered');
    
    // Plan Management
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    
    // Other admin routes
    Route::get('/user-manual', [UserManualController::class, 'show'])->name('userManual.manual');
});

Route::middleware('auth')->group(function () {
    Route::resource('roles', RoleController::class);

    Route::prefix('users')->group(function () {
        Route::get('/api/users', [UserController::class, 'getUsersByOffice']);
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/search', [UserController::class, 'search'])->name('users.search');
    });
    
     
    Route::get('/companies/manage/{id}', [UserManagedController::class, 'index'])->name('companies.userManaged');

    Route::put('/companies/update-logo/{id}', [UserManagedController::class, 'updateLogo'])->name('companies.updateLogo');

    Route::put('/companies/update-name/{id}', [UserManagedController::class, 'updateName'])->name('companies.updateName');

    Route::put('/companies/update-theme/{id}', [UserManagedController::class, 'updateTheme'])->name('companies.updateTheme');
       
    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/create', [CompanyController::class, 'create'])->name('companies.create');
        Route::post('/', [CompanyController::class, 'store'])->name('companies.store');
        Route::get('/{company}', [CompanyController::class, 'show'])->name('companies.show');
        Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
        Route::put('/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');

 

        Route::prefix('{company}/addresses')->group(function () {
            Route::get('/', [CompanyController::class, 'addresses'])->name('companies.addresses.index');
            Route::get('/create', [CompanyController::class, 'createAddress'])->name('companies.addresses.create');
            Route::post('/', [CompanyController::class, 'storeAddress'])->name('companies.addresses.store');
            Route::get('/{address}/edit', [CompanyController::class, 'editAddress'])->name('companies.addresses.edit');
            Route::put('/{address}', [CompanyController::class, 'updateAddress'])->name('companies.addresses.update');
            Route::delete('/{address}', [CompanyController::class, 'destroyAddress'])->name('companies.addresses.destroy');
     });
    });

    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/', [DocumentController::class, 'uploadController'])->name('documents.store');

        // Static routes
        Route::get('/archive', [DocumentController::class, 'showArchive'])->name('documents.archive');
        Route::get('/released', [DocumentController::class, 'showReleased'])->name('documents.released');
        Route::get('/pending', [DocumentController::class, 'showPending'])->name('documents.pending');
        Route::get('/complete', [DocumentController::class, 'showComplete'])->name('documents.complete');
        Route::delete('/attachments/{id}', [DocumentController::class, 'deleteAttachment'])->name('attachments.delete');
        Route::get('/forward/{document}', [DocumentController::class, 'forwardDocument'])->name('documents.forward');
        Route::get('/documents/restore/{id}', [DocumentController::class, 'restore'])->name('documents.restore');

        Route::prefix('workflows')->group(function () {
            Route::get('/', [DocumentWorkflowController::class, 'workflowManagement'])
                ->name('documents.workflows');
            
            Route::get('/{workflow}/receive', [DocumentWorkflowController::class, 'receiveWorkflow'])
                ->name('documents.receive');
            
            Route::post('/{workflow}/approve', [DocumentWorkflowController::class, 'approveWorkflow'])
                ->name('documents.approveWorkflow');
            
            Route::post('/{workflow}/reject', [DocumentWorkflowController::class, 'rejectWorkflow'])
                ->name('documents.rejectWorkflow');
            
            Route::get('/{workflow}/review', [DocumentWorkflowController::class, 'reviewDocument'])
                ->name('documents.review');
            
            Route::post('/review/submit/{workflow}', [DocumentWorkflowController::class, 'reviewSubmit'])
                ->name('documents.review.submit');
        });

        Route::post('/{document}/forward', [DocumentWorkflowController::class, 'forwardDocumentSubmit'])
            ->name('documents.forward.submit');

        // Parameterized routes
        Route::get('/{document}/show', [DocumentController::class, 'show'])->name('documents.show');
        Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
        Route::put('/{document}', [DocumentController::class, 'update'])->name('documents.update');
        Route::delete('/{document}/delete', [DocumentController::class, 'destroy'])->name('documents.destroy');
        Route::delete('/{document}/delete-attachment', [DocumentController::class, 'deleteAttachment'])->name('documents.attachments.destroy');
        Route::post('/documents/{document}/cancel', [DocumentController::class, 'cancelWorkflow'])->name('documents.cancel');

        // Update status route
        // Route::get('/{document}/status', [DocumentController::class, 'confirmReleased'])->name('documents.confirmrelease');
        // Route::get('/{document}/{status}', [DocumentController::class, 'changeStatus'])->name('documents.changeStatus');

        // Route::put('/{document}/{status}', [DocumentController::class, 'changeStatus'])->name('documents.updateStatus');

        Route::post('/search/tr', [DocumentController::class, 'searchByTr'])->name('trackingNumber-search');
        Route::post('/search', [DocumentController::class, 'search'])->name('documents.search');
        Route::get('/{id}/download', [DocumentController::class, 'downloadFile'])->name('documents.download');
    });

    Route::prefix('office')->group(function () {
        Route::get('/', [OfficeController::class, 'index'])->name('office.index');
        Route::get('/create', [OfficeController::class, 'create'])->name('office.create');
        Route::post('/', [OfficeController::class, 'store'])->name('office.store');
        Route::get('/{office}', [OfficeController::class, 'show'])->name('office.show');
        Route::get('/{office}/edit', [OfficeController::class, 'edit'])->name('office.edit');
        Route::put('/{office}', [OfficeController::class, 'update'])->name('office.update');
        Route::delete('/{office}', [OfficeController::class, 'destroy'])->name('office.destroy');
    });

    Route::middleware(['auth', 'has.company'])->group(function () {
        Route::resource('offices', OfficeController::class);
    });

    // Office user assignment routes
    Route::get('offices/{office}/assign-users', [OfficeController::class, 'assignUsers'])->name('office.assign.users');
    Route::post('offices/{office}/update-users', [OfficeController::class, 'updateAssignedUsers'])->name('office.users.update');
    Route::post('offices/{office}/add-user', [OfficeController::class, 'addUserToOffice'])->name('office.users.add');
    Route::post('offices/{office}/remove-user', [OfficeController::class, 'removeUserFromOffice'])->name('office.users.remove');

    Route::get('/admin/company-dashboard', [ReportController::class, 'companyDashboard'])
        ->middleware(['auth', 'role:company-admin'])
        ->name('reports.company-dashboard');

    Route::prefix('reports')->middleware(['auth', 'role:company-admin'])->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/', [ReportController::class, 'store'])->name('reports.store');
        Route::get('/analytics', [ReportController::class, 'analytics'])->name('reports.analytics');
        Route::get('/{report}', [ReportController::class, 'show'])->name('reports.show');
        Route::get('/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
        Route::put('/{report}', [ReportController::class, 'update'])->name('reports.update');
        Route::delete('/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
        Route::post('/generate', [ReportController::class, 'generate'])->name('reports.generate');
        Route::get('/office-dashboard', [ReportController::class, 'officeLeadDashboard'])->name('reports.office-dashboard');
        Route::get('/reports/office-user-dashboard', [ReportController::class, 'officeUserDashboard'])->name('reports.office-user-dashboard');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
        Route::get('/plans/create', [PlanController::class, 'create'])->name('plans.create');
        Route::post('/plans', [PlanController::class, 'store'])->name('plans.store'); 
        Route::get('/plans/{plan}', [PlanController::class, 'show'])->name('plans.show');
        Route::get('/plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
        Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
        Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');
    });

    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::patch('/subscriptions/{subscription}', [SubscriptionController::class, 'update']);
    Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel']);
    Route::post('/subscriptions/{subscription}/activate', [SubscriptionController::class, 'activate']);

    Route::get('/pay/{plan}/{billing?}', [PaymentController::class, 'linkCreate'])->name('payment.generate');
    Route::get('/payment/check-status/{reference}', [PaymentController::class, 'checkPaymentStatus'])->name('payment.check-status');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');




    Route::middleware(['auth'])->group(function () {
        Route::resource('payments', PaymentController::class)->only(['index', 'show']);
    });

    Route::middleware(['auth'])->group(function () {
        Route::resource('addresses', AddressController::class);
    });

    Route::get('/user-manual', [UserManualController::class, 'show'])->name('userManual.manual');

    // Add this line for report downloads
    Route::get('/reports/{report}/download/{format?}', [ReportController::class, 'download'])
        ->name('reports.download');
});

// Document Management for Company Admins
Route::middleware(['auth', 'role:company-admin'])->prefix('admin/documents')->name('admin.document-management.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DocumentManagementController::class, 'index'])->name('index');
    Route::get('/list', [App\Http\Controllers\Admin\DocumentManagementController::class, 'documents'])->name('documents');
    Route::get('/show/{id}', [App\Http\Controllers\Admin\DocumentManagementController::class, 'show'])->name('show');
    Route::delete('/delete/{id}', [App\Http\Controllers\Admin\DocumentManagementController::class, 'destroy'])->name('delete');
    Route::post('/toggle-archive/{id}', [App\Http\Controllers\Admin\DocumentManagementController::class, 'toggleArchive'])->name('toggle-archive');
    Route::post('/bulk-delete', [App\Http\Controllers\Admin\DocumentManagementController::class, 'bulkDelete'])->name('bulk-delete');
    Route::get('/deletion-schedule', [App\Http\Controllers\Admin\DocumentManagementController::class, 'showDeletionSchedule'])->name('schedule');
    Route::post('/deletion-schedule', [App\Http\Controllers\Admin\DocumentManagementController::class, 'saveDeletionSchedule'])->name('save-schedule');
    Route::post('/run-deletion-schedule', [App\Http\Controllers\Admin\DocumentManagementController::class, 'runDeletionSchedule'])->name('run-schedule');
});

// // Reports Routes
// Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
//     Route::get('/', [ReportController::class, 'index'])->name('index');
//     Route::get('/create', [ReportController::class, 'create'])->name('create');
//     Route::post('/', [ReportController::class, 'store'])->name('store');
//     Route::get('/analytics', [ReportController::class, 'analytics'])->name('analytics');
//     Route::get('/{report}', [ReportController::class, 'show'])->name('show');
//     Route::get('/{report}/edit', [ReportController::class, 'edit'])->name('edit');
//     Route::put('/{report}', [ReportController::class, 'update'])->name('update');
//     Route::delete('/{report}', [ReportController::class, 'destroy'])->name('destroy');
//     Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
// });

//stmp mail test
// Route::get('/testroute', function() {
//     $name = "Funny Coder";

//     // The email sending is done using the to method on the Mail facade
//     Mail::to('jamalalbadi03@gmail.com')->send(new TestMail($name));
// });

require __DIR__ . '/auth.php';
