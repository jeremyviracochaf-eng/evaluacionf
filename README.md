# 🎉 Atracciones Turísticas - Sistema de Reservas

> Sistema completo de gestión y reserva de atracciones turísticas en Pichincha, Ecuador

## ✅ Estado: COMPLETADO Y FUNCIONAL

---

## 🚀 Inicio Rápido

### 1. Preparación
```bash
# Instalar dependencias
composer install

# Configurar .env
cp .env.example .env

# Generar clave
php artisan key:generate

# Ejecutar migraciones
php artisan migrate
```

### 2. Ejecutar
```bash
# Iniciar servidor Laravel
php artisan serve
# Acceso: http://127.0.0.1:8000
# Frontend: http://127.0.0.1:8000/frontend/index.html
```

### 3. Pruebas
- Registrarse en `frontend/register.html`
- Ver catálogo en `frontend/index.html`
- Admin: Actualizar rol en BD y login

---

## 📚 Documentación Completa

| Documento | Descripción |
|-----------|------------|
| [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md) | Resumen ejecutivo y arquitectura |
| [FRONTEND_README.md](FRONTEND_README.md) | Guía del frontend |
| [TESTING_GUIDE.md](TESTING_GUIDE.md) | 15 tests de validación |
| [CHANGELOG.md](CHANGELOG.md) | Cambios en v2.0 |
| [FRONTEND_UPDATES.md](FRONTEND_UPDATES.md) | Mejoras implementadas |

---

## ✨ Características

### Usuario Normal ✅
- Registro e inicio de sesión
- Catálogo de atracciones
- Ver detalle de atracción
- Crear reservas
- Ver mis reservas
- Cancelar reservas

### Administrador ✅
- **Gestión de Atracciones**
  - Crear nueva atracción
  - Editar atracción existente
  - Eliminar atracción
  - Ver todas con detalles

- **Gestión de Reservas**
  - Ver todas las reservas
  - Cambiar estado (pendiente/aceptada/rechazada)
  - Filtrar por estado
  - Ver info del usuario

---

## 🏗️ Arquitectura

**Backend:** Laravel 11 + Sanctum + MySQL  
**Frontend:** HTML5 + Tailwind CSS + ES6 JavaScript  
**API:** RESTful con autenticación JWT  

---

## 📡 API Endpoints Principales

```
Auth:
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/auth/me

Atracciones:
GET    /api/atracciones
POST   /api/atracciones          (admin)
PUT    /api/atracciones/{id}     (admin)
DELETE /api/atracciones/{id}     (admin)

Reservas:
GET    /api/reservas
POST   /api/reservas
DELETE /api/reservas/{id}
PUT    /api/reservas/{id}/estado (admin)
```

---

## 🎯 Rutas del Frontend

| Página | Descripción |
|--------|-----------|
| `index.html` | Catálogo de atracciones |
| `login.html` | Iniciar sesión |
| `register.html` | Crear cuenta |
| `detalle.html` | Detalle + formulario de reserva |
| `reservas.html` | Mis reservas |
| `admin.html` | Gestión de atracciones |
| `reservas-admin.html` | Gestión de reservas |

---

## 🔒 Seguridad

✅ Autenticación con JWT (Sanctum)  
✅ Hashing de contraseñas  
✅ Validación de entrada  
✅ Control de acceso por rol  
✅ Prevención de doble reserva  
✅ CORS configurado  

---

## 📦 Requisitos

- PHP 8.2+
- MySQL 5.7+
- Composer
- Navegador moderno

---

## 📄 Estructura de Archivos

```
atracciones_turisticasp/
├── app/Http/Controllers/
│   ├── AuthController.php
│   ├── AtraccionController.php
│   └── ReservaController.php
├── app/Models/
│   ├── User.php
│   ├── Atraccion.php
│   └── Reserva.php
├── frontend/
│   ├── *.html
│   └── js/
│       ├── api.js
│       ├── auth.js
│       ├── admin.js
│       └── detalle.js
├── routes/api.php
└── [configuración]
```

---

## 🧪 Testing

Ejecutar tests según [TESTING_GUIDE.md](TESTING_GUIDE.md):
- 15 tests incluidos
- Cobertura completa
- Casos de éxito y error

---

## ⚙️ Configuración

### URL del API
Editar `frontend/js/api.js`:
```javascript
const API_URL = 'http://127.0.0.1:8000/api';
```

### Base de Datos
Editar `.env`:
```
DB_DATABASE=atracciones
DB_USERNAME=root
```

### Hacer usuario admin
```sql
UPDATE users SET role = 'admin' WHERE email = 'user@example.com';
```

---

## ✅ Checklist de Implementación

### Backend
- [x] Base de datos y migraciones
- [x] Modelos con relaciones
- [x] Controllers CRUD
- [x] Autenticación
- [x] Validaciones
- [x] Middleware
- [x] Rutas API
- [x] Respuestas JSON

### Frontend
- [x] Páginas HTML
- [x] Módulos JavaScript
- [x] Comunicación API
- [x] Gestión de estado
- [x] Formularios
- [x] Interfaz responsiva
- [x] Manejo de errores
- [x] Control de acceso

---

## 🎓 Versión

**Versión:** 2.0  
**Estado:** Producción  
**Última actualización:** 9 de Enero, 2025  

---

## 📞 Soporte

Para problemas:
1. Revisar la documentación (.md files)
2. Ver console del navegador (F12)
3. Revisar Network tab
4. Ver logs: `storage/logs/`

---

## 📝 Licencia

Proyecto educativo. Libre para usar.

---

> **💡 Comienza con:** [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md) para entender la arquitectura, o [TESTING_GUIDE.md](TESTING_GUIDE.md) para probar todas las features.

