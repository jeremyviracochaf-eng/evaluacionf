// filters.js - Manejo de filtros y paginación

const PROVINCES = [
    'Pichincha', 'Guayas', 'Azuay', 'Manabí', 'Los Ríos', 'Tungurahua',
    'Imbabura', 'Cotopaxi', 'Morona Santiago', 'Pastaza', 'Napo', 'Sucumbíos',
    'Orellana', 'Santa Elena', 'El Oro', 'Loja', 'Zamora Chinchipe', 'Chimborazo', 'Cañar',
    'Esmeraldas', 'Carchi', 'Bolívar', 'Galápagos'
];

const CATEGORIES = ['Parques', 'Museos', 'Restaurantes', 'Aventura', 'Playas', 'Montañas'];

class FilterManager {
    constructor() {
        this.currentPage = 1;
        this.perPage = 20;
        this.totalPages = 1;
        this.filters = {
            provincia: '',
            categoria: '',
            search: ''
        };
    }

    initFilters() {
        this.renderFilterUI();
        this.attachEventListeners();
    }

    renderFilterUI() {
        const filterContainer = document.getElementById('filter-container');
        if (!filterContainer) return;

        filterContainer.innerHTML = `
            <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 30px; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);">
                
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; color: white; font-weight: 600; margin-bottom: 8px;">Provincia</label>
                    <select id="provincia-filter" style="width: 100%; padding: 10px; border: none; border-radius: 8px; background: rgba(255,255,255,0.9); color: #333; font-size: 14px;">
                        <option value="">Todas las provincias</option>
                        ${PROVINCES.map(p => `<option value="${p}">${p}</option>`).join('')}
                    </select>
                </div>

                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; color: white; font-weight: 600; margin-bottom: 8px;">Buscar</label>
                    <input id="search-filter" type="text" placeholder="Nombre de atracción..." style="width: 100%; padding: 10px; border: none; border-radius: 8px; background: rgba(255,255,255,0.9); color: #333; font-size: 14px;">
                </div>

                <div style="display: flex; align-items: flex-end;">
                    <button id="reset-filters" style="padding: 10px 20px; background: rgba(255,255,255,0.3); color: white; border: 2px solid white; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s;">
                        Limpiar filtros
                    </button>
                </div>
            </div>
        `;
    }

    attachEventListeners() {
        document.getElementById('provincia-filter')?.addEventListener('change', () => {
            this.applyFilters();
        });

        document.getElementById('search-filter')?.addEventListener('input', () => {
            this.applyFilters();
        });

        document.getElementById('reset-filters')?.addEventListener('click', () => {
            this.resetFilters();
        });
    }

    applyFilters() {
        this.filters.provincia = document.getElementById('provincia-filter')?.value || '';
        this.filters.search = document.getElementById('search-filter')?.value || '';
        
        this.currentPage = 1;
        this.loadAtracciones();
    }

    resetFilters() {
        this.filters = { provincia: '', search: '' };
        document.getElementById('provincia-filter').value = '';
        document.getElementById('search-filter').value = '';
        this.currentPage = 1;
        this.loadAtracciones();
    }

    async loadAtracciones() {
        try {
            const params = new URLSearchParams({
                page: this.currentPage,
                per_page: this.perPage,
                ...(this.filters.provincia && { provincia: this.filters.provincia }),
                ...(this.filters.categoria && { categoria: this.filters.categoria }),
                ...(this.filters.search && { search: this.filters.search })
            });

            const response = await fetch(`${API_BASE}/atracciones?${params}`);
            
            if (!response.ok) {
                console.error('Error al cargar atracciones:', response.status);
                return;
            }

            const data = await response.json();
            
            this.totalPages = data.last_page;
            this.renderAtracciones(data.data);
            this.renderPagination(data);

        } catch (error) {
            console.error('Error:', error);
        }
    }

    renderAtracciones(atracciones) {
        const container = document.getElementById('atracciones-container') || document.getElementById('catalog');
        if (!container) return;

        if (atracciones.length === 0) {
            container.innerHTML = '<p style="text-align: center; padding: 40px; color: #666; font-size: 18px;">No se encontraron atracciones con los filtros seleccionados.</p>';
            return;
        }

        container.innerHTML = atracciones.map(atraccion => `
            <div class="atraccion-card" onclick="mostrarDetalle(${atraccion.id})" style="cursor: pointer; transition: transform 0.3s, box-shadow 0.3s;">
                <div style="position: relative; height: 200px; background: #e0e0e0; border-radius: 10px 10px 0 0; overflow: hidden;">
                    <img src="${atraccion.imagen_url || 'https://via.placeholder.com/300x200?text=Sin+imagen'}" alt="${atraccion.nombre}" style="width: 100%; height: 100%; object-fit: cover;">
                    ${atraccion.provincia ? `<div style="position: absolute; top: 10px; left: 10px; background: #764ba2; color: white; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">${atraccion.provincia}</div>` : ''}
                </div>
                <div style="padding: 15px;">
                    <h3 style="margin: 0 0 8px 0; color: #333; font-size: 16px;">${atraccion.nombre}</h3>
                    <p style="margin: 0 0 10px 0; color: #666; font-size: 13px; line-height: 1.4;">${atraccion.descripcion.substring(0, 80)}...</p>
                    <p style="margin: 0 0 10px 0; color: #999; font-size: 12px;">📍 ${atraccion.ubicacion}</p>
                    ${atraccion.precio ? `<p style="margin: 0; color: #667eea; font-weight: 600; font-size: 14px;">$${atraccion.precio}</p>` : ''}
                </div>
            </div>
        `).join('');
    }

    renderPagination(data) {
        const paginationContainer = document.getElementById('pagination-container');
        if (!paginationContainer) return;

        let paginationHTML = '<div style="display: flex; justify-content: center; gap: 5px; margin-top: 30px; flex-wrap: wrap;">';

        // Botón anterior
        if (data.current_page > 1) {
            paginationHTML += `<button onclick="filterManager.goToPage(${data.current_page - 1})" style="padding: 10px 12px; border: 2px solid #667eea; background: white; color: #667eea; border-radius: 5px; cursor: pointer; font-weight: 600; transition: all 0.3s;">← Anterior</button>`;
        }

        // Números de página
        for (let i = 1; i <= data.last_page; i++) {
            if (i === 1 || i === data.last_page || (i >= data.current_page - 1 && i <= data.current_page + 1)) {
                if (i === data.current_page) {
                    paginationHTML += `<button style="padding: 10px 12px; border: 2px solid #667eea; background: #667eea; color: white; border-radius: 5px; font-weight: 600; cursor: pointer;">${i}</button>`;
                } else {
                    paginationHTML += `<button onclick="filterManager.goToPage(${i})" style="padding: 10px 12px; border: 2px solid #ddd; background: white; color: #667eea; border-radius: 5px; cursor: pointer; font-weight: 600; transition: all 0.3s;">${i}</button>`;
                }
            } else if (i === data.current_page - 2 || i === data.current_page + 2) {
                paginationHTML += `<span style="padding: 10px 5px;">...</span>`;
            }
        }

        // Botón siguiente
        if (data.current_page < data.last_page) {
            paginationHTML += `<button onclick="filterManager.goToPage(${data.current_page + 1})" style="padding: 10px 12px; border: 2px solid #667eea; background: white; color: #667eea; border-radius: 5px; cursor: pointer; font-weight: 600; transition: all 0.3s;">Siguiente →</button>`;
        }

        paginationHTML += '</div>';

        paginationContainer.innerHTML = paginationHTML;

        // Info de paginación
        const infoHTML = `<p style="text-align: center; color: #666; margin-top: 20px; font-size: 14px;">
            Mostrando ${(data.current_page - 1) * data.per_page + 1} a ${Math.min(data.current_page * data.per_page, data.total)} de ${data.total} atracciones
        </p>`;
        
        const infoContainer = document.getElementById('pagination-info');
        if (infoContainer) {
            infoContainer.innerHTML = infoHTML;
        }
    }

    goToPage(page) {
        this.currentPage = page;
        this.loadAtracciones();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// Instancia global
const filterManager = new FilterManager();

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    filterManager.initFilters();
    filterManager.loadAtracciones();
});
