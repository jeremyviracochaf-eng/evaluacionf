import { getToken, saveUser, getUser } from './auth.js';

const API_URL = 'http://127.0.0.1:8000/api';

export async function apiFetch(endpoint, options = {}) {
  const token = getToken();

  const headers = {
    'Content-Type': 'application/json',
    ...(token && { Authorization: `Bearer ${token}` }),
    ...options.headers
  };

  const response = await fetch(`${API_URL}${endpoint}`, {
    ...options,
    headers
  });

  if (!response.ok) {
    const error = await response.json();
    
    // Mejorar manejo de errores de validación
    if (response.status === 422 && error.errors) {
      // Extraer primer mensaje de error
      const firstError = Object.values(error.errors)[0];
      error.message = Array.isArray(firstError) ? firstError[0] : firstError;
    }
    
    throw error;
  }

  const data = await response.json();
  
  // Si la respuesta contiene usuario, guardarlo
  if (data.user) {
    saveUser(data.user);
  }
  
  return data;
}

// Obtener datos del usuario actual
export async function getCurrentUser() {
  try {
    const user = await apiFetch('/auth/me');
    saveUser(user);
    return user;
  } catch (error) {
    console.error('Error getting current user:', error);
    return null;
  }
}
