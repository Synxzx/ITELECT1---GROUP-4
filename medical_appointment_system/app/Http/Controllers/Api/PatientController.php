<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        return response()->json(Patient::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|unique:patients,email',
            'phone'         => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|string|max:20',
            'address'       => 'nullable|string',
        ]);

        $patient = Patient::create($data);

        return response()->json($patient, 201);
    }

    public function show(Patient $patient)
    {
        return response()->json($patient);
    }

    public function update(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'name'          => 'sometimes|required|string|max:255',
            'email'         => 'nullable|email|unique:patients,email,' . $patient->id,
            'phone'         => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|string|max:20',
            'address'       => 'nullable|string',
        ]);

        $patient->update($data);

        return response()->json($patient);
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return response()->json(null, 204);
    }
}