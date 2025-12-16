/**
 * filters.js - SISTEMA COMPLETO DE FILTROS Y PAGINACIÓN
 * 
 * Propósito: Gestionar todos los filtros de búsqueda:
 * - Filtrado por provincia
 * - Búsqueda en tiempo real
 * - Paginación inteligente
 * - Renderizado de tarjetas
 * 
 * Arquitectura: Clase FilterManager que centraliza toda la lógica
 */

// CONSTANTES: Lista de todas las provincias de Ecuador
// Se usa para llenar dropdown de filtros
const PROVINCES = [
    'Pichincha', 'Guayas', 'Azuay', 'Manabí', 'Los Ríos', 'Tungurahua',
    'Imbabura', 'Cotopaxi', 'Morona Santiago', 'Pastaza', 'Napo', 'Sucumbíos',
    'Orellana', 'Santa Elena', 'El Oro', 'Loja', 'Zamora Chinchipe', 'Chimborazo', 'Cañar',
    'Esmeraldas', 'Carchi', 'Bolívar', 'Galápagos'
];

// CONSTANTES: Lista de categorías (aunque no se usa actualmente en UI)
// Se mantiene para futura expansión
const CATEGORIES = ['Parques', 'Museos', 'Restaurantes', 'Aventura', 'Playas', 'Montañas'];

/**
 * FilterManager - Clase principal que gestiona toda la lógica de filtros
 * 
 * Responsabilidades:
 * 1. Renderizar UI de filtros (dropdown, input búsqueda)
 * 2. Escuchar cambios en filtros
 * 3. Hacer requests a API con parámetros correctos
 * 4. Renderizar tarjetas de atracciones
 * 5. Manejar paginación
 */
class FilterManager {
    /**
     * constructor() - Inicializar propiedades
     * 
     * currentPage: número de página actual (comienza en 1)
     * perPage: atracciones por página (20 por defecto)
     * totalPages: total de páginas (calculado por API)
     * filters: objeto con filtros activos (provincia, búsqueda)
     */
    constructor() {
        this.currentPage = 1;          // Página actual
        this.perPage = 20;             // Atracciones por página
        this.totalPages = 1;           // Total de páginas (actualizado por API)
        this.filters = {
            provincia: '',             // Provincia seleccionada
            categoria: '',             // Categoría (no usado actualmente)
            search: ''                 // Términos de búsqueda
        };
    }

    /**
     * initFilters() - Punto de entrada para inicializar el sistema
     * 
     * Flujo:
     * 1. Renderizar UI de filtros (dropdown, input, botón)
     * 2. Agregar event listeners a elementos
     * 3. Cargar atracciones iniciales
     */
    initFilters() {
        // Crear HTML de filtros
        this.renderFilterUI();
        // Agregar listeners a botones/inputs
        this.attachEventListeners();
    }

    /**
     * renderFilterUI() - Crear y insertar HTML de filtros en el DOM
     * 
     * HTML generado:
     * - Dropdown de provincias con "Todas las provincias"
     * - Input de búsqueda con placeholder
     * - Botón "Limpiar filtros"
     * 
     * Estilos: Glassmorphism (fondo gradiente, blur, semi-transparencia)
     */
    renderFilterUI() {
        // Obtener elemento contenedor de filtros
        const filterContainer = document.getElementById('filter-container');
        if (!filterContainer) return; // Si no existe contenedor, no hacer nada

        // Generar HTML con dropdown de provincias
        // map(): convertir array de provincias a <option> tags
        // join(''): concatenar todos los strings sin separador
        filterContainer.innerHTML = `
            <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 30px; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);">
                
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; color: white; font-weight: 600; margin-bottom: 8px;">Provincia</label>
                    <!-- Dropdown de provincias: onChange → applyFilters() -->
                    <select id="provincia-filter" style="width: 100%; padding: 10px; border: none; border-radius: 8px; background: rgba(255,255,255,0.9); color: #333; font-size: 14px;">
                        <option value="">Todas las provincias</option>
                        ${PROVINCES.map(p => `<option value="${p}">${p}</option>`).join('')}
                    </select>
                </div>

                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; color: white; font-weight: 600; margin-bottom: 8px;">Buscar</label>
                    <!-- Input de búsqueda: onInput → applyFilters() -->
                    <input id="search-filter" type="text" placeholder="Nombre de atracción..." style="width: 100%; padding: 10px; border: none; border-radius: 8px; background: rgba(255,255,255,0.9); color: #333; font-size: 14px;">
                </div>

                <div style="display: flex; align-items: flex-end;">
                    <!-- Botón para limpiar todos los filtros -->
                    <button id="reset-filters" style="padding: 10px 20px; background: rgba(255,255,255,0.3); color: white; border: 2px solid white; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s;">
                        Limpiar filtros
                    </button>
                </div>
            </div>
        `;
    }

    /**
     * attachEventListeners() - Agregar listeners a elementos de filtro
     * 
     * Eventos:
     * 1. onChange en dropdown provincia → applyFilters()
     * 2. onInput en input búsqueda → applyFilters() (en tiempo real)
     * 3. onClick en botón reset → resetFilters()
     */
    attachEventListeners() {
        // Listener para dropdown provincia
        // change: dispara cuando usuario selecciona opción diferente
        document.getElementById('provincia-filter')?.addEventListener('change', () => {
            this.applyFilters();
        });

        // Listener para input búsqueda
        // input: dispara en cada keystroke (búsqueda en tiempo real)
        // Si escribes "mus", dispara al escribir m, u, s
        document.getElementById('search-filter')?.addEventListener('input', () => {
            this.applyFilters();
        });

        // Listener para botón limpiar
        // click: dispara al hacer click
        document.getElementById('reset-filters')?.addEventListener('click', () => {
            this.resetFilters();
        });
    }

    /**
     * applyFilters() - Aplicar filtros activos y cargar datos
     * 
     * Flujo:
     * 1. Obtener valores actuales de inputs
     * 2. Guardar en this.filters
     * 3. Resetear a página 1 (importante: si filtros cambian, volver al inicio)
     * 4. Hacer request a API con nuevos filtros
     */
    applyFilters() {
        // Obtener valor de dropdown provincia (vacío si no seleccionó)
        this.filters.provincia = document.getElementById('provincia-filter')?.value || '';
        // Obtener valor de input búsqueda (vacío si no escribió)
        this.filters.search = document.getElementById('search-filter')?.value || '';
        
        // Cuando filtros cambian, volver a página 1
        // (no tiene sentido estar en página 5 con filtro diferente)
        this.currentPage = 1;
        
        // Cargar atracciones con nuevos filtros
        this.loadAtracciones();
    }

    /**
     * resetFilters() - Limpiar todos los filtros
     * 
     * Flujo:
     * 1. Vaciar objeto filters
     * 2. Limpiar valores en inputs del DOM
     * 3. Resetear a página 1
     * 4. Cargar todas las atracciones
     */
    resetFilters() {
        // Limpiar objeto filtros
        this.filters = { provincia: '', search: '' };
        
        // Limpiar valores en inputs
        // Poner value = '' para mostrar estado limpio al usuario
        document.getElementById('provincia-filter').value = '';
        document.getElementById('search-filter').value = '';
        
        // Volver a primera página
        this.currentPage = 1;
        
        // Cargar todas las atracciones
        this.loadAtracciones();
    }

    /**
     * loadAtracciones() - Hacer request a API y cargar datos
     * 
     * Flujo:
     * 1. Construir URLSearchParams con parámetros
     * 2. Hacer fetch a /api/atracciones?provincia=...&search=...&page=...
     * 3. Parsear JSON response
     * 4. Guardar total de páginas
     * 5. Renderizar tarjetas
     * 6. Renderizar paginación
     * 
     * Query parameters:
     * - page: número de página
     * - per_page: atracciones por página
     * - provincia: provincia seleccionada (opcional)
     * - search: términos de búsqueda (opcional)
     * - categoria: categoría (opcional)
     */
    async loadAtracciones() {
        try {
            // URLSearchParams: helper para construir query strings
            // Ejemplo resultado: "page=1&per_page=20&provincia=Pichincha&search=museo"
            const params = new URLSearchParams({
                page: this.currentPage,
                per_page: this.perPage,
                // Spread condicional: solo incluir si filter tiene valor
                ...(this.filters.provincia && { provincia: this.filters.provincia }),
                ...(this.filters.categoria && { categoria: this.filters.categoria }),
                ...(this.filters.search && { search: this.filters.search })
            });

            // Hacer fetch a API
            const response = await fetch(`${API_BASE}/atracciones?${params}`);
            
            // Verificar si response fue exitosa
            if (!response.ok) {
                console.error('Error al cargar atracciones:', response.status);
                return;
            }

            // Parsear JSON
            const data = await response.json();
            
            // Guardar total de páginas (para renderizar botones)
            this.totalPages = data.last_page;
            
            // Renderizar tarjetas con datos
            this.renderAtracciones(data.data);
            
            // Renderizar botones de paginación
            this.renderPagination(data);

        } catch (error) {
            // Si hay error de red, mostrar en consola
            console.error('Error:', error);
        }
    }

    /**
     * renderAtracciones() - Renderizar tarjetas de atracciones en grid
     * 
     * Para cada atracción, crear tarjeta con:
     * - Imagen (con fallback si no existe)
     * - Badge de provincia
     * - Nombre
     * - Descripción (primeros 80 caracteres)
     * - Ubicación
     * - Precio (si existe)
     * 
     * Estilos: Glassmorphism con hover effects
     * 
     * @param {array} atracciones - Array de objetos atracción
     */
    renderAtracciones(atracciones) {
        // Obtener contenedor donde insertar tarjetas
        const container = document.getElementById('atracciones-container') || document.getElementById('catalog');
        if (!container) return;

        // Si no hay resultados, mostrar mensaje
        if (atracciones.length === 0) {
            container.innerHTML = '<p style="text-align: center; padding: 40px; color: #666; font-size: 18px;">No se encontraron atracciones con los filtros seleccionados.</p>';
            return;
        }

        // Map: transformar array de atracciones a array de strings HTML
        // Template literal: usar `backticks` para HTML multi-línea
        container.innerHTML = atracciones.map(atraccion => `
            <div class="atraccion-card" onclick="mostrarDetalle(${atraccion.id})" style="cursor: pointer; transition: transform 0.3s, box-shadow 0.3s;">
                <!-- Contenedor de imagen -->
                <div style="position: relative; height: 200px; background: #e0e0e0; border-radius: 10px 10px 0 0; overflow: hidden;">
                    <!-- Imagen: si no existe, usar placeholder gris -->
                    <img src="${atraccion.imagen_url || 'https://via.placeholder.com/300x200?text=Sin+imagen'}" alt="${atraccion.nombre}" style="width: 100%; height: 100%; object-fit: cover;">
                    
                    <!-- Badge de provincia (esquina superior izquierda) -->
                    ${atraccion.provincia ? `<div style="position: absolute; top: 10px; left: 10px; background: #764ba2; color: white; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 600;">${atraccion.provincia}</div>` : ''}
                </div>
                
                <!-- Contenido tarjeta -->
                <div style="padding: 15px;">
                    <!-- Nombre de atracción -->
                    <h3 style="margin: 0 0 8px 0; color: #333; font-size: 16px;">${atraccion.nombre}</h3>
                    
                    <!-- Descripción (primeros 80 caracteres con ...) -->
                    <p style="margin: 0 0 10px 0; color: #666; font-size: 13px; line-height: 1.4;">${atraccion.descripcion.substring(0, 80)}...</p>
                    
                    <!-- Ubicación con emoji ubicación -->
                    <p style="margin: 0 0 10px 0; color: #999; font-size: 12px;">📍 ${atraccion.ubicacion}</p>
                    
                    <!-- Precio (solo si existe) -->
                    ${atraccion.precio ? `<p style="margin: 0; color: #667eea; font-weight: 600; font-size: 14px;">$${atraccion.precio}</p>` : ''}
                </div>
            </div>
        `).join('');
    }

    /**
     * renderPagination() - Renderizar botones de paginación
     * 
     * Elementos:
     * 1. Botón "← Anterior" (si no es primera página)
     * 2. Botones de números de página (con ... para saltar)
     * 3. Botón "Siguiente →" (si no es última página)
     * 4. Info: "Mostrando 1-20 de 425"
     * 
     * Ejemplo: 1 2 3 ... 10 (actual) ... 20 21
     * 
     * @param {object} data - Response de API con metadata paginación
     */
    renderPagination(data) {
        // Obtener contenedor de paginación
        const paginationContainer = document.getElementById('pagination-container');
        if (!paginationContainer) return;

        // HTML string que iremos construyendo
        let paginationHTML = '<div style="display: flex; justify-content: center; gap: 5px; margin-top: 30px; flex-wrap: wrap;">';

        // BOTÓN ANTERIOR
        // Solo mostrar si no es primera página (current_page > 1)
        if (data.current_page > 1) {
            paginationHTML += `<button onclick="filterManager.goToPage(${data.current_page - 1})" style="padding: 10px 12px; border: 2px solid #667eea; background: white; color: #667eea; border-radius: 5px; cursor: pointer; font-weight: 600; transition: all 0.3s;">← Anterior</button>`;
        }

        // BOTONES DE NÚMEROS
        // Mostrar: primera, última, y páginas cercanas a actual
        // Ejemplo: Si estamos en página 5 de 20, mostrar: 1 ... 3 4 5 6 7 ... 20
        for (let i = 1; i <= data.last_page; i++) {
            // Condición: mostrar si es primera, última, o cercana a actual
            if (i === 1 || i === data.last_page || (i >= data.current_page - 1 && i <= data.current_page + 1)) {
                // Si es página actual, botón destacado (fondo de color)
                if (i === data.current_page) {
                    paginationHTML += `<button style="padding: 10px 12px; border: 2px solid #667eea; background: #667eea; color: white; border-radius: 5px; font-weight: 600; cursor: pointer;">${i}</button>`;
                } else {
                    // Si no es actual, botón sin fondo
                    paginationHTML += `<button onclick="filterManager.goToPage(${i})" style="padding: 10px 12px; border: 2px solid #ddd; background: white; color: #667eea; border-radius: 5px; cursor: pointer; font-weight: 600; transition: all 0.3s;">${i}</button>`;
                }
            } 
            // Mostrar "..." para saltear páginas
            else if (i === data.current_page - 2 || i === data.current_page + 2) {
                paginationHTML += `<span style="padding: 10px 5px;">...</span>`;
            }
        }

        // BOTÓN SIGUIENTE
        // Solo mostrar si no es última página (current_page < last_page)
        if (data.current_page < data.last_page) {
            paginationHTML += `<button onclick="filterManager.goToPage(${data.current_page + 1})" style="padding: 10px 12px; border: 2px solid #667eea; background: white; color: #667eea; border-radius: 5px; cursor: pointer; font-weight: 600; transition: all 0.3s;">Siguiente →</button>`;
        }

        paginationHTML += '</div>';

        // Insertar botones en DOM
        paginationContainer.innerHTML = paginationHTML;

        // INFO DE PAGINACIÓN
        // Mostrar: "Mostrando 1-20 de 425"
        // Cálculo: 
        // - Inicio: (page - 1) * per_page + 1
        // - Fin: Math.min(page * per_page, total)
        const infoHTML = `<p style="text-align: center; color: #666; margin-top: 20px; font-size: 14px;">
            Mostrando ${(data.current_page - 1) * data.per_page + 1} a ${Math.min(data.current_page * data.per_page, data.total)} de ${data.total} atracciones
        </p>`;
        
        // Insertar info en su contenedor
        const infoContainer = document.getElementById('pagination-info');
        if (infoContainer) {
            infoContainer.innerHTML = infoHTML;
        }
    }

    /**
     * goToPage() - Navegar a página específica
     * 
     * Flujo:
     * 1. Actualizar currentPage
     * 2. Cargar atracciones nuevas
     * 3. Scroll suave al top (para que vea nuevas tarjetas)
     * 
     * @param {number} page - Número de página a ir
     */
    goToPage(page) {
        // Actualizar página actual
        this.currentPage = page;
        
        // Cargar atracciones de la nueva página
        this.loadAtracciones();
        
        // Scroll suave al top de página (user experience)
        // behavior: 'smooth' = animación en lugar de salto inmediato
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// ============ INICIALIZACIÓN GLOBAL ============

// Crear instancia global de FilterManager
// Se usa en onclick="filterManager.goToPage(2)"
const filterManager = new FilterManager();

// Esperar a que DOM esté completamente cargado antes de inicializar
// DOMContentLoaded: dispara cuando todos los elementos HTML están cargados
document.addEventListener('DOMContentLoaded', () => {
    // Renderizar UI de filtros y cargar atracciones iniciales
    filterManager.initFilters();
    // Primera carga: sin filtros (mostrar todas)
    filterManager.loadAtracciones();
});
