import { apiFetch } from './api.js';
import { logout, isAuthenticated, isAdmin, getUser } from './auth.js';

// Verificar autenticación y rol de admin
if (!isAuthenticated() || !isAdmin()) {
  window.location.href = 'login.html';
}

// Mostrar nombre de usuario
const user = getUser();
if (user) {
  document.getElementById('usuarioNombre').textContent = `Hola, ${user.name}`;
}

// Logout
document.getElementById('logoutBtn').addEventListener('click', logout);

let atraccionEnEdicion = null;

// Cargar atracciones
async function cargarAtracciones() {
  try {
    const atracciones = await apiFetch('/atracciones');
    const grid = document.getElementById('atraccionesGrid');

    grid.innerHTML = atracciones.map(a => `
      <div class="bg-white p-4 rounded shadow">
        ${a.imagen_url ? `<img src="${a.imagen_url}" class="w-full h-40 object-cover rounded mb-3">` : '<div class="w-full h-40 bg-gray-300 rounded mb-3 flex items-center justify-center text-gray-600">Sin imagen</div>'}
        <h3 class="font-bold text-lg mb-1">${a.nombre}</h3>
        <p class="text-sm text-gray-600 mb-1">📍 ${a.ubicacion}</p>
        <p class="text-sm text-gray-700 mb-2 line-clamp-2">${a.descripcion}</p>
        <p class="text-blue-600 font-semibold mb-3">$${a.precio || 'Consultar'}</p>
        <div class="flex gap-2">
          <button class="flex-1 bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700" 
                  onclick="abrirEdicion(${a.id}, '${a.nombre}', '${a.categoria}', '${a.ubicacion}', ${a.precio || 'null'}, '${a.descripcion.replace(/'/g, "\\'")}', '${a.imagen_url || ''}')">
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

// Crear nueva atracción
document.getElementById('formAtraccion').addEventListener('submit', async (e) => {
  e.preventDefault();

  const formMsg = document.getElementById('formMsg');
  formMsg.classList.add('hidden');

  try {
    const data = {
      nombre: e.target.nombre.value,
      categoria: e.target.categoria.value,
      ubicacion: e.target.ubicacion.value,
      precio: e.target.precio.value ? parseFloat(e.target.precio.value) : null,
      descripcion: e.target.descripcion.value,
      imagen_url: e.target.imagen_url.value || null
    };

    await apiFetch('/atracciones', {
      method: 'POST',
      body: JSON.stringify(data)
    });

    formMsg.textContent = '✓ Atracción creada exitosamente';
    formMsg.className = 'mt-4 p-3 rounded bg-green-100 text-green-700';
    formMsg.classList.remove('hidden');
    
    e.target.reset();
    
    setTimeout(() => {
      cargarAtracciones();
    }, 1000);
  } catch (error) {
    formMsg.textContent = '✗ Error: ' + (error.message || 'No se pudo crear la atracción');
    formMsg.className = 'mt-4 p-3 rounded bg-red-100 text-red-700';
    formMsg.classList.remove('hidden');
  }
});

// Abrir modal de edición
window.abrirEdicion = (id, nombre, categoria, ubicacion, precio, descripcion, imagenUrl) => {
  atraccionEnEdicion = id;
  document.getElementById('formEditar').nombre.value = nombre;
  document.getElementById('formEditar').categoria.value = categoria;
  document.getElementById('formEditar').ubicacion.value = ubicacion;
  document.getElementById('formEditar').precio.value = precio || '';
  document.getElementById('formEditar').descripcion.value = descripcion;
  document.getElementById('formEditar').imagen_url.value = imagenUrl || '';
  document.getElementById('editMsg').classList.add('hidden');
  document.getElementById('editModal').classList.remove('hidden');
};

// Cerrar modal
window.cerrarModal = () => {
  document.getElementById('editModal').classList.add('hidden');
  atraccionEnEdicion = null;
};

// Guardar edición
document.getElementById('formEditar').addEventListener('submit', async (e) => {
  e.preventDefault();

  const editMsg = document.getElementById('editMsg');
  editMsg.classList.add('hidden');

  try {
    const data = {
      nombre: e.target.nombre.value,
      categoria: e.target.categoria.value,
      ubicacion: e.target.ubicacion.value,
      precio: e.target.precio.value ? parseFloat(e.target.precio.value) : null,
      descripcion: e.target.descripcion.value,
      imagen_url: e.target.imagen_url.value || null
    };

    await apiFetch(`/atracciones/${atraccionEnEdicion}`, {
      method: 'PUT',
      body: JSON.stringify(data)
    });

    editMsg.textContent = '✓ Atracción actualizada exitosamente';
    editMsg.className = 'mt-4 p-3 rounded bg-green-100 text-green-700';
    editMsg.classList.remove('hidden');

    setTimeout(() => {
      cerrarModal();
      cargarAtracciones();
    }, 1000);
  } catch (error) {
    editMsg.textContent = '✗ Error: ' + (error.message || 'No se pudo actualizar la atracción');
    editMsg.className = 'mt-4 p-3 rounded bg-red-100 text-red-700';
    editMsg.classList.remove('hidden');
  }
});

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
