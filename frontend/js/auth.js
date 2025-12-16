/**
 * auth.js - Módulo de autenticación
 * 
 * Propósito: Gestionar tokens y datos de usuario en localStorage
 * localStorage: almacenamiento local del navegador (persiste después de cerrar)
 * 
 * Datos guardados:
 * - token: JWT para requests autenticados
 * - user: objeto con datos del usuario (id, name, email, is_admin)
 */

/**
 * saveToken() - Guardar token JWT en localStorage
 * 
 * Flujo:
 * 1. Servidor devuelve token en login/register
 * 2. Guardar en localStorage['token']
 * 3. Usar en futuras requests en header: Authorization: Bearer <token>
 * 
 * @param {string} token - Token JWT del servidor
 */
export function saveToken(token) {
  // localStorage.setItem(key, value) - guardar string en localStorage
  // Si token ya existe, será sobrescrito
  localStorage.setItem('token', token);
}

/**
 * getToken() - Obtener token guardado
 * 
 * Uso: En cada request a API autenticado
 * const token = getToken(); // "eyJ0eXAiOiJKV1QiLCJhbGciOi..."
 * 
 * @returns {string|null} Token JWT o null si no existe
 */
export function getToken() {
  // localStorage.getItem(key) - obtener valor
  // Devuelve null si key no existe
  return localStorage.getItem('token');
}

/**
 * saveUser() - Guardar datos del usuario en localStorage
 * 
 * Flujo:
 * 1. Servidor devuelve usuario en login/register
 * 2. Guardar como JSON string
 * 3. Usar para mostrar nombre, verificar permisos, etc.
 * 
 * Datos típicos:
 * {
 *   "id": 1,
 *   "name": "Jeremy",
 *   "email": "jeremy@example.com",
 *   "is_admin": true,
 *   "created_at": "2025-12-01..."
 * }
 * 
 * @param {object} user - Objeto usuario del servidor
 */
export function saveUser(user) {
  // JSON.stringify() convierte objeto a string JSON
  // Necesario porque localStorage solo guarda strings
  // Ejemplo: {id:1, name:"Jeremy"} → '{"id":1,"name":"Jeremy"}'
  localStorage.setItem('user', JSON.stringify(user));
}

/**
 * getUser() - Obtener datos del usuario
 * 
 * Uso: Mostrar nombre, verificar si es admin, etc.
 * const user = getUser();
 * console.log(user.name); // "Jeremy"
 * 
 * @returns {object|null} Objeto usuario o null si no existe
 */
export function getUser() {
  // Obtener string de localStorage
  const user = localStorage.getItem('user');
  
  // JSON.parse() convierte string JSON a objeto JavaScript
  // Si no existe, devolver null
  // Ejemplo: '{"id":1}' → {id: 1}
  return user ? JSON.parse(user) : null;
}

/**
 * isAuthenticated() - Verificar si usuario tiene token válido
 * 
 * Uso: Decidir si mostrar página privada o redirigir a login
 * if (!isAuthenticated()) {
 *   window.location.href = 'login.html';
 * }
 * 
 * !!token convierte a boolean:
 * - "token_string" → true
 * - null → false
 * 
 * @returns {boolean} true si existe token, false si no
 */
export function isAuthenticated() {
  // !! es operador de doble negación para convertir a boolean
  // Equivalente a: getToken() !== null
  return !!getToken();
}

/**
 * isAdmin() - Verificar si usuario es administrador
 * 
 * Uso: Mostrar/ocultar panel admin
 * if (isAdmin()) {
 *   mostrarPanelAdmin();
 * }
 * 
 * @returns {boolean} true si usuario es admin, false si no
 */
export function isAdmin() {
  // Obtener usuario guardado
  const user = getUser();
  
  // Devolver true solo si:
  // 1. Usuario existe (user es truthy)
  // 2. Campo role = 'admin' (algunos sistemas usan role, otros is_admin)
  return user && user.role === 'admin';
}

/**
 * logout() - Cerrar sesión
 * 
 * Flujo:
 * 1. Borrar token de localStorage
 * 2. Borrar datos usuario de localStorage
 * 3. Redirigir a página login
 * 
 * También llamar a servidor POST /auth/logout pero es opcional
 * (localStorage limpio = sesión acabada en cliente)
 */
export function logout() {
  // Eliminar token
  localStorage.removeItem('token');
  
  // Eliminar datos usuario
  localStorage.removeItem('user');
  
  // Redirigir a login
  // window.location.href causa reload completo de página
  window.location.href = 'login.html';
}


