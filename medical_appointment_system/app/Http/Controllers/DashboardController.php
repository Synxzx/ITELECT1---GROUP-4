<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function index()
    {
        $doctors     = Doctor::orderBy('name')->get();
        $patients    = Patient::orderBy('name')->get();
        $appointments = Appointment::with(['doctor', 'patient'])
            ->orderBy('appointment_date', 'asc')
            ->get();

        return view('dashboard', compact('doctors', 'patients', 'appointments'));
    }

    public function storeDoctor(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:doctors,email',
            'phone'          => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:255',
        ]);

        Doctor::create($data);

        return redirect()->route('dashboard')->with('success', 'Doctor created successfully.');
    }

    public function storePatient(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|unique:patients,email',
            'phone'         => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|string|max:20',
            'address'       => 'nullable|string',
        ]);

        Patient::create($data);

        return redirect()->route('dashboard')->with('success', 'Patient created successfully.');
    }

    public function storeAppointment(Request $request)
    {
        $data = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'status'           => 'nullable|string|in:scheduled,completed,cancelled',
            'notes'            => 'nullable|string',
        ]);

        if (empty($data['status'])) {
            $data['status'] = 'scheduled';
        }

        Appointment::create($data);

        return redirect()->route('dashboard')->with('success', 'Appointment created successfully.');
    }
}
