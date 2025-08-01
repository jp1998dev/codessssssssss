<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\PresidentSidebarController;
use Illuminate\Support\Facades\Route;

Route::get('/president/dashboard', [PresidentSidebarController::class, 'dashboard'])->name('president.dashboard');
Route::get('/president/accounting', [PresidentSidebarController::class, 'accounting'])->name('president.accounting-dashboard');
Route::get('/president/revenue-trends', [PresidentSidebarController::class, 'revenueTrends'])->name('president.revenue-trends');
Route::get('/president/scholarships-discounts', [PresidentSidebarController::class, 'scholarshipsDiscounts'])->name('president.scholarships-discounts');
Route::get('/president/enrollment-heatmap', [PresidentSidebarController::class, 'enrollmentHeatmap'])->name('president.enrollment-heatmap');
Route::get('/president/financial-alerts', [PresidentSidebarController::class, 'financialAlerts'])->name('president.financial-alerts');
Route::get('/api/revenue-trends', [BillingController::class, 'getRevenueTrends']);
Route::get('/api/revenue-trends/shs', [BillingController::class, 'getRevenueTrendsShs']);
Route::get('/api/balance-due', [BillingController::class, 'getBalanceDue']);
Route::get('/api/balance-due/shs', [BillingController::class, 'getBalanceDueShs']);


Route::prefix('president')->name('president.')->group(function () {


    // all transactions
    Route::get('transactions', [PresidentSidebarController::class, 'transactions'])->name('transactions'); // college
    Route::get('/transactions/shs', [PresidentSidebarController::class, 'shsTransactions'])->name('transactions.shs'); // shs
    Route::get('/transactions/other', [PresidentSidebarController::class, 'otherTransactions'])->name('transactions.other'); //other
    Route::get('/transactions/uniform', [PresidentSidebarController::class, 'uniformTransactions'])->name('transactions.uniform'); //uniform
    Route::get('/transactions/old', [PresidentSidebarController::class, 'oldTransactions'])->name('transactions.old'); //old account

    // // collections
    Route::get('/daily-collection/data', [PresidentSidebarController::class, 'getDailyCollectionByCashier']);
    Route::get('/collection-summary/data', [PresidentSidebarController::class, 'getDailyCollectionTotalByCashier']);
    Route::post('/daily-collection/submit', [PresidentSidebarController::class, 'updateCollection']);
    Route::get('/collection-summary/print/{id}', [PresidentSidebarController::class, 'printCollection']);

    // // collections view routes
    Route::get('/daily-collection', function () {
        return view('president.collections.daily-collection');
    })->name('daily_collection');

    Route::get('/collection-summary', function () {
        return view('president.collections.collection-summary');
    })->name('collection_summary');

    // // bank despits routes
    Route::get('/bank-deposit', [PresidentSidebarController::class, 'getBankDeposits'])->name('bank_deposit');

    // // old accounts
    Route::get('/old-accounts', [PresidentSidebarController::class, 'getOldAccounts'])->name('old_accounts');

    // // student summary
    Route::get('/student-summary', [PresidentSidebarController::class, 'studentSummary'])->name('student_summary');
    Route::get('/student-summary/data/{shoolYearId}', [PresidentSidebarController::class, 'getStudentSummary']);
    Route::get('/shs-summary', [PresidentSidebarController::class, 'shsSummary'])->name('shs_summary');
    Route::get('/shs-summary/data/{shoolYearId}', [PresidentSidebarController::class, 'getShsSummary']);

    // pending online payments
    Route::get('/online-payments', [PresidentSidebarController::class, 'getPendingOnlinePayments'])->name('online_payments');
    Route::post('/pending-payments/clg/{paymentId}/approve', [PresidentSidebarController::class, 'setCollegeApprovedOnlinePayment'])->name('college.approve');
    Route::post('/pending-payments/shs/{paymentId}/approve', [PresidentSidebarController::class, 'setShsApprovedOnlinePayment'])->name('shs.approve');
    Route::post('/pending-payments/clg/{paymentId}/dropped', [PresidentSidebarController::class, 'setCollegeDroppedOnlinePayment'])->name('college.reject');
    Route::post('/pending-payments/shs/{paymentId}/dropped', [PresidentSidebarController::class, 'setShsDroppedOnlinePayment'])->name('shs.reject');
});
