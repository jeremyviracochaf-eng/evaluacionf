<?php

namespace App\Http\Controllers;

use App\Models\Atraccion;
use Illuminate\Http\Request;

class AtraccionController extends Controller
{
    // Listar todas las atracciones

    public function index()
    {
        return response()->json(Atraccion::all(), 200);

    }

    // Crear nueva atracción

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria' => 'required|string|max:100',
            'ubicacion' => 'required|string|max:255',
            'precio' => 'nullable|numeric',
            'imagen_url' => 'nullable|string',
        ]);

        $atraccion = Atraccion::create($validated);

        return response()->json($atraccion, 201);

    }

    // Mostrar atracción específica

    public function show(string $id)
    {
        $atraccion = Atraccion::findOrFail($id);
        return response()->json($atraccion, 200);

    }

    // Actualizar atracción

    public function update(Request $request, string $id)
    {
        $atraccion = Atraccion::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'categoria' => 'sometimes|string|max:100',
            'ubicacion' => 'sometimes|string|max:255',
            'precio' => 'nullable|numeric',
            'imagen_url' => 'nullable|string',
        ]);

        $atraccion->update($validated);

        return response()->json($atraccion, 200);

    }

    // Eliminar atracción

    public function destroy(string $id)
    {
        $atraccion = Atraccion::findOrFail($id);
        $atraccion->delete();

        return response()->json(['message' => 'Atracción eliminada'], 200);

    }
}
