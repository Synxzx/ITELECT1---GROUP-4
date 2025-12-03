<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        // List all appointments with doctor & patient info
        return response()->json(
            Appointment::with(['doctor', 'patient'])->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'status'           => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        // Prevent double booking: same doctor, same time
        $exists = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('appointment_date', $data['appointment_date'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This doctor already has an appointment at that time.',
            ], 422);
        }

        $appointment = Appointment::create($data);

        return response()->json(
            $appointment->load(['doctor', 'patient']),
            201
        );
    }

    public function show(Appointment $appointment)
    {
        return response()->json($appointment->load(['doctor', 'patient']));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'patient_id'       => 'sometimes|required|exists:patients,id',
            'doctor_id'        => 'sometimes|required|exists:doctors,id',
            'appointment_date' => 'sometimes|required|date',
            'status'           => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        // Merge existing values para ma-check ang double booking
        $doctorId = $data['doctor_id']        ?? $appointment->doctor_id;
        $date     = $data['appointment_date'] ?? $appointment->appointment_date;

        $exists = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This doctor already has an appointment at that time.',
            ], 422);
        }

        $appointment->update($data);

        return response()->json($appointment->load(['doctor', 'patient']));
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return response()->json(null, 204);
    }

    // ➜ GET /api/doctors/{doctor}/appointments
    public function byDoctor($doctorId)
    {
        $appointments = Appointment::with(['doctor', 'patient'])
            ->where('doctor_id', $doctorId)
            ->orderBy('appointment_date', 'asc')
            ->get();

        return response()->json($appointments);
    }

    // ➜ GET /api/patients/{patient}/appointments
    public function byPatient($patientId)
    {
        $appointments = Appointment::with(['doctor', 'patient'])
            ->where('patient_id', $patientId)
            ->orderBy('appointment_date', 'asc')
            ->get();

        return response()->json($appointments);
    }

    // ➜ GET /api/appointments-by-date?date=YYYY-MM-DD&doctor_id=&patient_id=
    public function byDate(Request $request)
    {
        $request->validate([
            'date'       => 'required|date',
            'doctor_id'  => 'nullable|integer',
            'patient_id' => 'nullable|integer',
        ]);

        $query = Appointment::with(['doctor', 'patient'])
            ->whereDate('appointment_date', $request->date);

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $appointments = $query->orderBy('appointment_date', 'asc')->get();

        return response()->json($appointments);
    }
}