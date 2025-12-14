# Frontend - Atracciones Turísticas

## Descripción General

Este es el frontend para el sistema de reservas de atracciones turísticas. Está construido con HTML5, Tailwind CSS y JavaScript ES6 módulos, comunicándose con una API Laravel Sanctum.

## Características Principales

### Para Usuarios Normales
- ✅ Ver catálogo de atracciones
- ✅ Ver detalle de cada atracción
- ✅ Crear reservas con fecha, hora y comentarios
- ✅ Ver mis reservas
- ✅ Cancelar reservas
- ✅ Gestión de cuenta (login/logout/registro)

### Para Administradores
- ✅ Todo lo de usuarios normales, más:
- ✅ Crear nuevas atracciones
- ✅ Editar atracciones existentes
- ✅ Eliminar atracciones
- ✅ Gestionar todas las reservas del sistema
- ✅ Cambiar estado de reservas (pendiente/aceptada/rechazada)
- ✅ Ver información de usuarios en reservas

## Estructura de Archivos

```
frontend/
├── index.html              # Catálogo principal de atracciones
├── login.html              # Página de inicio de sesión
├── register.html           # Página de registro
├── detalle.html            # Detalle de atracción + formulario de reserva
├── reservas.html           # Mis reservas (usuario)
├── admin.html              # Panel de admin - gestión de atracciones
├── reservas-admin.html     # Panel de admin - gestión de reservas
└── js/
    ├── api.js              # Módulo de comunicación con API
    ├── auth.js             # Módulo de autenticación y persistencia
    ├── admin.js            # Lógica del panel admin
    ├── detalle.js          # Lógica de detalle de atracción
    └── [otros archivos]
```

## Flujo de Autenticación

1. **Registro**: Usuario se registra con nombre, email y contraseña
2. **Login**: Usuario inicia sesión con email y contraseña
3. **Token**: Se almacena el JWT token en localStorage
4. **Usuario**: Se almacenan datos del usuario en localStorage
5. **Rol**: Se utiliza el rol (admin/user) para mostrar opciones

## Cómo Usar

### Para Usuarios Normales

#### 1. Registro
```
1. Ir a register.html
2. Completar formulario (nombre, email, contraseña)
3. Confirmar contraseña
4. Hacer click en "Registrarse"
5. Se redirige automáticamente a index.html
```

#### 2. Ver Atracciones
```
1. En index.html se muestra grid de todas las atracciones
2. Cada tarjeta muestra: nombre, ubicación, descripción, precio
3. Hacer click en "Ver detalle" para más información
```

#### 3. Hacer Reserva
```
1. Desde detalle.html hacer click en "Ver detalle"
2. Si está autenticado, aparece formulario de reserva
3. Seleccionar fecha y hora
4. Agregar comentarios (opcional)
5. Hacer click en "Reservar"
6. Se redirige a reservas.html
```

#### 4. Ver Mis Reservas
```
1. Hacer click en "Mis Reservas" en header
2. Ver lista de todas las reservas
3. Estado color-codificado:
   - Verde: Aceptada
   - Rojo: Rechazada
   - Amarillo: Pendiente
4. Opción de cancelar reserva
```

### Para Administradores

#### 1. Acceso al Panel
```
1. Hacer login con cuenta de admin
2. Se redirige automáticamente a admin.html
3. O ir a admin.html desde cualquier lugar (si está autenticado como admin)
```

#### 2. Crear Atracción
```
1. En admin.html, completar formulario de "Crear Nueva Atracción"
2. Campos: nombre, categoría, ubicación, precio, descripción, URL de imagen
3. Hacer click en "Crear Atracción"
4. Aparece confirmación verde
```

#### 3. Editar Atracción
```
1. En grid de atracciones, hacer click en "Editar"
2. Se abre modal con datos de la atracción
3. Modificar los campos deseados
4. Hacer click en "Guardar"
5. Grid se actualiza automáticamente
```

#### 4. Eliminar Atracción
```
1. En grid de atracciones, hacer click en "Eliminar"
2. Confirmar eliminación
3. Atracción se remueve del sistema
```

#### 5. Gestionar Reservas
```
1. Hacer click en "Gestionar Reservas" en header de admin.html
2. Ver todas las reservas del sistema
3. Información incluye: usuario, atracción, fecha, hora, comentarios
4. Cambiar estado desde dropdown:
   - Pendiente: Nueva reserva, a la espera de aprobación
   - Aceptada: Reserva confirmada
   - Rechazada: Reserva cancelada
5. Filtrar por estado si es necesario
```

## Manejo de Errores

### Error 409 - Doble Reserva
Cuando intenta hacer una reserva en una atracción que ya tiene una reserva aceptada a esa hora:
```
Mensaje: "Esta atracción ya tiene una reserva aceptada en esa fecha y hora. 
Por favor, elige otro horario."
```

### Errores de Validación
Si completa mal un formulario, verá los errores específicos:
```
- "Email no válido"
- "Contraseña debe tener mínimo 8 caracteres"
- "El email ya está registrado"
```

## Configuración

### URL del API
La URL del API está configurada en `frontend/js/api.js`:
```javascript
const API_URL = 'http://127.0.0.1:8000/api';
```

Si su API está en una URL diferente, actualice esta línea.

## Requisitos

- Navegador moderno (Chrome, Firefox, Safari, Edge)
- JavaScript habilitado
- Conexión a Internet para acceder al API
- LocalStorage habilitado para persistencia de sesión

## Cambios Recientes

### Versión 2.0 (Actualización)
- ✅ Mejorado manejo de errores
- ✅ Mostrado nombre real del usuario
- ✅ CRUD completo de atracciones para admin
- ✅ Nueva página de gestión de reservas para admin
- ✅ Mejor experiencia de usuario con modales
- ✅ Validación de rol admin mejorada
- ✅ Error 409 (doble reserva) manejado específicamente

## Próximas Mejoras

- [ ] Subida de imágenes directamente desde formulario
- [ ] Importación desde Google Places
- [ ] Búsqueda y filtrado avanzado
- [ ] Paginación
- [ ] Exportación de reportes
- [ ] Notificaciones en tiempo real

## Soporte Técnico

### Si no funciona el login
1. Verificar que el API está corriendo
2. Verificar URL del API en `js/api.js`
3. Verificar que LocalStorage está habilitado
4. Limpiar cache del navegador

### Si no ve el Panel Admin
1. Verificar que su usuario tiene rol 'admin' en la base de datos
2. Hacer logout y login nuevamente
3. Verificar que está siendo redirigido a admin.html al hacer login

### Si las imágenes no cargan
1. Verificar que las URLs de las imágenes son válidas
2. Verificar que no tiene adblocker bloqueando las imágenes
3. Abrir consola (F12) para ver errores específicos
