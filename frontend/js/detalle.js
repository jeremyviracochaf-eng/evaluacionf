import { apiFetch } from './api.js';
import { getToken, logout, getUser } from './auth.js';

const params = new URLSearchParams(window.location.search);
const id = params.get('id');

async function cargarDetalle() {
  try {
    const atraccion = await apiFetch(`/atracciones/${id}`);

    document.getElementById('detalle').innerHTML = `
      <div>
        ${atraccion.imagen_url ? `<img src="${atraccion.imagen_url}" class="w-full h-64 object-cover rounded mb-4">` : ''}
        <h1 class="text-3xl font-bold mb-2">${atraccion.nombre}</h1>
        <p class="text-gray-600 text-lg mb-2">📍 ${atraccion.ubicacion}</p>
        <p class="text-gray-700 mb-4">${atraccion.descripcion}</p>
        <p class="text-xl font-semibold text-blue-600">Precio: $${atraccion.precio || 'Consultar'}</p>
        <p class="text-gray-600 mt-2">Categoría: ${atraccion.categoria}</p>
      </div>
    `;

    // Mostrar formulario si está autenticado
    if (getToken()) {
      const user = getUser();
      document.getElementById('reservaContainer').classList.remove('hidden');
      if (user) {
        document.getElementById('usuarioNombre').textContent = `Hola, ${user.name}`;
      }
      document.getElementById('logoutBtn').classList.remove('hidden');
    } else {
      document.getElementById('noAuthContainer').classList.remove('hidden');
    }

  } catch (error) {
    document.getElementById('detalle').innerHTML = '<p class="text-red-600">Error cargando el detalle</p>';
  }
}

// Logout
document.getElementById('logoutBtn').addEventListener('click', () => {
  logout();
});

// Formulario de reserva
document.getElementById('formReserva').addEventListener('submit', async (e) => {
  e.preventDefault();

  const errorDiv = document.getElementById('reservaError');
  const successDiv = document.getElementById('reservaSuccess');
  errorDiv.classList.add('hidden');
  successDiv.classList.add('hidden');

  try {
    const data = {
      atraccion_id: parseInt(id),
      fecha: e.target.fecha.value,
      hora: e.target.hora.value,
      comentarios: e.target.comentarios.value || null
    };

    await apiFetch('/reservas', {
      method: 'POST',
      body: JSON.stringify(data)
    });

    successDiv.textContent = 'Reserva creada exitosamente';
    successDiv.classList.remove('hidden');
    e.target.reset();

    setTimeout(() => {
      window.location.href = 'reservas.html';
    }, 2000);
  } catch (error) {
    // Manejar error 409 de doble reserva
    if (error.status === 409 || error.message?.includes('ya tiene una reserva')) {
      errorDiv.textContent = 'Esta atracción ya tiene una reserva aceptada en esa fecha y hora. Por favor, elige otro horario.';
    } else {
      errorDiv.textContent = error.message || 'Error al crear reserva';
    }
    errorDiv.classList.remove('hidden');
  }
});

cargarDetalle();
