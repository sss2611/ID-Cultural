# 📚 **Documentación de APIs - ID Cultural**

**Última actualización:** 4 de Noviembre de 2025  
**Versión:** 2.0 (Refactorizada con CRUDs Unificados)

---

## 🎯 **Resumen de Cambios**

✅ **CRUDs Unificados:** Se han consolidado múltiples endpoints en 6 CRUDs principales  
✅ **APIs Limpias:** Eliminadas 20+ APIs redundantes  
✅ **Bugs Corregidos:** Corregidas referencias incorrectas a tablas de base de datos  
❌ **Carpeta `unificado/`:** Puede ser eliminada (archivos ya copiados a `/api/`)

---

## 📋 **APIs Activas Actuales**

### 1. **CRUD de Artistas** - `artistas.php`

**Autenticación:** Público (registro), Admin/Validador (gestión)

#### Acciones disponibles:

**a) Obtener artistas**
```bash
# Obtener todos los artistas
GET /api/artistas.php?action=get

# Obtener artistas por estado (pendiente, validado, rechazado)
GET /api/artistas.php?action=get&status=validado

# Obtener artista específico
GET /api/artistas.php?action=get&id=1
```

**b) Registrar artista (público)**
```bash
POST /api/artistas.php
# action=register
# nombre, apellido, fecha_nacimiento, genero, pais, provincia, municipio, email, password, confirm_password, intereses[]
```

**c) Actualizar estado de artista (admin/validador)**
```bash
POST /api/artistas.php
# action=update_status
# id, status (validado/rechazado), motivo
```

**d) Eliminar artista (admin)**
```bash
POST /api/artistas.php
# action=delete
# id
```

**e) Obtener estadísticas (admin/validador)**
```bash
GET /api/artistas.php?action=get_stats
```

---

### 2. **CRUD de Personal** - `personal.php`

**Autenticación:** Admin solamente

#### Acciones disponibles:

**a) Obtener personal**
```bash
# Obtener todo el personal
GET /api/personal.php?action=get

# Obtener usuario específico
GET /api/personal.php?action=get&id=1
```

**b) Agregar personal**
```bash
POST /api/personal.php
# action=add
# nombre, email, rol (admin/editor/validador), password (mín. 8 caracteres)
```

**c) Actualizar personal**
```bash
POST /api/personal.php
# action=update
# id, nombre, email, role, password (opcional)
```

**d) Eliminar personal**
```bash
POST /api/personal.php
# action=delete
# id
```

---

### 3. **CRUD de Borradores/Publicaciones** - `borradores.php`

**Autenticación:** Artista solamente

#### Acciones disponibles:

**a) Obtener borradores**
```bash
# Obtener todos mis borradores
GET /api/borradores.php?action=get

# Obtener borrador específico
GET /api/borradores.php?action=get&id=1
```

**b) Guardar borrador (crear o actualizar)**
```bash
POST /api/borradores.php
# action=save
# id (opcional, para actualizar), titulo, descripcion, categoria, estado (borrador/pendiente_validacion)
# campos_extra: cualquier otro campo que se envíe se guarda como JSON
```

**c) Enviar a validación**
```bash
POST /api/borradores.php
# action=save
# ... (mismo que guardar, pero con estado=pendiente_validacion)
```

**d) Eliminar borrador**
```bash
POST /api/borradores.php
# action=delete
# id
```

---

### 4. **CRUD de Solicitudes** - `solicitudes.php`

**Autenticación:** Artista (ver propias) / Validador-Admin (gestionar todas)

#### Acciones disponibles:

**a) Obtener solicitudes del artista (artista)**
```bash
# Obtener mis solicitudes
GET /api/solicitudes.php?action=get_my

# Obtener solicitud específica
GET /api/solicitudes.php?action=get_my&id=1
```

**b) Obtener todas las solicitudes (validador/admin)**
```bash
# Obtener todas las solicitudes pendientes
GET /api/solicitudes.php?action=get_all

# Obtener solicitudes de un estado específico
GET /api/solicitudes.php?action=get_all&estado=validado

# Obtener todas (borrador, pendiente, validado, rechazado)
GET /api/solicitudes.php?action=get_all&estado=all

# Obtener solicitud específica
GET /api/solicitudes.php?action=get_all&id=1
```

**c) Actualizar estado de solicitud (validador/admin)**
```bash
POST /api/solicitudes.php
# action=update
# id, estado (validado/rechazado), motivo
```

---

### 5. **CRUD de Noticias** - `noticias.php`

**Autenticación:** Público (leer) / Editor-Admin (crear/editar/eliminar)

#### Acciones disponibles:

**a) Obtener noticias**
```bash
# Obtener todas las noticias
GET /api/noticias.php?action=get

# Obtener noticia específica
GET /api/noticias.php?action=get&id=1
```

**b) Crear noticia (editor/admin)**
```bash
POST /api/noticias.php
# action=add
# titulo, contenido, imagen (opcional)
```

**c) Actualizar noticia (editor/admin)**
```bash
POST /api/noticias.php
# action=update
# id, titulo, contenido, imagen (opcional)
```

**d) Eliminar noticia (editor/admin)**
```bash
POST /api/noticias.php
# action=delete
# id
```

---

### 6. **CRUD de Contenido del Sitio** - `site_content.php`

**Autenticación:** Editor-Admin solamente

#### Acciones disponibles:

**a) Obtener contenido**
```bash
# Obtener todo el contenido
GET /api/site_content.php?action=get

# Obtener contenido específico por clave
GET /api/site_content.php?action=get&key=welcome_title
```

**b) Actualizar contenido (editor/admin)**
```bash
POST /api/site_content.php
# action=update
# welcome_title, welcome_paragraph, welcome_slogan, carousel_image_1, carousel_image_2, carousel_image_3
# etc... (cualquier campo de site_content)
```

---

## 🔐 **APIs de Autenticación y Utilidad**

### **Login** - `login.php`

```bash
POST /api/login.php
# email, password
```

**Respuesta exitosa:**
```json
{
  "status": "ok",
  "user_data": {
    "id": 1,
    "role": "admin"
  }
}
```

---

### **Estadísticas Inicio** - `get_estadisticas_inicio.php`

```bash
GET /api/get_estadisticas_inicio.php
```

**Respuesta:**
```json
{
  "status": "ok",
  "artistas": 5,
  "obras": 12,
  "noticias": 3
}
```

---

### **Estadísticas Validador** - `get_estadisticas_validador.php`

```bash
GET /api/get_estadisticas_validador.php
```

**Respuesta:**
```json
{
  "pendientes": 2,
  "validados": 10,
  "rechazados": 1,
  "borradores": 5,
  "total_artistas_validados": 3
}
```

---

### **Logs del Sistema** - `get_logs.php`

```bash
GET /api/get_logs.php
```

---

### **Obtener Publicaciones** - `get_publicaciones.php`

```bash
GET /api/get_publicaciones.php?estado=validado
GET /api/get_publicaciones.php?estado=pendiente&categoria=musica
GET /api/get_publicaciones.php?estado=validado&municipio=Santiago+Capital
```

---

### **Obtener Detalle de Publicación** - `get_publicacion_detalle.php`

```bash
GET /api/get_publicacion_detalle.php?id=1
```

---

### **Validar Publicación** - `validar_publicacion.php`

```bash
POST /api/validar_publicacion.php
# id, accion (validar/rechazar), motivo (requerido si es rechazo)
```

---

## 🚀 **Actualización de Archivos JavaScript**

Los archivos JavaScript deben actualizar sus llamadas a las APIs antiguas. Ejemplos:

### ❌ **Antiguo**
```javascript
fetch(`${BASE_URL}api/get_mis_borradores.php`)
fetch(`${BASE_URL}api/get_noticias.php`)
fetch(`${BASE_URL}api/add_noticia.php`, { method: 'POST', body: formData })
```

### ✅ **Nuevo**
```javascript
fetch(`${BASE_URL}api/borradores.php?action=get`)
fetch(`${BASE_URL}api/noticias.php?action=get`)
formData.append('action', 'add');
fetch(`${BASE_URL}api/noticias.php`, { method: 'POST', body: formData })
```

---

## 📊 **Matriz de Acciones por Role**

| Acción | Admin | Editor | Validador | Artista |
|--------|-------|--------|-----------|---------|
| Gestionar artistas | ✅ | ❌ | ✅ (solo lectura) | ❌ |
| Gestionar personal | ✅ | ❌ | ❌ | ❌ |
| Crear/editar noticias | ✅ | ✅ | ❌ | ❌ |
| Crear/editar borradores | ❌ | ❌ | ❌ | ✅ |
| Validar publicaciones | ✅ | ❌ | ✅ | ❌ |
| Acceder a estadísticas | ✅ | ❌ | ✅ | ❌ |
| Editar contenido sitio | ✅ | ✅ | ❌ | ❌ |

---

## 🔄 **Flujo de Validación de Obras**

```
1. Artista crea borrador
   POST /api/borradores.php?action=save (estado=borrador)

2. Artista envía a validación
   POST /api/borradores.php?action=save (estado=pendiente_validacion)
   → Automáticamente artista pasa a status=pendiente en tabla artistas

3. Validador ve solicitud
   GET /api/solicitudes.php?action=get_all (estado=pendiente)

4. Validador valida obra
   POST /api/solicitudes.php?action=update (estado=validado)
   → Automáticamente artista pasa a status=validado
   → Obra aparece en Wiki de Artistas

5. Obra visible en Wiki
   GET /api/get_publicaciones.php?estado=validado
```

---

## 🗑️ **APIs Eliminadas (No usar)**

Las siguientes APIs han sido consolidadas en los CRUDs unificados y **NO DEBEN USARSE**:

- ❌ `add_personal.php` → Usar `personal.php?action=add`
- ❌ `delete_personal.php` → Usar `personal.php?action=delete`
- ❌ `update_personal.php` → Usar `personal.php?action=update`
- ❌ `get_personal.php` → Usar `personal.php?action=get`
- ❌ `delete_artista.php` → Usar `artistas.php?action=delete`
- ❌ `update_artista_status.php` → Usar `artistas.php?action=update_status`
- ❌ `get_artistas.php` → Usar `artistas.php?action=get`
- ❌ `get_artist_stats.php` → Usar `artistas.php?action=get_stats`
- ❌ `add_noticia.php` → Usar `noticias.php?action=add`
- ❌ `edit_noticia.php` → Usar `noticias.php?action=update`
- ❌ `delete_noticia.php` → Usar `noticias.php?action=delete`
- ❌ `get_noticias.php` → Usar `noticias.php?action=get`
- ❌ `get_noticia_detalle.php` → Usar `noticias.php?action=get`
- ❌ `get_mis_borradores.php` → Usar `borradores.php?action=get`
- ❌ `get_mis_solicitudes.php` → Usar `solicitudes.php?action=get_my`
- ❌ `save_borrador.php` → Usar `borradores.php?action=save`
- ❌ `delete_publicacion.php` → Usar `borradores.php?action=delete`
- ❌ `get_solicitudes.php` → Usar `solicitudes.php?action=get_all`
- ❌ `update_solicitud.php` → Usar `solicitudes.php?action=update`
- ❌ `get_site_content.php` → Usar `site_content.php?action=get`
- ❌ `update_site_content.php` → Usar `site_content.php?action=update`
- ❌ `register_artista.php` → Usar `artistas.php?action=register`

---

## ✅ **APIs Funcionales Mantidas**

Las siguientes APIs se mantienen tal como están (actualizadas/corregidas):

- ✅ `login.php` - Autenticación
- ✅ `get_estadisticas_inicio.php` - Estadísticas de homepage
- ✅ `get_estadisticas_validador.php` - Estadísticas del validador
- ✅ `get_logs.php` - Logs del sistema
- ✅ `get_publicaciones.php` - Listar publicaciones (CORREGIDA)
- ✅ `get_publicacion_detalle.php` - Detalle de publicación (CORREGIDA)
- ✅ `validar_publicacion.php` - Validar publicación (CORREGIDA)

---

## 🐛 **Bugs Corregidos**

### 1. **Referencias a tabla incorrecta `usuarios`**
   - ❌ Antes: Buscaba `JOIN usuarios` (no existe)
   - ✅ Ahora: Usa `JOIN artistas` o `JOIN users` según corresponda

### 2. **Estados de publicación**
   - ❌ Antes: Usaba `'pendiente_validacion'`
   - ✅ Ahora: Usa `'pendiente'`

### 3. **Campos inexistentes**
   - ❌ Antes: Referenciaba `motivo_rechazo`, `p.contenido`, `ip_address`
   - ✅ Ahora: Eliminadas las referencias innecesarias

---

## 📂 **Estructura Final de Carpeta `/api/`**

```
api/
├── CRUDs Unificados (6 archivos):
│   ├── artistas.php          (GET, register, update_status, delete, get_stats)
│   ├── personal.php          (GET, add, update, delete)
│   ├── borradores.php        (GET, save, delete)
│   ├── solicitudes.php       (get_my, get_all, update)
│   ├── noticias.php          (GET, add, update, delete)
│   └── site_content.php      (GET, update)
│
├── Autenticación y Utilidad (7 archivos):
│   ├── login.php
│   ├── get_estadisticas_inicio.php
│   ├── get_estadisticas_validador.php
│   ├── get_logs.php
│   ├── get_publicaciones.php      (Corregida)
│   ├── get_publicacion_detalle.php (Corregida)
│   └── validar_publicacion.php    (Corregida)
│
├── Referencia (para migración, puede eliminarse):
│   └── unificado/
│       └── (archivos .txt y .php originales)
│
└── ESTE ARCHIVO:
    └── API_DOCUMENTATION.md
```

---

## 🎓 **Próximos Pasos**

1. ✅ **Actualizar todos los archivos JavaScript** para usar los nuevos endpoints
2. ✅ **Probar flujos completos** de validación, registro, creación de obras
3. ✅ **Eliminar carpeta `unificado/`** cuando se confirme que todo funciona
4. ⏳ **Implementar seguridad**: Variables de entorno, validación robusta, CSRF tokens
5. ⏳ **Modernizar frontend**: Frameworks JS, UI/UX mejorada

---

**Creado por:** GitHub Copilot  
**Fecha:** 4 de Noviembre de 2025
