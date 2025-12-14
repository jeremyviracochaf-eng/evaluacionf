import { apiFetch } from './api.js';

const grid = document.getElementById('grid');

async function cargarAtracciones() {
  try {
    const atracciones = await apiFetch('/atracciones');

    grid.innerHTML = atracciones.map(a => `
      <div class="bg-white rounded shadow overflow-hidden">
        <img src="${a.imagen_url}" class="h-48 w-full object-cover">
        <div class="p-4">
          <h2 class="font-bold text-lg">${a.nombre}</h2>
          <p class="text-sm text-gray-600">${a.ubicacion}</p>
          <a href="detalle.html?id=${a.id}"
             class="inline-block mt-3 text-blue-600 font-semibold">
             Ver detalle →
          </a>
        </div>
      </div>
    `).join('');

  } catch (e) {
    grid.innerHTML = '<p>Error cargando atracciones</p>';
  }
}

cargarAtracciones();
