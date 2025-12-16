<?php

namespace App\Http\Controllers;

use App\Models\Atraccion;
use Illuminate\Http\Request;
use App\Services\FirebaseStorageService;

/**
 * AtraccionController
 * 
 * Controla todas las operaciones CRUD (Create, Read, Update, Delete) para atracciones
 * También maneja filtrado, búsqueda y paginación
 * 
 * Endpoints:
 * - GET    /api/atracciones              → Listar con filtros (público)
 * - POST   /api/atracciones              → Crear (solo admin)
 * - GET    /api/atracciones/{id}         → Detalle (público)
 * - PUT    /api/atracciones/{id}         → Editar (solo admin)
 * - DELETE /api/atracciones/{id}         → Eliminar (solo admin)
 * - POST   /api/atracciones/{id}/upload  → Subir imagen (solo admin)
 */
class AtraccionController extends Controller
{
    /**
     * index() - Listar TODAS las atracciones con filtros y paginación
     * 
     * Flujo:
     * 1. Recibe query parameters opcionales:
     *    - ?provincia=Pichincha    → Filtrar por provincia
     *    - ?search=museo           → Buscar por nombre (LIKE)
     *    - ?page=1                 → Número de página
     *    - ?per_page=20            → Atracciones por página
     * 2. Crea query builder (Eloquent ORM)
     * 3. Aplica filtros con WHERE clauses
     * 4. Ordena por fecha creación (nuevas primero)
     * 5. Pagina resultados (20 por defecto)
     * 6. Devuelve JSON con:
     *    - data[]        → Array de atracciones
     *    - current_page  → Página actual
     *    - last_page     → Total de páginas
     *    - total         → Total de atracciones
     *    - per_page      → Atracciones por página
     * 
     * Ejemplo de request:
     * GET /api/atracciones?provincia=Pichincha&search=museo&page=1&per_page=20
     */
    public function index(Request $request)
    {
        // Crear query builder - empieza sin filtros
        // ORM: Object-Relational Mapping, convierte BD queries a PHP
        $query = Atraccion::query();

        // FILTRO 1: Por provincia
        // Verificar si existe parámetro 'provincia' en URL y no está vacío
        // Se usa en dropdown de filtros
        if ($request->has('provincia') && $request->provincia !== '') {
            // WHERE provincia = 'Pichincha'
            $query->where('provincia', $request->provincia);
        }

        // FILTRO 2: Por categoría
        // Aunque actualmente el UI no lo usa, backend sigue soportándolo
        // Se puede agregar fácilmente a frontend si se necesita
        if ($request->has('categoria') && $request->categoria !== '') {
            // WHERE categoria = 'Parques'
            $query->where('categoria', $request->categoria);
        }

        // FILTRO 3: Búsqueda por nombre
        // Se usa en input de búsqueda de tiempo real
        // LIKE es búsqueda parcial: "museo" encuentra "Museo del Prado"
        if ($request->has('search') && $request->search !== '') {
            // WHERE nombre LIKE '%museo%'
            // El '%' al inicio y final permite búsqueda en cualquier parte de la palabra
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        // PAGINACIÓN
        // Obtener número de resultados por página (por defecto 20)
        $perPage = $request->get('per_page', 20);
        // Obtener número de página actual (por defecto 1)
        $page = $request->get('page', 1);

        // Ejecutar query con paginación
        // orderBy: Ordenar por más reciente primero (DESC = descendente)
        // paginate(): Aplicar paginación y devolver resultados + metadata
        return response()->json(
            $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page),
            200
        );
    }

    /**
     * store() - Crear nueva atracción
     * 
     * Flujo:
     * 1. Recibe datos POST desde frontend (admin)
     * 2. Valida que nombre, descripción, provincia sean requeridos
     * 3. Valida tipos de datos (strings, números)
     * 4. Valida límites de caracteres
     * 5. Crea registro en BD con Atraccion::create()
     * 6. Devuelve atracción creada con status 201 (Created)
     * 
     * Datos esperados:
     * {
     *   "nombre": "Parque Metropolitano",
     *   "descripcion": "Parque grande...",
     *   "categoria": "Parques",
     *   "ubicacion": "Quito, Pichincha",
     *   "provincia": "Pichincha",
     *   "precio": 5.00,
     *   "imagen_url": "https://..."
     * }
     */
    public function store(Request $request)
    {
        // Validar datos de entrada
        // 'required' = campo obligatorio
        // 'string' = debe ser texto
        // 'max:255' = máximo 255 caracteres
        // 'nullable' = campo opcional (puede ser null)
        // 'numeric' = debe ser número
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria' => 'required|string|max:100',
            'ubicacion' => 'required|string|max:255',
            'provincia' => 'required|string|max:100',
            'precio' => 'nullable|numeric',
            'imagen_url' => 'nullable|string',
        ]);

        // Crear nueva atracción en BD
        // Usa $fillable en modelo para saber qué campos se pueden asignar masivamente
        $atraccion = Atraccion::create($validated);

        // Devolver atracción creada con status 201 (Created)
        return response()->json($atraccion, 201);
    }

    /**
     * show() - Mostrar detalle de UNA atracción
     * 
     * Flujo:
     * 1. Recibe ID de atracción en URL: GET /api/atracciones/5
     * 2. Busca atracción por ID
     * 3. Si no existe → 404 Not Found
     * 4. Carga relaciones con with('reservas')
     *    → Trae también todas las reservas de esta atracción
     * 5. Devuelve atracción con reservas en JSON
     * 
     * Ejemplo response:
     * {
     *   "id": 5,
     *   "nombre": "Parque Metropolitano",
     *   "provincia": "Pichincha",
     *   "reservas": [
     *     {"id": 1, "user_id": 2, "cantidad_personas": 4, ...},
     *     {"id": 2, "user_id": 3, "cantidad_personas": 2, ...}
     *   ]
     * }
     */
    public function show(string $id)
    {
        // with() carga relaciones Eloquent (evita N+1 queries)
        // findOrFail() busca por ID o lanza 404 si no existe
        $atraccion = Atraccion::with('reservas')->findOrFail($id);
        return response()->json($atraccion, 200);
    }

    /**
     * update() - Editar una atracción existente
     * 
     * Flujo:
     * 1. Recibe ID y datos PUT desde frontend (admin)
     * 2. Busca atracción por ID (404 si no existe)
     * 3. Valida datos (pueden ser parciales con 'sometimes')
     *    'sometimes' = campo solo se valida si está presente
     * 4. Actualiza solo los campos enviados
     * 5. Devuelve atracción actualizada
     * 
     * Ejemplo: Actualizar solo nombre
     * PUT /api/atracciones/5
     * {"nombre": "Nuevo nombre"}
     */
    public function update(Request $request, string $id)
    {
        // Buscar atracción a actualizar
        $atraccion = Atraccion::findOrFail($id);

        // Validar datos (permitir actualizaciones parciales con 'sometimes')
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'categoria' => 'sometimes|string|max:100',
            'ubicacion' => 'sometimes|string|max:255',
            'provincia' => 'sometimes|string|max:100',
            'precio' => 'nullable|numeric',
            'imagen_url' => 'nullable|string',
        ]);

        // Actualizar atracción con nuevos datos
        $atraccion->update($validated);

        // Devolver atracción actualizada
        return response()->json($atraccion, 200);
    }

    /**
     * destroy() - Eliminar una atracción
     * 
     * Flujo:
     * 1. Recibe ID en URL: DELETE /api/atracciones/5
     * 2. Busca atracción (404 si no existe)
     * 3. Elimina de BD
     * 4. Devuelve mensaje de confirmación
     * 
     * Nota: Se elimina en cascada con las reservas (si están configuradas)
     */
    public function destroy(string $id)
    {
        // Buscar atracción a eliminar
        $atraccion = Atraccion::findOrFail($id);
        // Eliminar de BD
        $atraccion->delete();

        // Devolver mensaje de confirmación
        return response()->json(['message' => 'Atracción eliminada'], 200);
    }

    /**
     * uploadImage() - Subir imagen de atracción a Firebase Storage
     * 
     * Flujo:
     * 1. Verificar que usuario sea admin (is_admin = true)
     * 2. Validar que archivo sea imagen (jpg, png, etc.)
     * 3. Validar tamaño máximo (2MB)
     * 4. Generar nombre único: atracciones/{id}_{timestamp}.{ext}
     * 5. Subir a Firebase Storage
     * 6. Obtener URL pública
     * 7. Guardar URL en campo imagen_url de atracción
     * 8. Devolver URL
     * 
     * Ejemplo:
     * POST /api/atracciones/5/upload
     * multipart/form-data: imagen = <archivo.jpg>
     */
    public function uploadImage(Request $request, $id, FirebaseStorageService $storage)
    {
        // Verificar que usuario sea admin
        // Si no → devolver 403 Forbidden
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Validar que archivo sea imagen y no supere 2MB
        $request->validate([
            'imagen' => 'required|image|max:2048', // max:2048 = 2MB
        ]);

        // Buscar atracción a actualizar
        $atraccion = Atraccion::findOrFail($id);

        // Generar nombre único para archivo
        // Ejemplo: atracciones/5_1702567890.jpg
        // Usar timestamp para evitar conflictos si suben varias imágenes
        $path = "atracciones/{$atraccion->id}_" . time() . "." . $request->imagen->extension();

        // Subir imagen a Firebase Storage
        // Devuelve URL pública para acceder a la imagen
        $url = $storage->upload($request->imagen, $path);

        // Guardar URL en BD
        $atraccion->update([
            'imagen_url' => $url,
        ]);

        // Devolver mensaje + URL de la imagen
        return response()->json([
            'message' => 'Imagen subida correctamente',
            'imagen_url' => $url,
        ]);
    }

}
