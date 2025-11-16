# Validación de Perfiles de Artistas - Arquitectura Completa

## 📍 Ubicación de Validación

Los cambios en los perfiles de artistas se validan en **dos niveles**:

### 1️⃣ **NIVEL 1: Actualización Inmediata (Sin Validación)**

Los artistas pueden actualizar su perfil básico directamente sin aprobación de validadores:

**Endpoint:**
- `/public/api/actualizar_perfil_artista.php` → `/backend/controllers/actualizar_perfil_artista.php`

**Qué se actualiza sin validación:**
```
- nombre
- apellido  
- fecha_nacimiento
- genero
- pais
- provincia
- municipio
```

**Flujo:**
```
1. Artista envía datos JSON al endpoint
2. Controlador valida campos requeridos
3. UPDATE directo en tabla `artistas`
4. Sesión se actualiza con nuevos datos
5. Respuesta JSON exitosa
```

**Archivo:** `/backend/controllers/actualizar_perfil_artista.php` (línea 28-71)

---

### 2️⃣ **NIVEL 2: Perfil Público (Requiere Validación)**

Los datos del perfil público sí requieren validación por admin/validador:

**Endpoint:**
- `/public/api/actualizar_perfil_publico.php` → `/backend/controllers/actualizar_perfil_publico.php`

**Qué se actualiza (perfil público):**
```
- biografia
- especialidades
- instagram
- facebook
- twitter
- sitio_web
- foto_perfil (multimedia)
```

**Flujo:**
```
1. Artista envía FormData con datos y foto
2. MultimediaValidator procesa imagen
3. UPDATE directo en tabla `artistas`
4. Se guarda en /public/uploads/imagens/
5. Respuesta JSON exitosa
```

⚠️ **NOTA:** Actualmente se actualiza directamente sin validación. La aprobación es solo para **obras/publicaciones**, no para perfiles.

**Archivo:** `/backend/controllers/actualizar_perfil_publico.php` (línea 42-113)

---

## 🔍 **¿DÓNDE SE VALIDAN LAS OBRAS?**

La validación de **obras** (que es lo diferente de perfiles) se hace en:

### **Panel de Validador - Gestión de Obras Pendientes**

**Ubicación Frontend:**
- `/public/src/views/pages/shared/gestion_artistas_obras.php`
- Se redirige desde `/public/src/views/pages/validador/gestion_pendientes.php`

**JavaScript que controla validación:**
- `/public/static/js/gestion_pendientes.js`

**API que recibe obras pendientes:**
- `/public/api/get_publicaciones.php?estado=pendiente`

**API que aprueba/rechaza obras:**
- `/public/api/validar_publicacion.php`

---

## 🔐 **Panel de Validador - Estructura**

### **1. Panel Principal** 
- Ubicación: `/public/src/views/pages/validador/panel_validador.php`
- Rol requerido: `validador` o `admin`
- Muestra estadísticas de artistas pendientes, validados y rechazados

### **2. Gestión de Obras**
- Ubicación: `/public/src/views/pages/shared/gestion_artistas_obras.php`
- Rol requerido: `validador` o `admin`
- **Nota:** Gestiona **obras**, no perfiles de artistas

### **3. Acciones en el Panel**

Los validadores pueden:
- ✅ **Ver detalles** de obras (imagen, descripción, categoría)
- ✅ **Aprobar obras** → Cambia estado a `'validado'`
- ✅ **Rechazar obras** → Requiere motivo, cambia estado a `'rechazado'`
- ✅ **Filtrar** por: nombre artista, título, categoría, municipio

---

## 🗄️ **Validación en Base de Datos**

### **Tabla: `artistas`**
```sql
- id (PK)
- nombre
- apellido
- fecha_nacimiento
- genero
- pais
- provincia
- municipio
- biografia
- especialidades
- instagram, facebook, twitter
- sitio_web
- foto_perfil
- status (validado/pendiente/rechazado)
- fecha_validacion
- validador_id
```

### **Tabla: `publicaciones` (Obras)**
```sql
- id (PK)
- titulo
- categoria
- descripcion
- multimedia
- estado (borrador/pendiente_validacion/validado/rechazado)
- usuario_id (artista)
- validador_id (quien valida)
- fecha_envio_validacion
- fecha_validacion
```

---

## ✅ **Validación Actual de Perfiles**

### **¿Qué SÍ se valida?**
✅ Obras/Publicaciones (a través del panel de validador)

### **¿Qué NO se valida?**
❌ Perfil básico del artista (nombre, apellido, ubicación)
❌ Perfil público (biografía, redes sociales, foto)

**Ambos se actualizan directamente sin aprobación de validadores.**

---

## 🔄 **Flujo Completo de Validación**

```
┌─────────────────────────────────────────────────────────────┐
│                  ARTISTA                                    │
├─────────────────────────────────────────────────────────────┤
│ 1. Edita perfil básico                                      │
│    → POST /api/actualizar_perfil_artista.php                │
│    → Actualización inmediata sin validación                 │
│                                                             │
│ 2. Edita perfil público                                     │
│    → POST /api/actualizar_perfil_publico.php                │
│    → Actualización inmediata sin validación                 │
│                                                             │
│ 3. Crea obra y envía a validación                          │
│    → POST /api/borradores.php (caso: save)                 │
│    → Estado cambia a 'pendiente_validacion'                 │
└──────────────────┬──────────────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────────────┐
│              VALIDADOR/ADMIN                                │
├─────────────────────────────────────────────────────────────┤
│ 1. Accede a panel: /validador/panel_validador.php          │
│                                                             │
│ 2. Ve obras pendientes: /shared/gestion_artistas_obras.php │
│    Carga: GET /api/get_publicaciones.php?estado=pendiente   │
│                                                             │
│ 3. Ve detalles de obra                                      │
│    GET /api/get_publicacion_detalle.php?id=X              │
│                                                             │
│ 4. Aprueba o rechaza                                        │
│    POST /api/validar_publicacion.php                        │
│    - accion: 'validar' o 'rechazar'                        │
│    - Actualiza estado a 'validado' o 'rechazado'           │
│    - Registra validador_id y fecha_validacion              │
│    - Envía email de notificación                           │
└──────────────────┬──────────────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────────────┐
│                   WIKI PÚBLICA                              │
├─────────────────────────────────────────────────────────────┤
│ Solo muestra obras con estado='validado'                    │
│ GET /api/get_obras_wiki.php                                │
│ WHERE estado = 'validado'                                  │
└─────────────────────────────────────────────────────────────┘
```

---

## 📱 **Endpoints Clave**

### **Actualización de Perfil (Artista)**
```
POST /api/actualizar_perfil_artista.php
Headers: Content-Type: application/json
Body: {
  "nombre": "Juan",
  "apellido": "Pérez",
  "fecha_nacimiento": "1990-01-15",
  "genero": "M",
  "pais": "Argentina",
  "provincia": "Santiago del Estero",
  "municipio": "Capital"
}
Response: { success: true, mensaje: "Perfil actualizado correctamente" }
```

### **Actualización de Perfil Público (Artista)**
```
POST /api/actualizar_perfil_publico.php
Headers: Content-Type: multipart/form-data
Body: FormData {
  "biografia": "Artista local...",
  "especialidades": "Música, teatro",
  "instagram": "@miinstagram",
  "facebook": "Mi Facebook",
  "twitter": "@twitter",
  "sitio_web": "www.misitio.com",
  "foto_perfil": <File>
}
Response: { success: true, mensaje: "Tu perfil público ha sido actualizado correctamente." }
```

### **Validar Obra (Validador)**
```
POST /api/validar_publicacion.php
Headers: Content-Type: multipart/form-data
Body: FormData {
  "id": 123,
  "accion": "validar" // o "rechazar"
  "motivo": "..." // solo si accion="rechazar"
}
Response: { status: 'ok', message: "Obra validada exitosamente", ... }
```

---

## 🛡️ **Permisos de Acceso**

### **Actualizar Perfil (Artista)**
```php
Rol requerido: 'artista'
Verificación: $_SESSION['user_data']['role'] === 'artista'
Solo puede actualizar su propio perfil (usuario_id = $_SESSION['user_data']['id'])
```

### **Validar Obras (Panel Validador)**
```php
Rol requerido: 'validador' o 'admin'
Verificación: in_array($_SESSION['user_data']['role'], ['validador', 'admin'])
Puede validar cualquier obra pendiente
```

### **Aprobar Perfil (Deprecated)**
```
Archivo: /backend/controllers/aprobar_perfil.php
Rol requerido: 'validador' o 'admin'
Nota: Este archivo existe pero no está siendo usado actualmente.
      La aprobación de perfiles no está implementada.
```

---

## 📋 **Resumen de Validaciones**

| Elemento | Validación | Aprobación | API |
|----------|-----------|-----------|-----|
| **Perfil Básico** | Campos requeridos | ❌ No | `actualizar_perfil_artista.php` |
| **Perfil Público** | Multimedia | ❌ No | `actualizar_perfil_publico.php` |
| **Obras/Publicaciones** | Estado pendiente | ✅ Sí | `validar_publicacion.php` |
| **Foto Perfil** | MultimediaValidator | ❌ No | Dentro de perfil público |

---

## 🔧 **Si Quieres Agregar Validación de Perfiles:**

Para que los perfiles de artistas también requieran validación (como las obras):

1. **Agregar campo en tabla `artistas`:**
   ```sql
   ALTER TABLE artistas ADD COLUMN status_perfil VARCHAR(20) DEFAULT 'pendiente';
   ```

2. **Crear nuevo endpoint:** `/api/validar_perfil.php`

3. **Agregar sección en panel validador** para aprobar perfiles

4. **Modificar actualización de perfil** para guardar en estado 'pendiente' en lugar de actualizar directo

---

**Última actualización:** 8 de noviembre de 2025
