import { apiFetch } from './api.js';

const params = new URLSearchParams(window.location.search);
const id = params.get('id');

async function cargarDetalle() {
  const atraccion = await apiFetch(`/atracciones/${id}`);

  document.getElementById('detalle').innerHTML = `
    <img src="${atraccion.imagen_url}" class="w-full h-64 object-cover rounded">
    <h1 class="text-2xl font-bold mt-4">${atraccion.nombre}</h1>
    <p class="text-gray-600">${atraccion.ubicacion}</p>
  `;
}

cargarDetalle();

document.getElementById('formReserva').addEventListener('submit', async e => {
  e.preventDefault();

  const data = {
    atraccion_id: id,
    fecha: e.target.fecha.value,
    hora: e.target.hora.value,
    comentarios: e.target.comentarios.value
  };

  await apiFetch('/reservas', {
    method: 'POST',
    body: JSON.stringify(data)
  });

  alert('Reserva creada');
});