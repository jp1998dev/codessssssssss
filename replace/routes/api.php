<?php

use App\Http\Controllers\AccountingSideBarController;
use App\Http\Controllers\AllAccessController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\QZController;
use App\Http\Controllers\SuperController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\PostMail;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/daily-collections', [DataController::class, 'getDailyCollection']);
Route::get('/semester-collections', [DataController::class, 'getSemesterCollection']);
Route::get('/college-full-collections', [DataController::class, 'getCollegePaymentCollection']);
Route::get('/shs-full-collections', [DataController::class, 'getShsPaymentCollection']);


Route::post('/queueing/save', [QueueController::class, 'saveQueue']);
Route::post('/check-balance', [QueueController::class, 'getStudentBalance']);

Route::post('/qz/sign', [QZController::class, 'sign']);
Route::get('/qz/cert', [QZController::class, 'cert']);
Route::get('/print', [PrintController::class, 'printReceipt']);

Route::prefix('all')->name('all.')->group(function () {
    Route::get('/dashboard', [AllAccessController::class, 'dashboard'])->name('dashboard');
    Route::get('/queueing', [AllAccessController::class, 'queueing'])->name('queueing');
    Route::get('/queueing/latest', [QueueController::class, 'getLatestQueue']);
    Route::post('/queueing/save', [QueueController::class, 'saveQueue']);
});

Route::post('/print-receipt', [PrintController::class, 'reprintReceipt']);
// Route::view('/print-view', 'print');  


// text email route
Route::get('/send-test-email', function () {
    $email = 'erichowenfajeculay20@gmail.com';
    $subject = 'Test Email Subject';
    $message = 'This is a sample plain text email content.';

    Mail::to($email)->send(new PostMail($message, $subject));
    return 'Email sent successfully!';
});
Route::get('/dashboard', [SuperController::class, 'dashboard'])->name('dashboard');