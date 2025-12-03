<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\AppointmentController;

Route::get('/test', function () {
    return response()->json([
        'status'  => 'OK',
        'message' => 'Medical Appointment API is working',
    ]);
});

// ---------- AUTH ----------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Protected routes (need token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Doctors, Patients, Appointments API
    Route::apiResource('doctors', DoctorController::class);
    Route::apiResource('patients', PatientController::class);
    Route::apiResource('appointments', AppointmentController::class);

    // Extra: appointments per doctor & patient
    Route::get('doctors/{doctor}/appointments', [AppointmentController::class, 'byDoctor']);
    Route::get('patients/{patient}/appointments', [AppointmentController::class, 'byPatient']);

    // Extra: filter appointments by date (optional doctor_id/patient_id)
    Route::get('appointments-by-date', [AppointmentController::class, 'byDate']);
});