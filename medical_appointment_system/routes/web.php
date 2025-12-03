<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WebAuthController;

// ---------- AUTH PAGES ----------
Route::get('/login',    [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login',   [WebAuthController::class, 'login'])->name('login.perform');

Route::get('/register', [WebAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register',[WebAuthController::class, 'register'])->name('register.perform');

Route::post('/logout',  [WebAuthController::class, 'logout'])->name('logout');

// ---------- PROTECTED DASHBOARD ----------
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // use "web." route names para hindi mag-conflict sa API
    Route::post('/doctors',      [DashboardController::class, 'storeDoctor'])->name('web.doctors.store');
    Route::post('/patients',     [DashboardController::class, 'storePatient'])->name('web.patients.store');
    Route::post('/appointments', [DashboardController::class, 'storeAppointment'])->name('web.appointments.store');
});