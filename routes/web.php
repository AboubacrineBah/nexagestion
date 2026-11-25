<?php

use App\Http\Controllers\AuditEventController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesDocumentController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : app(AuthenticatedSessionController::class)->create();
})->name('home');
Route::middleware('guest')->group(function (): void {
    Route::get('/connexion', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/connexion', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:10,1')->name('login.store');
    Route::get('/mot-de-passe/oublie', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/mot-de-passe/e-mail', [PasswordResetController::class, 'sendLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/mot-de-passe/reinitialiser/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/mot-de-passe/reinitialiser', [PasswordResetController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/deconnexion', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/audit', [AuditEventController::class, 'index'])->name('audit-events.index');
    Route::get('/audit/{event}', [AuditEventController::class, 'show'])->name('audit-events.show');

    Route::middleware('module:crm')->group(function (): void {
        Route::resource('clients', ClientController::class)->parameters(['clients' => 'client'])->only(['index', 'store', 'update', 'destroy']);
        Route::post('clients/{client}/restore', [ClientController::class, 'restore'])->name('clients.restore');
        Route::delete('clients/{client}/force-delete', [ClientController::class, 'forceDelete'])->name('clients.force-delete');
        Route::resource('opportunities', OpportunityController::class)->parameters(['opportunities' => 'opportunity']);
        Route::post('opportunities/{opportunity}/restore', [OpportunityController::class, 'restore'])->name('opportunities.restore');
        Route::delete('opportunities/{opportunity}/force-delete', [OpportunityController::class, 'forceDelete'])->name('opportunities.force-delete');
    });

    Route::middleware('module:sales')->group(function (): void {
        Route::get('/ventes', [SalesDocumentController::class, 'index'])->name('sales.index');
        Route::get('/ventes/nouveau', [SalesDocumentController::class, 'create'])->name('sales.create');
        Route::post('/ventes', [SalesDocumentController::class, 'store'])->name('sales.store');
        Route::get('/ventes/{document}', [SalesDocumentController::class, 'show'])->name('sales.show');
        Route::get('/ventes/{document}/pdf', [SalesDocumentController::class, 'exportPdf'])->name('sales.pdf');
        Route::post('/ventes/{document}/envoyer', [SalesDocumentController::class, 'markSent'])->name('sales.send');
        Route::post('/ventes/{document}/accepter', [SalesDocumentController::class, 'accept'])->name('sales.accept');
        Route::post('/ventes/{document}/convertir-en-facture', [SalesDocumentController::class, 'convertToInvoice'])->name('sales.convert');
        Route::post('/ventes/{document}/paiements', [SalesDocumentController::class, 'storePayment'])->name('sales.payments.store');
        Route::post('/ventes/{document}/restore', [SalesDocumentController::class, 'restore'])->name('sales.restore');
        Route::delete('/ventes/{document}/force-delete', [SalesDocumentController::class, 'forceDelete'])->name('sales.force-delete');
        Route::get('/paiements', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/paiements/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::delete('/paiements/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
        Route::post('/paiements/{payment}/restore', [PaymentController::class, 'restore'])->name('payments.restore');
        Route::delete('/paiements/{payment}/force-delete', [PaymentController::class, 'forceDelete'])->name('payments.force-delete');
    });

    Route::middleware('module:hr')->group(function (): void {
        Route::resource('employees', EmployeeController::class)->parameters(['employees' => 'employee']);
        Route::resource('employees.contracts', ContractController::class)->parameters(['employees' => 'employee', 'contracts' => 'contract'])->scoped();
        Route::resource('employees.leave-requests', LeaveRequestController::class)->parameters(['employees' => 'employee', 'leave-requests' => 'leaveRequest'])->scoped();
        Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/payroll/{payslip}', [PayrollController::class, 'show'])->name('payroll.show');
        Route::get('/payroll/{payslip}/pdf', [PayrollController::class, 'exportPdf'])->name('payroll.pdf');
        Route::post('employees/{employee}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
        Route::delete('employees/{employee}/force-delete', [EmployeeController::class, 'forceDelete'])->name('employees.force-delete');
        Route::post('employees/{employee}/contracts/{contract}/restore', [ContractController::class, 'restore'])->name('employees.contracts.restore');
        Route::delete('employees/{employee}/contracts/{contract}/force-delete', [ContractController::class, 'forceDelete'])->name('employees.contracts.force-delete');
        Route::post('employees/{employee}/leave-requests/{leaveRequest}/restore', [LeaveRequestController::class, 'restore'])->name('employees.leave-requests.restore');
        Route::delete('employees/{employee}/leave-requests/{leaveRequest}/force-delete', [LeaveRequestController::class, 'forceDelete'])->name('employees.leave-requests.force-delete');
        Route::post('payroll/{payslip}/restore', [PayrollController::class, 'restore'])->name('payroll.restore');
        Route::delete('payroll/{payslip}/force-delete', [PayrollController::class, 'forceDelete'])->name('payroll.force-delete');
    });

    Route::middleware('module:sales')->group(function (): void {
        Route::resource('products', ProductController::class)->parameters(['products' => 'product']);
        Route::resource('products.stock-movements', StockMovementController::class)->parameters(['products' => 'product', 'stock-movements' => 'stockMovement'])->only(['index', 'create', 'store']);
        Route::post('products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('products/{product}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');
    });
});
