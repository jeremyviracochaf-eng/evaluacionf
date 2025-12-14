<?php

namespace App\Http\Controllers;

use App\Models\Atraccion;
use Illuminate\Http\Request;
use App\Services\FirebaseStorageService;

class AtraccionController extends Controller
{
    // Listar todas las atracciones

    public function index()
    {
        return response()->json(
        Atraccion::orderBy('created_at', 'desc')->get(),
        200
    );

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
        $atraccion = Atraccion::with('reservas')->findOrFail($id);
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

    // Subir imagen a Firebase Storage

    public function uploadImage(Request $request, $id, FirebaseStorageService $storage)
    {
    if (! $request->user()->isAdmin()) {
        return response()->json(['message' => 'No autorizado'], 403);
    }

    $request->validate([
        'imagen' => 'required|image|max:2048',
    ]);

    $atraccion = Atraccion::findOrFail($id);

    $path = "atracciones/{$atraccion->id}_" . time() . "." . $request->imagen->extension();

    $url = $storage->upload($request->imagen, $path);

    $atraccion->update([
        'imagen_url' => $url,
    ]);

    return response()->json([
        'message' => 'Imagen subida correctamente',
        'imagen_url' => $url,
    ]);
    }

}
