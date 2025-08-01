<?php

use App\Http\Controllers\AccountingSideBarController;
use App\Http\Controllers\MiscFeeController;
use App\Http\Controllers\NewUserController;
use App\Http\Controllers\OtherFeeController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\SchoolYearController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VPAdminSideBarController;
//sss

Route::get('/vp-admin-db', [VPAdminSideBarController::class, 'dashboard'])
    ->middleware('auth')
    ->name('vpadmin.vpadmin_db');

Route::get('/blank_page', [VPAdminSideBarController::class, 'blankPage']);
Route::get('/vpadmin_dashboard', [VPAdminSideBarController::class, 'dashboard']);

Route::prefix('fees')->group(function () {
    Route::get('/edit-tuition', [VPAdminSideBarController::class, 'editTuition']);
    Route::get('/misc-fees', [VPAdminSideBarController::class, 'miscFees']);
});

Route::prefix('academic')->group(function () {
    Route::get('/term-configuration', [VPAdminSideBarController::class, 'termConfiguration']);
});

Route::prefix('user-management')->group(function () {
    Route::get('/add-new', [VPAdminSideBarController::class, 'addNewUser']);
    Route::get('/manage', [VPAdminSideBarController::class, 'manageUsers']);
    Route::get('/activate', [VPAdminSideBarController::class, 'activateUsers']);
});
Route::get('fees/other-fees', [VPAdminSideBarController::class, 'otherFees'])->name('fees.other');



Route::get('school-years', [SchoolYearController::class, 'index'])->name('school-years.index');
Route::post('school-years', [SchoolYearController::class, 'store'])->name('school-years.store');
Route::delete('school-years/{id}', [SchoolYearController::class, 'destroy'])->name('school-years.destroy');
Route::patch('school-years/{id}', [SchoolYearController::class, 'update'])->name('school-years.update');  // <-- This one for updating
Route::patch('school-years/{id}/archive', [SchoolYearController::class, 'archive'])->name('school-years.archive');
Route::patch('school-years/{id}/set-active', [SchoolYearController::class, 'setActive'])->name('school-years.set-active');
Route::patch('school-years/{id}/restore', [SchoolYearController::class, 'restore'])->name('school-years.restore');
Route::delete('school-years/{id}/force-delete', [SchoolYearController::class, 'forceDelete'])->name('school-years.forceDelete');


Route::get('/misc-fees/list/{mappingId}', [MiscFeeController::class, 'getList']);
Route::post('/misc-fees/store-bulk', [MiscFeeController::class, 'storeBulk'])->name('misc-fees.store-bulk');

Route::get('/scholarships', [ScholarshipController::class, 'index'])->name('scholarships.index');
Route::post('/scholarships', [ScholarshipController::class, 'store'])->name('scholarships.store');
Route::delete('/scholarships/{id}', [ScholarshipController::class, 'destroy'])->name('scholarships.destroy');

Route::patch('/scholarships/{id}/restore', [ScholarshipController::class, 'restore'])->name('scholarships.restore');
Route::delete('/scholarships/{id}/force-delete', [ScholarshipController::class, 'forceDelete'])->name('scholarships.forceDelete');

Route::patch('/scholarships/{id}/toggle-status', [ScholarshipController::class, 'toggleStatus'])->name('scholarships.toggleStatus');
Route::post('/users', [NewUserController::class, 'store'])->name('users.store');

Route::prefix('fees')->group(function () {
    Route::get('/', [OtherFeeController::class, 'index'])->name('fees.index');
    Route::post('/', [OtherFeeController::class, 'store'])->name('fees.store');
    Route::delete('/{fee}', [OtherFeeController::class, 'destroy'])->name('fees.destroy');
    Route::patch('/{fee}/toggle-status', [OtherFeeController::class, 'toggleStatus'])->name('fees.toggleStatus');
    Route::patch('/{id}/restore', [OtherFeeController::class, 'restore'])->name('fees.restore');
    Route::delete('/{id}/force-delete', [OtherFeeController::class, 'forceDelete'])->name('fees.forceDelete');
    Route::put('/fees/{fee}', [OtherFeeController::class, 'update'])->name('fees.update');
});


Route::prefix('vpadmin')->name('vpadmin.')->group(function () {


    // all transactions
    Route::get('transactions', [VPAdminSideBarController::class, 'transactions'])->name('transactions'); // college
    Route::get('/transactions/shs', [VPAdminSideBarController::class, 'shsTransactions'])->name('transactions.shs'); // shs
    Route::get('/transactions/other', [VPAdminSideBarController::class, 'otherTransactions'])->name('transactions.other'); //other
    Route::get('/transactions/uniform', [VPAdminSideBarController::class, 'uniformTransactions'])->name('transactions.uniform'); //uniform
    Route::get('/transactions/old', [VPAdminSideBarController::class, 'oldTransactions'])->name('transactions.old'); //old account

    // collections
    Route::get('/daily-collection/data', [VPAdminSideBarController::class, 'getDailyCollectionByCashier']);
    Route::get('/collection-summary/data', [VPAdminSideBarController::class, 'getDailyCollectionTotalByCashier']);
    Route::post('/daily-collection/submit', [VPAdminSideBarController::class, 'updateCollection']);
    Route::get('/collection-summary/print/{id}', [VPAdminSideBarController::class, 'printCollection']);

    // collections view routes
    Route::get('/daily-collection', function () {
        return view('vp_admin.collections.daily-collection');
    })->name('daily_collection');

    Route::get('/collection-summary', function () {
        return view('vp_admin.collections.collection-summary');
    })->name('collection_summary');

    // bank despits routes
    Route::get('/bank-deposit', [VPAdminSideBarController::class, 'getBankDeposits'])->name('bank_deposit');

    // old accounts
    Route::get('/old-accounts', [VPAdminSideBarController::class, 'getOldAccounts'])->name('old_accounts');

    // student summary
    Route::get('/student-summary', [VPAdminSideBarController::class, 'studentSummary'])->name('student_summary');
    Route::get('/student-summary/data/{shoolYearId}', [VPAdminSideBarController::class, 'getStudentSummary']);
    Route::get('/shs-summary', [VPAdminSideBarController::class, 'shsSummary'])->name('shs_summary');
    Route::get('/shs-summary/data/{shoolYearId}', [VPAdminSideBarController::class, 'getShsSummary']);
});

