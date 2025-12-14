const API = "http://127.0.0.1:8000/api/atracciones";

fetch(API)
  .then(res => res.json())
  .then(data => {
    const contenedor = document.getElementById("atracciones");

    data.forEach(a => {
      contenedor.innerHTML += `
        <div class="bg-white rounded shadow">
          <img src="${a.imagen_url}" class="h-48 w-full object-cover rounded-t">
          <div class="p-4">
            <h3 class="font-bold text-lg">${a.nombre}</h3>
            <p class="text-sm text-gray-600">${a.ubicacion}</p>
            <a href="login.html"
               class="block mt-3 bg-blue-600 text-white text-center py-1 rounded">
               Reservar
            </a>
          </div>
        </div>
      `;
    });
  });
