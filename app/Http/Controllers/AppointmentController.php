<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    /**
     * Listar citas.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['person', 'commission'])
            ->orderBy('date')
            ->orderBy('time');

        // Filtrar por fecha.
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        // ?all=true devuelve todas las citas.
        if ($request->boolean('all')) {
            return response()->json([
                'data' => $query->get(),
            ]);
        }

        return response()->json(
            $query->paginate(20)
        );
    }

    /**
     * Crear una cita.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_id' => [
                'required',
                'integer',
                'exists:people,id',
            ],

            'commission_id' => [
                'required',
                'integer',
                'exists:commissions,id',
            ],

            'date' => [
                'required',
                'date',
            ],

            'time' => [
                'required',
                'date_format:H:i',
            ],

            'status' => [
                'nullable',
                'string',
                'in:pendiente,confirmada,cancelada,atendida',
            ],
        ]);

        /*
         * Comprobar que la comisión no tenga
         * otra cita en la misma fecha y hora.
         */
        $exists = Appointment::where(
            'commission_id',
            $validated['commission_id']
        )
            ->whereDate('date', $validated['date'])
            ->where('time', $validated['time'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'time' => [
                    'La comisión seleccionada ya tiene una cita para esa fecha y hora.',
                ],
            ]);
        }

        $appointment = Appointment::create($validated);

        $appointment->load([
            'person',
            'commission',
        ]);

        return response()->json([
            'message' => 'Cita creada correctamente.',
            'data' => $appointment,
        ], 201);
    }

    /**
     * Mostrar una cita.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load([
            'person',
            'commission',
        ]);

        return response()->json([
            'data' => $appointment,
        ]);
    }

    /**
     * Actualizar una cita.
     */
    public function update(
        Request $request,
        Appointment $appointment
    ) {
        $validated = $request->validate([
            'person_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:people,id',
            ],

            'commission_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:commissions,id',
            ],

            'date' => [
                'sometimes',
                'required',
                'date',
            ],

            'time' => [
                'sometimes',
                'required',
                'date_format:H:i',
            ],

            'status' => [
                'sometimes',
                'required',
                'string',
                'in:pendiente,confirmada,cancelada,atendida',
            ],
        ]);

        $commissionId = $validated['commission_id']
            ?? $appointment->commission_id;

        $date = $validated['date']
            ?? $appointment->date->format('Y-m-d');

        $time = $validated['time']
            ?? $appointment->time;

        /*
         * Comprobar que la comisión no tenga
         * otra cita en la misma fecha y hora.
         */
        $exists = Appointment::where(
            'commission_id',
            $commissionId
        )
            ->whereDate('date', $date)
            ->where('time', $time)
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'time' => [
                    'La comisión seleccionada ya tiene una cita para esa fecha y hora.',
                ],
            ]);
        }

        $appointment->update($validated);

        $appointment->load([
            'person',
            'commission',
        ]);

        return response()->json([
            'message' => 'Cita actualizada correctamente.',
            'data' => $appointment,
        ]);
    }

    /**
     * Eliminar una cita.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return response()->json([
            'message' => 'Cita eliminada correctamente.',
        ]);
    }
}