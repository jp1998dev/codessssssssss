<?php

use App\Http\Controllers\AccountantDashboardController;
use App\Http\Controllers\AccountantTransactionController;
use App\Http\Controllers\AccountingSideBarController;
use App\Http\Controllers\BankDepositController;
use App\Http\Controllers\OldAccountController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\SOAController;
use Illuminate\Support\Facades\Route;



Route::prefix('accountant')->name('accountant.')->group(function () {
  Route::get('/accountant_db', [AccountingSideBarController::class, 'dashboard'])->name('accountant_db');

  Route::get('/pending-voids', [AccountingSideBarController::class, 'pendingVoids'])->name('pending_voids');

  // all transactions
  Route::get('/transactions', [AccountingSideBarController::class, 'transactions'])->name('transactions'); // college
  Route::get('/transactions/shs', [AccountingSideBarController::class, 'shsTransactions'])->name('transactions.shs'); // shs
  Route::get('/transactions/other', [AccountingSideBarController::class, 'otherTransactions'])->name('transactions.other'); //other
  Route::get('/transactions/uniform', [AccountingSideBarController::class, 'uniformTransactions'])->name('transactions.uniform'); //uniform
  Route::get('/transactions/old', [AccountingSideBarController::class, 'oldTransactions'])->name('transactions.old'); //old account

  // collections
  Route::get('/daily-collection/data', [AccountingSideBarController::class, 'getDailyCollectionByCashier']);
  Route::get('/collection-summary/data', [AccountingSideBarController::class, 'getDailyCollectionTotalByCashier']);
  Route::post('/daily-collection/submit', [AccountingSideBarController::class, 'updateCollection']);
  Route::get('/collection-summary/print/{id}', [AccountingSideBarController::class, 'printCollection']);
  

  Route::get('/daily-collection', function () {
    return view('accountant.daily-collection');
  })->name('daily_collection');

  // bank despits routes
  Route::get('/bank-deposit', [AccountingSideBarController::class, 'getBankDeposits'])->name('bank_deposit');
  Route::put('/deposits/{id}', [BankDepositController::class, 'update']);
  Route::post('/deposits/new', [BankDepositController::class, 'store'])->name('deposits.new');

  // old accounts
  Route::get('/old-accounts', [AccountingSideBarController::class, 'getOldAccounts'])->name('old_accounts');
  Route::put('/old-accounts/{id}/mark-as-paid', [OldAccountController::class, 'markAsPaid'])->name('old_accounts.markAsPaid');
  Route::post('/old-accounts/new', [OldAccountController::class, 'store'])->name('old_accounts.store');

  // student summary
  Route::get('/student-summary', [AccountingSideBarController::class, 'studentSummary'])->name('student_summary');
  Route::get('/student-summary/data/{shoolYearId}', [AccountingSideBarController::class, 'getStudentSummary']);
  Route::get('/shs-summary', [AccountingSideBarController::class, 'shsSummary'])->name('shs_summary');
  Route::get('/shs-summary/data/{shoolYearId}', [AccountingSideBarController::class, 'getShsSummary']);

  Route::get('/collection-summary', function () {
    return view('accountant.collection-summary');
  })->name('collection_summary');


  Route::get('/soa', [AccountingSideBarController::class, 'soa'])->name('soa');
  Route::get('/student-ledger', [AccountingSideBarController::class, 'studentLedger'])->name('student_ledger');
  Route::get('/promisories', [AccountingSideBarController::class, 'promisories'])->name('promisories');
  Route::get('/accountant/dashboard', [AccountantDashboardController::class, 'index'])
    ->name('accountant.dashboard');

  Route::get('/admissions', [SOAController::class, 'index'])->name('admissions.index');
});
// // test routes
// Route::get('/test/daily-collection', [AccountingSideBarController::class, 'getDailyCollectionByCashier']);
// Route::get('/test/collectio-summary/data', [AccountingSideBarController::class, 'getDailyCollectionTotalByCashier']);
// Route::get('/student-summary', [AccountingSideBarController::class, 'studentSummary']);
Route::get('/student-summary/data/{shoolYearId}', [AccountingSideBarController::class, 'getStudentSummary']);


Route::post('/accounting/voids/{payment}/approve', [AccountingSideBarController::class, 'approveVoid'])->name('accounting.voids.approve');
Route::post('/accounting/shs/voids/{payment}/approve', [AccountingSideBarController::class, 'approveShsVoid'])->name('accounting.shs.voids.approve');
Route::post('/accounting/voids/{payment}/reject', [AccountingSideBarController::class, 'rejectVoid'])->name('accounting.voids.reject');
Route::post('/accounting/shs/voids/{payment}/reject', [AccountingSideBarController::class, 'rejectShsVoid'])->name('accounting.shs.voids.reject');
Route::get('/billing/{studentId}', [AccountingSideBarController::class, 'getBillingByStudent']);
Route::get('/billing/{studentId}', [AccountingSideBarController::class, 'getBillingByStudent']);
Route::get('/accountant/ledger', [AccountingSideBarController::class, 'ledger'])->name('accountant.ledger');
