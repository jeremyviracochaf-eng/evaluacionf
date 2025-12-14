# Guía de Pruebas - Atracciones Turísticas

## Pre-requisitos para Pruebas

1. ✅ Laravel corriendo en `http://127.0.0.1:8000`
2. ✅ Base de datos migrada y seedeada
3. ✅ Frontend en carpeta `frontend/`
4. ✅ CORS configurado en Laravel

## Pruebas de Flujo Completo

### Test 1: Registro de Usuario

**Pasos:**
1. Abrir `frontend/register.html`
2. Llenar formulario:
   - Nombre: `Juan Prueba`
   - Email: `juan@example.com`
   - Contraseña: `password123`
   - Confirmar: `password123`
3. Hacer click en "Registrarse"

**Resultado Esperado:**
- ✅ Mensaje verde "Cuenta creada exitosamente"
- ✅ Redirección a `index.html` después de 1.5 segundos
- ✅ Header muestra "Hola, Juan Prueba"
- ✅ Botón "Login" desaparece
- ✅ Botones "Mis Reservas" y "Panel Admin" aparecen (aunque sin acceso a admin)

---

### Test 2: Login

**Pasos:**
1. Abrir `frontend/login.html`
2. Llenar formulario:
   - Email: `juan@example.com` (del test anterior)
   - Contraseña: `password123`
3. Hacer click en "Ingresar"

**Resultado Esperado:**
- ✅ Redirección a `index.html`
- ✅ Header muestra "Hola, Juan Prueba"
- ✅ Sesión mantiene el usuario activo

---

### Test 3: Ver Catálogo de Atracciones

**Pasos:**
1. En `index.html`, revisar grid de atracciones

**Resultado Esperado:**
- ✅ Se muestran atracciones en grid responsivo
- ✅ Cada atracción muestra:
  - Imagen (si existe)
  - Nombre
  - Ubicación
  - Descripción (truncada)
  - Precio
  - Botón "Ver detalle"

---

### Test 4: Ver Detalle y Hacer Reserva

**Pasos:**
1. Hacer click en "Ver detalle" de una atracción
2. Verificar que se carga el detalle
3. Completar formulario de reserva:
   - Fecha: Seleccionar fecha futura
   - Hora: Seleccionar hora (ej: 10:00)
   - Comentarios: "Grupo de 4 personas" (opcional)
4. Hacer click en "Reservar"

**Resultado Esperado:**
- ✅ Se muestra el detalle completo de la atracción
- ✅ Si está autenticado, aparece formulario de reserva
- ✅ Mensaje verde "Reserva creada exitosamente"
- ✅ Redirección a `reservas.html` después de 2 segundos

---

### Test 5: Error de Doble Reserva

**Pasos:**
1. Hacer otra reserva para la MISMA atracción, MISMA fecha, MISMA hora
2. Intentar confirmar

**Resultado Esperado:**
- ✅ Error rojo: "Esta atracción ya tiene una reserva aceptada en esa fecha y hora"
- ✅ Permite seleccionar otra hora/fecha

---

### Test 6: Ver Mis Reservas

**Pasos:**
1. Hacer click en "Mis Reservas"
2. Revisar lista de reservas

**Resultado Esperado:**
- ✅ Se muestran todas las reservas del usuario
- ✅ Cada reserva muestra:
  - Nombre de atracción
  - Ubicación
  - Fecha (formato local)
  - Hora
  - Estado (color-codificado)
  - Comentarios (si existen)
  - Botón "Cancelar Reserva"

---

### Test 7: Cancelar Reserva

**Pasos:**
1. En `reservas.html`, hacer click en "Cancelar Reserva"
2. Confirmar en el diálogo

**Resultado Esperado:**
- ✅ Alerta de confirmación
- ✅ Después de confirmar: alerta "Reserva cancelada"
- ✅ Lista se actualiza automáticamente
- ✅ Reserva desaparece de la lista

---

### Test 8: Login como Admin

**Pasos:**
1. En base de datos, actualizar usuario para que sea admin:
   ```sql
   UPDATE users SET role = 'admin' WHERE email = 'juan@example.com';
   ```
2. Hacer logout desde cualquier página
3. Login nuevamente con `juan@example.com` / `password123`

**Resultado Esperado:**
- ✅ Redirección automática a `admin.html` (no a `index.html`)
- ✅ Botón "Panel Admin" aparece en header

---

### Test 9: Crear Nueva Atracción (Admin)

**Pasos:**
1. En `admin.html`, llenar formulario "Crear Nueva Atracción":
   - Nombre: `Volcán Pichincha`
   - Categoría: `Naturaleza`
   - Ubicación: `Quito, Ecuador`
   - Precio: `50.00`
   - Descripción: `Hermoso volcán con vista panorámica de la ciudad`
   - URL imagen: (dejar vacío)
2. Hacer click en "Crear Atracción"

**Resultado Esperado:**
- ✅ Mensaje verde "✓ Atracción creada exitosamente"
- ✅ Grid se actualiza automáticamente
- ✅ Nueva atracción aparece en la lista
- ✅ Nueva atracción aparece también en `index.html` para usuarios

---

### Test 10: Editar Atracción (Admin)

**Pasos:**
1. En grid de atracciones, hacer click en "Editar" de una atracción
2. Cambiar algunos campos (ej: nombre, precio)
3. Hacer click en "Guardar"

**Resultado Esperado:**
- ✅ Modal se abre con datos de la atracción
- ✅ Campos se pueden modificar
- ✅ Mensaje verde "✓ Atracción actualizada exitosamente"
- ✅ Grid se actualiza con nuevos datos
- ✅ Cambios se ven inmediatamente en index.html

---

### Test 11: Eliminar Atracción (Admin)

**Pasos:**
1. En grid de atracciones, hacer click en "Eliminar" de una atracción
2. Confirmar en el diálogo

**Resultado Esperado:**
- ✅ Diálogo de confirmación
- ✅ Alerta "Atracción eliminada"
- ✅ Grid se actualiza automáticamente
- ✅ Atracción desaparece de lista

---

### Test 12: Gestionar Reservas (Admin)

**Pasos:**
1. En header de `admin.html`, hacer click en "Gestionar Reservas"
2. Revisar lista de todas las reservas

**Resultado Esperado:**
- ✅ Se muestra lista de TODAS las reservas del sistema (no solo propias)
- ✅ Cada reserva muestra:
  - Nombre de atracción y ubicación
  - Nombre de usuario y email
  - Fecha y hora de la reserva
  - Comentarios (si existen)
  - Dropdown para cambiar estado

---

### Test 13: Cambiar Estado de Reserva (Admin)

**Pasos:**
1. En `reservas-admin.html`, seleccionar una reserva con estado "Pendiente"
2. Cambiar estado a "Aceptada" desde el dropdown
3. Verificar que cambia

**Resultado Esperado:**
- ✅ Dropdown permite seleccionar: Pendiente, Aceptada, Rechazada
- ✅ Al cambiar, se envía a API
- ✅ Estado se actualiza inmediatamente en la interfaz
- ✅ Color del estado cambia (verde para aceptada)

---

### Test 14: Filtrar Reservas por Estado (Admin)

**Pasos:**
1. En `reservas-admin.html`, seleccionar filtro "Aceptada"
2. Hacer click en "Filtrar"

**Resultado Esperado:**
- ✅ Se muestran solo reservas con estado "Aceptada"
- ✅ Seleccionar "Todos los estados" vuelve a mostrar todas

---

### Test 15: Logout

**Pasos:**
1. Desde cualquier página autenticada, hacer click en "Cerrar sesión"

**Resultado Esperado:**
- ✅ Redirección a `login.html`
- ✅ localStorage limpio
- ✅ Intento de acceder a páginas protegidas (ej: `reservas.html`) redirige a login

---

## Pruebas de Errores

### Error: Usuario no autenticado intenta ver reservas

**Pasos:**
1. Limpiar localStorage o abrir en navegador privado
2. Ir directamente a `reservas.html`

**Resultado Esperado:**
- ✅ Redirección a `login.html`

---

### Error: Usuario no autenticado intenta ver detalle

**Pasos:**
1. Limpiar localStorage o abrir en navegador privado
2. Ir a `detalle.html?id=1`

**Resultado Esperado:**
- ✅ Se muestra detalle de atracción
- ✅ Formulario de reserva NO aparece
- ✅ Se muestra alerta amarilla "Debes iniciar sesión para hacer una reserva"
- ✅ Botón para ir a login.html

---

### Error: Usuario no admin intenta acceder a admin.html

**Pasos:**
1. Hacer login con usuario normal
2. Intentar ir a `admin.html` directamente (cambiar URL)

**Resultado Esperado:**
- ✅ Redirección a `login.html`
- ✅ Header de admin NO muestra link a Panel Admin

---

### Error: Intentar registrar con email existente

**Pasos:**
1. Ir a `register.html`
2. Ingresar email que ya existe: `juan@example.com`
3. Completar otros campos y enviar

**Resultado Esperado:**
- ✅ Error rojo: "El correo ya ha sido registrado"

---

### Error: Intentar hacer login con contraseña incorrecta

**Pasos:**
1. Ir a `login.html`
2. Email correcto: `juan@example.com`
3. Contraseña incorrecta: `wrongpassword`

**Resultado Esperado:**
- ✅ Error rojo: "Credenciales inválidas."

---

## Resumen de Estados Esperados

| Página | No Autenticado | Usuario Normal | Admin |
|--------|---|---|---|
| index.html | ✅ Ver atracciones | ✅ Ver + botones | ✅ Ver + botones + Admin |
| login.html | ✅ Acceso | ❌ Redirige a index | ❌ Redirige a admin |
| register.html | ✅ Acceso | ❌ Redirige a index | ❌ Redirige a admin |
| detalle.html | ✅ Ver sin formulario | ✅ Ver con formulario | ✅ Ver con formulario |
| reservas.html | ❌ Redirige a login | ✅ Ver propias | ✅ Ver propias |
| admin.html | ❌ Redirige a login | ❌ Redirige a login | ✅ Acceso |
| reservas-admin.html | ❌ Redirige a login | ❌ Redirige a login | ✅ Acceso |

## Conclusión

Si todos estos tests pasan exitosamente, su aplicación está funcionando correctamente. Si alguno falla, revise:

1. Consola del navegador (F12 → Console) para mensajes de error
2. Network tab para ver requests fallidos al API
3. Que el API esté corriendo en `http://127.0.0.1:8000`
4. Que la base de datos tiene datos
5. Que CORS está configurado en Laravel
