<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    // Listar todas las reservas
    public function index(Request $request)
    {
        $user = $request->user();

    if ($user->isAdmin()) {
        // Admin ve todas las reservas con relaciones
        $reservas = Reserva::with(['usuario','atraccion'])->get();
    } else {
        // Usuario normal solo ve sus reservas
        $reservas = Reserva::with(['usuario','atraccion'])
            ->where('user_id', $user->id)
            ->get();
    }

    return response()->json($reservas, 200);


    }

    // Crear nueva reserva
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'atraccion_id' => 'required|exists:atracciones,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'comentarios' => 'nullable|string',
        ]);

        $reserva = Reserva::create($validated);

        return response()->json($reserva, 201);

    }

    // Mostrar reserva específica
    public function show(string $id)
    {
        $reserva = Reserva::with(['usuario','atraccion'])->findOrFail($id);
        return response()->json($reserva, 200);

    }

    // Actualizar reserva

    public function update(Request $request, string $id)
    {
        $reserva = Reserva::findOrFail($id);

        $validated = $request->validate([
            'fecha' => 'sometimes|date',
            'hora' => 'sometimes',
            'estado' => 'sometimes|in:pendiente,aceptada,rechazada',
            'comentarios' => 'nullable|string',
        ]);

        $reserva->update($validated);

        return response()->json($reserva, 200);

    }

    // Eliminar reserva

    public function destroy(string $id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->delete();

        return response()->json(['message' => 'Reserva eliminada'], 200);

    }
}
