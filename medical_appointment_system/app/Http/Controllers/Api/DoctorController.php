<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        return response()->json(Doctor::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:doctors,email',
            'phone'          => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:255',
        ]);

        $doctor = Doctor::create($data);

        return response()->json($doctor, 201);
    }

    public function show(Doctor $doctor)
    {
        return response()->json($doctor);
    }

    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'name'           => 'sometimes|required|string|max:255',
            'email'          => 'sometimes|required|email|unique:doctors,email,' . $doctor->id,
            'phone'          => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:255',
        ]);

        $doctor->update($data);

        return response()->json($doctor);
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();

        return response()->json(null, 204);
    }
}
