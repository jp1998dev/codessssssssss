<?php

use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AdmissionIrregularController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProgramCourseMappingController;
use App\Http\Controllers\ReEnrollRegularController;
use App\Http\Controllers\RegistrarSideBarController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentSearchController;
use App\Models\RefBrgy;
use App\Models\RefCityMun;
use App\Models\RefProvince;
use App\Models\RefRegion;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Apply 'auth' middleware to the whole 'registrar' prefix group
Route::prefix('registrar')->middleware('auth')->group(function () {
    // Dashboard
    Route::get('dashboard', [RegistrarSideBarController::class, 'dashboard'])->name('registrar.dashboard');
    Route::get('queueing', function () {
        return view('registrar.queueing');
    })->name('registrar.queueing');

    // Enrollment
    Route::get('enrollment/manage', [RegistrarSideBarController::class, 'quickSearch'])->name('registrar.enrollment.manage');
    Route::get('enrollment/pending', [RegistrarSideBarController::class, 'bulkUpload'])->name('registrar.enrollment.pending');

    // ✅ New Enrollment Pages
    Route::get('enrollment/new', [RegistrarSideBarController::class, 'newEnrollment'])->name('registrar.enrollment.new');
    Route::get('enrollment/transferee', [RegistrarSideBarController::class, 'transfereeEnrollment'])->name('registrar.enrollment.transferee');
    Route::get('enrollment/re-enroll/regular', [RegistrarSideBarController::class, 'reEnrollRegular'])->name('registrar.enrollment.reenroll.regular');
    Route::get('enrollment/re-enroll/irregular', [RegistrarSideBarController::class, 'reEnrollIrregular'])->name('registrar.enrollment.reenroll.irregular');


    // SHS Routes
    Route::get('enrollment/new/shs', [RegistrarSideBarController::class, 'newShsEnrollment'])->name('registrar.enrollment.new.shs');
    Route::get('enrollment/records', [RegistrarSideBarController::class, 'enrollmentRecords'])->name('registrar.enrollment.records');

    // Student Records
    Route::get('records/search', [RegistrarSideBarController::class, 'quickSearch'])->name('registrar.records.search');
    Route::get('records/update', [RegistrarSideBarController::class, 'bulkUpload'])->name('registrar.records.update');

    // Requests
    Route::get('requests/express-processing', [RegistrarSideBarController::class, 'expressProcessing'])->name('registrar.requests.express_processing');
    Route::get('requests/notify-student', [RegistrarSideBarController::class, 'notifyStudent'])->name('registrar.requests.notify_student');

    // Archive
    Route::get('archive/old-student-records', [RegistrarSideBarController::class, 'oldStudentRecords'])->name('registrar.archive.old_student_records');
    Route::get('archive/disposal-log', [RegistrarSideBarController::class, 'disposalLog'])->name('registrar.archive.disposal_log');
});

// Admissions Routes (without the 'registrar' prefix)
Route::get('admissions', [AdmissionController::class, 'index'])->name('admissions.index');
Route::get('admissions/create', [AdmissionController::class, 'create'])->name('admissions.create');
Route::post('admissions', [AdmissionController::class, 'store'])->name('admissions.store');

// Shs routes
Route::post('shs', [StudentController::class, 'store'])->name('shs.store');
Route::post('shs/promote/{id}', [StudentController::class, 'promoteStudent'])->name('shs.promote');
Route::put('/billing/shs/{studentId}/initial-payment', [StudentController::class, 'updateInitialPayment'])
    ->name('billing.shs.updateInitialPayment');


Route::get('/admissions/{student_id}', [AdmissionController::class, 'show'])->name('admissions.show');

Route::get('/admissions/{student_id}/print-cor', [AdmissionController::class, 'printCOR'])->name('admissions.printCOR');
Route::post('/calculate-tuition-fee', [AdmissionController::class, 'calculateTuitionFee'])->name('calculate.tuition.fee');


Route::get('/program-course-mapping/{id}/total-units', [ProgramCourseMappingController::class, 'getTotalUnits']);

Route::get('/get-total-units', [AdmissionController::class, 'getTotalUnits'])->name('get.total.units');
Route::post('/get-mapping-units', [AdmissionController::class, 'getMappingUnits'])->name('getMappingUnits');
Route::put('/billing/{studentId}/initial-payment', [AdmissionController::class, 'updateInitialPayment'])
    ->name('billing.updateInitialPayment');

// Routes for fetching data for cascading dropdowns
Route::get('/provinces/{regionCode}', [LocationController::class, 'getProvinces']);
Route::get('/cities/{provinceCode}', [LocationController::class, 'getCities']);
Route::get('/barangays/{citymunCode}', [LocationController::class, 'getBarangays']);



Route::post('/re-enroll-regular', [ReEnrollRegularController::class, 'submitForm'])
    ->name('re_enroll_regular.store');

Route::get('/search-student', [ReEnrollRegularController::class, 'search'])->name('search.student');
Route::post('/calculate-tuition-fee', [ReEnrollRegularController::class, 'calculateTuitionFee'])
    ->name('calculate.tuition.fee');


Route::get('/admissions/{student_id}/edit', [RegistrarSideBarController::class, 'editStudent'])
    ->name('admissions.edit');
Route::get('/admissions/{student_id}/edit/shs', [RegistrarSideBarController::class, 'editShs'])
    ->name('shs.edit');
Route::put('/admissions/{student_id}', [AdmissionController::class, 'update'])->name('admissions.update');
Route::put('/admissions/{student_id}/shs', [StudentController::class, 'update'])->name('shs.update');

Route::post('/admissions/transferee', [AdmissionController::class, 'storesTransferee'])->name('admissions.store.transferee');
Route::post('/admissions/irregular', [AdmissionIrregularController::class, 'storeIrregular'])->name('admissions.store.irregular');
Route::get('/courses/search', [AdmissionController::class, 'search'])->name('courses.search');



Route::post('/calculate-irregular-tuition', [AdmissionIrregularController::class, 'calculateIrregularTuition'])->name('calculate.irregular.tuition');
Route::post('/get-mapping-courses', [AdmissionController::class, 'getMappingCourses'])
    ->name('getMappingCourses');


    Route::post('/courses/prerequisites', [CourseController::class, 'prerequisites'])->name('courses.prerequisites');
Route::post('/re-enroll/irregular', [ReEnrollRegularController::class, 'reEnrollIrregular'])->name('re_enroll_irregular.store');