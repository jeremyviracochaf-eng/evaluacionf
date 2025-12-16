/**
 * API CLIENT - Módulo centralizado para comunicación con backend
 * 
 * Propósito: Centralizar TODAS las llamadas HTTP a la API
 * Ventajas:
 * - Agregar headers comunes en un solo lugar (Authorization, Content-Type)
 * - Manejar errores de forma consistente
 * - Guardar tokens automáticamente
 * - Fácil de debuggear y mantener
 */

import { getToken, saveUser, getUser } from './auth.js';

// URL base de la API (backend Laravel)
// En producción: cambiar a URL de servidor real (https://ejemplo.com/api)
const API_URL = 'http://127.0.0.1:8000/api';

/**
 * apiFetch() - Función base para todos los requests
 * 
 * Uso:
 * const data = await apiFetch('/atracciones');
 * const user = await apiFetch('/auth/me');
 * const created = await apiFetch('/atracciones', {
 *   method: 'POST',
 *   body: JSON.stringify({nombre: 'Parque'})
 * });
 * 
 * Flujo:
 * 1. Obtener token de localStorage
 * 2. Preparar headers HTTP:
 *    - Content-Type: application/json (indicar que enviamos JSON)
 *    - Authorization: Bearer <token> (si existe token)
 * 3. Hacer fetch a URL completa (API_URL + endpoint)
 * 4. Si error → extraer mensaje y lanzar excepción
 * 5. Si éxito → guardar usuario si viene en response
 * 6. Devolver datos
 * 
 * @param {string} endpoint - Ruta del API (ej: '/atracciones')
 * @param {object} options - Opciones fetch (method, body, headers)
 * @returns {Promise<object>} Datos de respuesta
 */
export async function apiFetch(endpoint, options = {}) {
  // Obtener token guardado en localStorage
  const token = getToken();

  // Preparar headers HTTP
  const headers = {
    'Content-Type': 'application/json',
    // Si existe token, agregar en header Authorization
    // Syntax: Authorization: Bearer <token>
    ...(token && { Authorization: `Bearer ${token}` }),
    // Permitir headers adicionales que pasen en options
    ...options.headers
  };

  // Hacer fetch a backend
  // Spread operator (...) copia opciones adicionales de options
  const response = await fetch(`${API_URL}${endpoint}`, {
    ...options,
    headers
  });

  // Verificar si response fue exitosa (status 200-299)
  // Si no fue exitosa (4xx, 5xx), lanzar error
  if (!response.ok) {
    // Parsear error desde JSON
    const error = await response.json();
    
    // Mejorar manejo de errores de validación (status 422)
    // 422 Unprocessable Entity = validación fallida
    // Ejemplo error: {errors: {email: ["Email ya registrado"]}}
    if (response.status === 422 && error.errors) {
      // Extraer primer mensaje de error para mostrar al usuario
      const firstError = Object.values(error.errors)[0];
      error.message = Array.isArray(firstError) ? firstError[0] : firstError;
    }
    
    // Lanzar error para que catch del usuario lo maneje
    throw error;
  }

  // Parsear respuesta a JSON
  const data = await response.json();
  
  // Si respuesta contiene usuario (ej: login), guardar en localStorage
  // Esto permite mantener sesión activa aunque se cierre navegador
  if (data.user) {
    saveUser(data.user);
  }
  
  // Devolver datos para que use el código que llamó apiFetch
  return data;
}

/**
 * getCurrentUser() - Obtener datos del usuario autenticado
 * 
 * Flujo:
 * 1. Hacer request a GET /auth/me (requiere token válido)
 * 2. Si servidor devuelve usuario → guardar en localStorage
 * 3. Si falla (no autenticado) → devolver null
 * 
 * Uso: Al cargar página principal, verificar quién está logueado
 * 
 * @returns {Promise<object|null>} Usuario actual o null si no autenticado
 */
export async function getCurrentUser() {
  try {
    // Obtener usuario del servidor
    const user = await apiFetch('/auth/me');
    // Guardar en localStorage para futuras cargas
    saveUser(user);
    return user;
  } catch (error) {
    // Si error, probablemente token no es válido
    console.error('Error getting current user:', error);
    return null;
  }
}
