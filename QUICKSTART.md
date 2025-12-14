# ⚡ QUICKSTART - Guía Rápida

## En 5 Minutos

### 1. Preparar
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### 2. Ejecutar
```bash
php artisan serve
```

### 3. Acceder
```
Frontend: http://127.0.0.1:8000/frontend/index.html
API: http://127.0.0.1:8000/api
```

---

## Acciones Básicas

### Como Usuario
1. Ir a `register.html` → Registrarse
2. Ir a `index.html` → Ver atracciones
3. Click en "Ver detalle" → Hacer reserva
4. Ir a `reservas.html` → Ver mis reservas

### Como Admin
1. En BD: `UPDATE users SET role = 'admin' WHERE id = 1;`
2. Login → Automáticamente a `admin.html`
3. Crear/Editar/Eliminar atracciones
4. Click "Gestionar Reservas" → Cambiar estados

---

## Archivos Importantes

| Archivo | Qué Es |
|---------|--------|
| `routes/api.php` | Endpoints API |
| `frontend/js/api.js` | Comunicación API |
| `frontend/js/auth.js` | Manejo de sesión |
| `app/Http/Controllers/` | Lógica del backend |
| `app/Models/` | Estructuras de datos |

---

## URLs Clave

```
Frontend:
/frontend/index.html - Catálogo
/frontend/login.html - Login
/frontend/admin.html - Admin

API:
http://127.0.0.1:8000/api/atracciones
http://127.0.0.1:8000/api/reservas
http://127.0.0.1:8000/api/auth/login
```

---

## Próximos Pasos

- [ ] Lee [README.md](README.md) para resumen
- [ ] Lee [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md) para arquitectura
- [ ] Lee [TESTING_GUIDE.md](TESTING_GUIDE.md) para probar
- [ ] Lee [FRONTEND_README.md](FRONTEND_README.md) para frontend

---

## Problemas?

1. ¿No inicia? → Revisar PHP 8.2+, MySQL corriendo
2. ¿Error en API? → Ver `storage/logs/laravel.log`
3. ¿Frontend no carga? → Ver console (F12)
4. ¿Revisar más?→ Ver `TESTING_GUIDE.md`

---

**Tiempo estimado de setup:** 5 minutos  
**Todas las features:** ✅ Implementadas y funcionales
