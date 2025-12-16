<?php

namespace App\Http\Controllers;

use App\Models\User; //Modelo User
use Illuminate\Http\Request; //Manejar requests HTTP
use Illuminate\Support\Facades\Hash; //Hashear y verificar contraseñas
use Illuminate\Validation\ValidationException; //Manejar errores de validación 

/**
 * AuthController
 * 
 * Controla toda la autenticación de usuarios:
 * - Registro de nuevos usuarios
 * - Login con email y contraseña
 * - Logout
 * - Obtener usuario actual
 * 
 * Usa Laravel Sanctum para autenticación con tokens
 */
class AuthController extends Controller
{
    /**
     * register() - Registrar nuevo usuario
     * 
     * Flujo:
     * 1. Recibe nombre, email, password desde frontend
     * 2. Valida que:
     *    - nombre: requerido, string, máx 255 caracteres
     *    - email: requerido, formato válido, único en BD
     *    - password: requerido, string, mínimo 8 caracteres, confirmación igual
     * 3. Hashea la contraseña (bcrypt) - nunca guardar plaintext
     * 4. Crea nuevo usuario en BD
     * 5. Genera token opaco con Sanctum para autenticación
     * 6. Devuelve usuario + token al cliente
     * 7. Cliente guarda token en localStorage y lo usa en futuras requests
     * 
     * Errores posibles:
     * - Email ya registrado → ValidationException
     * - Validación fallida → 422 Unprocessable Entity
     */
    public function register(Request $request)
    {
        // Validar datos de entrada
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // Único en tabla users
            'password' => 'required|string|min:8|confirmed', // Confirmación = password_confirmation
        ]);

        // Crear nuevo usuario con datos validados
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']), // Hashear contraseña
        ]);

        // Generar token opaco para autenticación (Sanctum)
        // Este token se usa en requests posteriores con: Authorization: Bearer <token>
        $token = $user->createToken('api_token')->plainTextToken;

        // Devolver usuario creado + token con status 201 (Created)
        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    /**
     * login() - Autenticar usuario existente
     * 
     * Flujo:
     * 1. Recibe email + password desde frontend
     * 2. Valida formato de email
     * 3. Busca usuario por email
     * 4. Si no existe → error específico: "Correo no registrado"
     * 5. Si existe → verifica contraseña con Hash::check()
     * 6. Si password no coincide → error específico: "Contraseña incorrecta"
     * 7. Si todo OK → genera token y devuelve usuario + token
     * 8. Cliente recibe token y lo usa en futuras requests
     * 
     * Errores específicos (Importante para UX):
     * - Email no registrado → Le dice al usuario que se registre
     * - Contraseña incorrecta → Le dice que intente de nuevo
     * 
     * Esto es mejor que decir "Email o contraseña incorrectos" (que revela si email existe)
     */
    public function login(Request $request)
    {
        // Validar entrada: email formato válido, password requerido
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Buscar usuario por email en BD
        // first() devuelve un User o null si no existe
        $user = User::where('email', $data['email'])->first();

        // Si usuario no existe, lanzar error con mensaje específico
        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['Correo no registrado en nuestro sistema.'],
            ]);
        }

        // Comparar contraseña ingresada con hash guardado en BD
        // Hash::check(plaintext, hash) devuelve true/false
        if (! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Contraseña incorrecta.'],
            ]);
        }

        // Si password es correcto, generar token opaco para autenticación (Sanctum)
        $token = $user->createToken('api_token')->plainTextToken;

        // Devolver usuario + token con status 200 (OK)
        return response()->json(['user' => $user, 'token' => $token], 200);
    }

    /**
     * logout() - Cerrar sesión
     * 
     * Flujo:
     * 1. Recibe request autenticado (con token en header)
     * 2. Obtiene token actual del usuario: currentAccessToken()
     * 3. Lo elimina de BD
     * 4. En frontend: borrar token de localStorage
     * 5. Redirect a login
     * 
     * Nota: El cliente también debe borrar localStorage['token']
     */
    public function logout(Request $request)
    {
        // Obtener token actual del usuario y eliminarlo de BD
        // Esto invalida el token para futuras requests
        $request->user()->currentAccessToken()->delete();

        // Devolver mensaje de confirmación
        return response()->json(['message' => 'Sesión cerrada'], 200);
    }

    /**
     * me() - Obtener datos del usuario autenticado
     * 
     * Flujo:
     * 1. Request autenticado (con token válido)
     * 2. Laravel Sanctum valida token automáticamente
     * 3. $request->user() devuelve el usuario del token
     * 4. Devolver datos del usuario en JSON
     * 
     * Uso: Al cargar página, verificar quién está logueado
     * GET /api/auth/me con header: Authorization: Bearer <token>
     */
    public function me(Request $request)
    {
        // user() devuelve el User del token actual
        // Si token no es válido, Laravel retorna 401 Unauthorized automáticamente
        return response()->json($request->user());
    }

}
