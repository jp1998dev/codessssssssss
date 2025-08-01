<?php

use App\Http\Controllers\SuperController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('super')->name('super.')->group(function () {
     Route::get('/dashboard', [SuperController::class, 'dashboard'])->name('dashboard');
     Route::post('/send-welcome-email', [SuperController::class, 'sendWelcomeEmail'])->name('send.priv.email');

});