# Implementación: Validación de Perfiles de Artistas

**Fecha:** 8 de noviembre de 2025  
**Estado:** ✅ COMPLETADO  
**Objetivo:** Agregar validación de perfiles de artistas en el panel de validador para asegurar información apropiada en una entidad pública.

---

## 📋 Cambios Implementados

### 1️⃣ **Base de Datos**

#### Migración ejecutada: `database/migrar_validacion_perfiles.sql`

**Campos agregados a tabla `artistas`:**
```sql
ALTER TABLE artistas ADD COLUMN status_perfil VARCHAR(20) DEFAULT 'pendiente';
ALTER TABLE artistas ADD COLUMN motivo_rechazo TEXT NULL;
```

**Valores de `status_perfil`:**
- `'pendiente'` - Perfil en espera de validación
- `'validado'` - Perfil aprobado por validador
- `'rechazado'` - Perfil rechazado (requiere cambios)

**Tabla de logs creada:** `logs_validacion_perfiles`
```
- id (PK)
- artista_id (FK)
- validador_id (FK)
- accion (validar/rechazar)
- motivo_rechazo (TEXT)
- fecha_accion (TIMESTAMP)
```

**Índices agregados:**
- `idx_status_perfil` - Para búsquedas rápidas por estado
- `idx_status_provincia` - Para filtros combinados

---

### 2️⃣ **Backend - API Endpoints**

#### **A. GET /public/api/get_perfiles.php**
**Función:** Obtiene lista de perfiles de artistas filtrados

**Parámetros:**
```
GET ?estado=pendiente|validado|rechazado|todos
GET ?provincia=Santiago del Estero
GET ?pagina=1&limite=50
```

**Respuesta:**
```json
[
  {
    "id": 1,
    "nombre": "Juan",
    "apellido": "Pérez",
    "email": "juan@example.com",
    "provincia": "Santiago del Estero",
    "municipio": "Capital",
    "especialidades": "Música, Teatro",
    "biografia": "...",
    "foto_perfil": "/uploads/imagens/...",
    "instagram": "@juanperez",
    "facebook": "Juan Pérez",
    "twitter": "@juanperez",
    "sitio_web": "www.juanperez.com",
    "status_perfil": "pendiente",
    "motivo_rechazo": null,
    "fecha_registro": "2025-11-08 10:00:00",
    "fecha_validacion": null
  }
]
```

**Permisos:** `validador` o `admin`

---

#### **B. POST /public/api/validar_perfil.php**
**Función:** Aprueba o rechaza un perfil de artista

**Parámetros (FormData):**
```
POST /api/validar_perfil.php
- id: (int) ID del artista
- accion: "validar" o "rechazar"
- motivo: (string, solo si accion=rechazar) Motivo del rechazo
```

**Respuesta exitosa:**
```json
{
  "status": "ok",
  "message": "Perfil de artista validado exitosamente",
  "artista_id": 1,
  "nuevo_estado": "validado"
}
```

**Proceso:**
1. Verifica que sea validador o admin
2. Obtiene datos del artista
3. Actualiza `status_perfil` en BD
4. Registra en `logs_validacion_perfiles`
5. Envía email de notificación al artista
6. Retorna confirmación

**Permisos:** `validador` o `admin`

---

#### **C. POST /backend/controllers/actualizar_perfil_publico.php** (Modificado)

**Cambios:**
- Ahora establece `status_perfil = 'pendiente'` al actualizar
- El perfil queda en estado de revisión automáticamente
- Mensaje de confirmación ajustado

**Flujo nuevo:**
```
1. Artista edita perfil público
2. Se guarda con estado 'pendiente'
3. Validador ve en "Validar Perfiles de Artistas"
4. Validador aprueba o rechaza
5. Si aprueba → estado: 'validado'
6. Si rechaza → estado: 'rechazado' + motivo
```

---

### 3️⃣ **Frontend - Páginas**

#### **A. `/public/src/views/pages/shared/gestion_perfiles.php`**
**Descripción:** Página de gestión de perfiles de artistas

**Características:**
- Tabla con lista de artistas pendientes de validación
- Filtros por: nombre/email, estado, provincia
- Botones de acción: Ver, Aprobar, Rechazar
- Modal con detalles completos del perfil
- Paginación automática

**Acceso:**
- URL: `/src/views/pages/shared/gestion_perfiles.php`
- Roles: `validador`, `admin`
- Redirección: Automática si no tiene permisos

**Variables de contexto:**
```php
$userRole = $_SESSION['user_data']['role'];
// 'validador' o 'admin'
```

---

#### **B. `/public/static/js/gestionar_perfiles.js`**
**Descripción:** JavaScript que controla la gestión de perfiles

**Funciones principales:**

1. **`cargarPerfiles()`**
   - Obtiene perfiles pendientes de API
   - Llena los filtros
   - Renderiza tabla

2. **`mostrarPerfiles(perfiles)`**
   - Crea filas de tabla
   - Agrega event listeners a botones

3. **`verDetallesPerfil(perfilId)`**
   - Muestra modal SweetAlert con información completa
   - Foto de perfil
   - Redes sociales
   - Biografía
   - Botones de aprobación/rechazo en modal

4. **`aprobarPerfil(perfilId)`**
   - Confirmación con SweetAlert
   - POST a `/api/validar_perfil.php` con `accion=validar`
   - Recarga tabla al completar

5. **`mostrarModalRechazo(perfilId)` y `rechazarPerfil(perfilId, motivo)`**
   - Modal para ingresar motivo de rechazo
   - POST con `accion=rechazar`
   - Email de notificación automático

6. **`aplicarFiltros()`**
   - Filtra por: búsqueda, estado, provincia
   - Actualiza tabla en tiempo real

7. **`llenarSelectProvincias()`**
   - Llena dropdown dinámicamente con provincias disponibles

**Utilidades:**
```javascript
function escapeHtml(text)     // Escape de caracteres
function formatearFecha(fecha) // Formato de fecha
function obtenerBadgeEstado(estado) // Badge según estado
```

---

### 4️⃣ **Frontend - Panel Validador**

#### **Actualización: `/public/src/views/pages/validador/panel_validador.php`**

**Nuevo botón agregado:**
```php
<a href="<?php echo BASE_URL; ?>src/views/pages/shared/gestion_perfiles.php" 
   class="dashboard-item" 
   title="Revisar y validar los perfiles de artistas para asegurar información apropiada.">
    <i class="bi bi-person-check dashboard-icon"></i> Validar Perfiles de Artistas
</a>
```

**Orden de opciones en panel:**
1. ✅ **Validar Perfiles de Artistas** (NUEVO)
2. Obras Pendientes de Validación
3. Historial de Validaciones

---

### 5️⃣ **Email Helper - Templates**

#### **Método agregado: `EmailHelper::notificarPerfilRechazado()`**

**Template de email - Perfil Rechazado:**
```html
<h2 style='color: #dc3545;'>Revisión requerida en tu perfil</h2>
<p>Tu perfil en ID Cultural requiere algunos ajustes antes de ser aprobado.</p>
<p><strong>Motivo:</strong></p>
<p style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #dc3545;'>
  {motivo del validador}
</p>
<p>Por favor, accede a tu panel y actualiza la información según lo indicado.</p>
<a href='https://idcultural.gob.ar/src/views/pages/artista/editar_perfil_publico.php'>
  Editar mi perfil
</a>
```

**Template existente - Perfil Validado:**
```html
<h2 style='color: #28a745;'>✓ ¡Tu perfil ha sido aprobado!</h2>
<p>¡Excelente noticia! Tu perfil en ID Cultural ha sido validado y aprobado.</p>
<p>Ahora puedes publicar tus obras y ser descubierto por la comunidad cultural.</p>
```

---

## 🔄 Flujo Completo de Validación de Perfiles

```
┌─────────────────────────────────────────────────────────────┐
│                    ARTISTA                                  │
├─────────────────────────────────────────────────────────────┤
│ 1. Accede a "Editar Perfil Público"                         │
│ 2. Actualiza:                                               │
│    - Biografía                                              │
│    - Especialidades                                         │
│    - Foto de perfil                                         │
│    - Redes sociales                                         │
│ 3. Envía datos al servidor                                  │
│    POST /api/actualizar_perfil_publico.php                 │
│ 4. Sistema guarda con:                                      │
│    - status_perfil = 'pendiente'                            │
│    - Notificación: "En revisión..."                         │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                  VALIDADOR/ADMIN                            │
├─────────────────────────────────────────────────────────────┤
│ 1. Accede a Panel Validador                                │
│ 2. Hace clic en "Validar Perfiles de Artistas"             │
│ 3. Ve tabla con perfiles pendientes:                        │
│    - Nombre, email, ubicación, estado                       │
│ 4. Selecciona perfil y hace clic en "Ver"                  │
│ 5. Modal muestra:                                           │
│    - Foto de perfil                                         │
│    - Información personal                                   │
│    - Redes sociales                                         │
│    - Biografía completa                                     │
│ 6. Puede:                                                   │
│    A) Aprobar → estado: 'validado'                          │
│       Email: "Tu perfil ha sido aprobado"                   │
│    B) Rechazar → estado: 'rechazado'                        │
│       + motivo → Email: "Requiere ajustes: {motivo}"        │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│              SISTEMA - NOTIFICACIONES                       │
├─────────────────────────────────────────────────────────────┤
│ ✉️ Email automático enviado al artista                     │
│ 📝 Registro en logs_validacion_perfiles                     │
│ 🔄 Tabla se actualiza en tiempo real                        │
│ 📊 Estadísticas del panel se recalculan                     │
└─────────────────────────────────────────────────────────────┘
```

---

## 📊 Estado de los Perfiles

| Estado | Descripción | Puede Editar | Visible en Wiki |
|--------|-------------|--------------|-----------------|
| `pendiente` | Esperando validación | ✅ Sí | ❌ No |
| `validado` | Aprobado por validador | ✅ Sí (vuelve a pendiente) | ✅ Sí |
| `rechazado` | Requiere cambios | ✅ Sí (vuelve a pendiente) | ❌ No |

---

## 🔐 Seguridad

**Validaciones implementadas:**
- ✅ Verificación de rol (solo validador/admin)
- ✅ Validación de ID de artista
- ✅ Escape de HTML en salida
- ✅ Transacciones de BD para integridad
- ✅ Logs de auditoría de validaciones

**Permisos por rol:**
```php
// Artista
- Ver su propio perfil
- Actualizar perfil (genera estado pendiente)

// Validador
- Ver todos los perfiles pendientes
- Aprobar o rechazar perfiles
- Ver histórico de validaciones
- Acceder a logs de validación

// Admin
- Todas las acciones de validador
- Acceso a panel de administración
```

---

## 📝 Tabla de Resumen de Archivos

| Archivo | Tipo | Acción |
|---------|------|--------|
| `database/migrar_validacion_perfiles.sql` | SQL | Crear campos y tabla |
| `/public/api/get_perfiles.php` | PHP API | Obtener perfiles |
| `/public/api/validar_perfil.php` | PHP API | Validar/Rechazar |
| `/public/src/views/pages/shared/gestion_perfiles.php` | PHP View | UI de gestión |
| `/public/static/js/gestionar_perfiles.js` | JavaScript | Lógica del frontend |
| `/public/src/views/pages/validador/panel_validador.php` | PHP View | Nuevo botón en panel |
| `backend/helpers/EmailHelper.php` | PHP Helper | Nuevo método de email |
| `backend/controllers/actualizar_perfil_publico.php` | PHP Controller | Cambio a pendiente |

---

## ✅ Checklist de Validación

- ✅ Campos agregados a BD (status_perfil, motivo_rechazo)
- ✅ Tabla de logs creada
- ✅ API GET para obtener perfiles implementada
- ✅ API POST para validar perfiles implementada
- ✅ Página de gestión de perfiles creada
- ✅ JavaScript de gestión completado
- ✅ Panel de validador actualizado con botón nuevo
- ✅ EmailHelper actualizado con método nuevo
- ✅ Controlador de actualización adaptado
- ✅ Templates de email agregados
- ✅ Filtros funcionando (búsqueda, estado, provincia)
- ✅ Modales SweetAlert implementados
- ✅ Transacciones de BD implementadas
- ✅ Logs de auditoría implementados
- ✅ Escape de HTML implementado

---

## 🚀 Próximos Pasos Opcionales

1. **Dashboard de estadísticas de validación**
   - Gráficos de perfiles por estado
   - Tiempo promedio de validación
   - Validadores más activos

2. **Notificaciones en tiempo real**
   - WebSockets para actualizaciones live
   - Push notifications al validador

3. **Reporte de validaciones**
   - Exportar a CSV/PDF
   - Filtros avanzados en log

4. **Automatización**
   - Recordatorios automáticos
   - Validación automática de campos requeridos
   - Análisis de contenido inapropiado

---

## 📞 Soporte

Para consultas o problemas con la implementación:
- Ver logs en: `/logs/` (si están configurados)
- Verificar BD: `SELECT * FROM logs_validacion_perfiles;`
- Check API: `GET /api/get_perfiles.php?estado=pendiente`

---

**Última actualización:** 8 de noviembre de 2025  
**Versión:** 1.0  
**Estado:** ✅ PRODUCCIÓN
