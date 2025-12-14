import { apiFetch } from './api.js';
import { getToken, logout, isAuthenticated } from './auth.js';

// Verificar autenticación
if (!isAuthenticated()) {
  window.location.href = 'login.html';
}

// Logout
document.getElementById('logoutBtn').addEventListener('click', logout);

// Cargar atracciones
async function cargarAtracciones() {
  try {
    const atracciones = await apiFetch('/atracciones');
    const grid = document.getElementById('atraccionesGrid');

    grid.innerHTML = atracciones.map(a => `
      <div class="bg-white p-4 rounded shadow">
        ${a.imagen_url ? `<img src="${a.imagen_url}" class="w-full h-40 object-cover rounded mb-3">` : ''}
        <h3 class="font-bold text-lg mb-2">${a.nombre}</h3>
        <p class="text-sm text-gray-600 mb-2">${a.ubicacion}</p>
        <p class="text-blue-600 font-semibold mb-3">$${a.precio || 'Consultar'}</p>
        <div class="flex gap-2">
          <button class="flex-1 bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700" 
                  onclick="editarAtraccion(${a.id})">
            Editar
          </button>
          <button class="flex-1 bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700"
                  onclick="eliminarAtraccion(${a.id})">
            Eliminar
          </button>
        </div>
      </div>
    `).join('');
  } catch (error) {
    console.error('Error cargando atracciones:', error);
  }
}

// Editar atracción
window.editarAtraccion = (id) => {
  alert(`Editar atracción ${id} - Feature en desarrollo`);
};

// Eliminar atracción
window.eliminarAtraccion = async (id) => {
  if (!confirm('¿Estás seguro de eliminar esta atracción?')) return;

  try {
    await apiFetch(`/atracciones/${id}`, {
      method: 'DELETE'
    });
    alert('Atracción eliminada');
    cargarAtracciones();
  } catch (error) {
    alert('Error al eliminar: ' + error.message);
  }
};

// Cargar en inicio
cargarAtracciones();
