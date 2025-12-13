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
    $reservas = Reserva::with(['usuario','atraccion'])->get();
    } else {
    $reservas = Reserva::with(['atraccion'])
        ->where('user_id', $user->id)
        ->get();
    }

    return response()->json($reservas, 200);

    }

    // Crear nueva reserva
    public function store(Request $request)
    {
        $validated = $request->validate([
        'atraccion_id' => 'required|exists:atracciones,id',
        'fecha' => 'required|date',
        'hora' => 'required',
        'comentarios' => 'nullable|string',
    ]);

    // 🔒 VALIDACIÓN DE DOBLE RESERVA
    $existe = Reserva::where('atraccion_id', $validated['atraccion_id'])
        ->where('fecha', $validated['fecha'])
        ->where('hora', $validated['hora'])
        ->where('estado', 'aceptada')
        ->exists();

    if ($existe) {
        return response()->json([
            'message' => 'La atracción ya tiene una reserva aceptada en esa fecha y hora'
        ], 409); // Conflict
    }

    $reserva = Reserva::create([
        'user_id' => $request->user()->id,
        'atraccion_id' => $validated['atraccion_id'],
        'fecha' => $validated['fecha'],
        'hora' => $validated['hora'],
        'estado' => 'pendiente',
        'comentarios' => $validated['comentarios'] ?? null,
    ]);

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

    //Cambiar estado de reserva

    public function cambiarEstado(Request $request, string $id)
   {
    // Seguridad extra
    if (! $request->user()->isAdmin()) {
        return response()->json(['message' => 'No autorizado'], 403);
    }

    $validated = $request->validate([
        'estado' => 'required|in:pendiente,aceptada,rechazada',
    ]);

    $reserva = Reserva::findOrFail($id);
    $reserva->update([
        'estado' => $validated['estado'],
    ]);

    return response()->json([
        'message' => 'Estado de la reserva actualizado',
        'reserva' => $reserva,
    ], 200);
    }

    // Eliminar reserva

    public function destroy(string $id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->delete();

        return response()->json(['message' => 'Reserva eliminada'], 200);

    }
}
