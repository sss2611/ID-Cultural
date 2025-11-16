# 📘 Manual de Instrucciones - ID Cultural

**Versión:** 1.0  
**Última actualización:** 6 de noviembre de 2025  
**Plataforma:** Subsecretaría de Cultura - Santiago del Estero

---

## 📑 Tabla de Contenidos

1. [Introducción](#introducción)
2. [Inicio Rápido](#inicio-rápido)
3. [Registro e Inicio de Sesión](#registro-e-inicio-de-sesión)
4. [Panel de Control](#panel-de-control)
5. [Gestión de Artistas](#gestión-de-artistas)
6. [Gestión de Obras](#gestión-de-obras)
7. [Panel Editor](#panel-editor)
8. [Panel Validador](#panel-validador)
9. [Panel Admin](#panel-admin)
10. [Preguntas Frecuentes](#preguntas-frecuentes)
11. [Troubleshooting](#troubleshooting)

---

## 🎯 Introducción

**ID Cultural** es una plataforma digital dedicada a visibilizar, preservar y promover la identidad artística y cultural de Santiago del Estero.

### 🎭 ¿Qué puedo hacer en ID Cultural?

- ✅ Registrar tu perfil de artista
- ✅ Publicar tus obras y proyectos
- ✅ Acceder a la Biblioteca Digital
- ✅ Explorar otros artistas y sus trabajos
- ✅ Validar perfiles y contenido (si eres validador)
- ✅ Administrar la plataforma (si eres admin)

### 👥 Tipos de Usuarios

| Rol | Descripción | Permisos |
|-----|-------------|----------|
| **Visitante** | Usuario sin registrar | Ver wiki, noticias, artistas públicos |
| **Artista** | Usuario registrado | Crear perfil, subir obras, editar contenido personal |
| **Validador** | Rol especial | Revisar y aprobar perfiles y obras |
| **Editor** | Rol especial | Editar contenido de la plataforma |
| **Admin** | Administrador | Acceso total a la plataforma |

---

## 🚀 Inicio Rápido

### 1. Acceder a la plataforma

```
URL: http://localhost:8080 (desarrollo)
o
http://[IP-TAILSCALE] (producción)
```

### 2. Registrarse como Artista

1. Haz clic en **"Registrarse"** en la esquina superior derecha
2. Completa el formulario con:
   - Email
   - Contraseña (mínimo 8 caracteres)
   - Nombre completo
   - Aceptar términos y condiciones
3. Haz clic en **"Crear Cuenta"**
4. ✅ Recibirás confirmación por email (opcional)

### 3. Iniciar Sesión

1. Haz clic en **"Iniciar Sesión"**
2. Ingresa tu email y contraseña
3. Haz clic en **"Entrar"**
4. 🎉 ¡Bienvenido a tu panel!

---

## 🔐 Registro e Inicio de Sesión

### Crear una Cuenta Nueva

#### Paso a paso:

1. **Accede a la página de Registro**
   - URL: `/src/views/pages/auth/registro.php`
   - O haz clic en "Registrarse" en el navbar

2. **Completa los campos obligatorios:**
   - 📧 **Email:** Debe ser único y válido
   - 🔑 **Contraseña:** Mínimo 8 caracteres
   - 📝 **Nombre Completo:** Tu nombre real
   - ✓ **Acepto términos y condiciones:** Marca el checkbox

3. **Haz clic en "Crear Cuenta"**

4. **Confirmación:**
   - Si todo está bien, verás: "¡Cuenta creada exitosamente!"
   - Se guardará en la base de datos como usuario registrado

### Iniciar Sesión

1. **Ve a la página de Login**
   - URL: `/src/views/pages/auth/login.php`
   - O haz clic en "Iniciar Sesión"

2. **Ingresa tus credenciales:**
   - 📧 Email registrado
   - 🔑 Contraseña

3. **Opciones:**
   - ☑️ "Recuérdame" - Mantén la sesión activa
   - "¿Olvidaste tu contraseña?" - Recuperar acceso

4. **Haz clic en "Entrar"**

### Recuperar Contraseña

Si olvidaste tu contraseña:

1. Ve a la página de login
2. Haz clic en "¿Olvidaste tu contraseña?"
3. Ingresa tu email
4. Recibirás un enlace para resetear (funcionalidad en desarrollo)

### Cerrar Sesión

1. Haz clic en tu nombre en la esquina superior derecha
2. Selecciona **"Cerrar Sesión"**
3. ✅ Tu sesión ha finalizado

---

## 🎛️ Panel de Control

### Acceder al Panel

Después de iniciar sesión, automáticamente irás a tu panel según tu rol:

- **Artista:** `/src/views/pages/artista/dashboard.php`
- **Validador:** `/src/views/pages/validador/gestion_pendientes.php`
- **Editor:** `/src/views/pages/editor/panel_editor.php`
- **Admin:** `/src/views/pages/admin/estado_solicitud.php`

### Elementos Comunes del Panel

```
┌─────────────────────────────────────────┐
│         NAVBAR - Navegación             │
├─────────────────────────────────────────┤
│ ID Cultural    [Wiki] [Noticias] [👤]   │
├─────────────────────────────────────────┤
│  SIDEBAR                │  CONTENIDO    │
│  - Dashboard            │               │
│  - Mi Perfil            │  Bienvenida   │
│  - Mis Obras            │  Estadísticas │
│  - Configuración        │  Acciones     │
├─────────────────────────────────────────┤
│              FOOTER                     │
└─────────────────────────────────────────┘
```

---

## 👤 Gestión de Artistas

### Completar tu Perfil de Artista

#### 1. Acceder a "Mi Perfil"

- En el panel, haz clic en **"Mi Perfil"** o **"Editar Perfil"**
- URL: `/public/src/views/pages/artista/estado_artista.php`

#### 2. Información Personal

Completa los siguientes campos:

| Campo | Descripción | Obligatorio |
|-------|-------------|-----------|
| **Nombre Completo** | Tu nombre artístico o real | ✅ |
| **Biografía** | Cuéntanos sobre ti (200-500 caracteres) | ✅ |
| **Disciplina Artística** | Selecciona: Música, Artes Plásticas, Danza, etc. | ✅ |
| **Localidad** | Tu municipio en Santiago del Estero | ✅ |
| **Teléfono** | Tu número de contacto | ⭕ |
| **Redes Sociales** | Links a Instagram, Facebook, etc. | ⭕ |
| **Sitio Web** | Tu página personal (si tienes) | ⭕ |
| **Foto de Perfil** | Imagen JPG/PNG (máx 5MB) | ⭕ |

#### 3. Guardar Cambios

- Revisa que toda la información sea correcta
- Haz clic en **"Guardar Cambios"**
- ✅ Tu perfil será enviado a validación

### Estados del Perfil

```
┌──────────────────────────────────────────────┐
│ ESTADOS DEL PERFIL                           │
├──────────────────────────────────────────────┤
│ 🟡 Incompleto      → Falta información       │
│ 🔵 En Revisión     → Esperando validación    │
│ 🟢 Aprobado        → Perfil público          │
│ 🔴 Rechazado       → Ver comentarios         │
│ ⚫ Inactivo         → Desactivado             │
└──────────────────────────────────────────────┘
```

### Ver Otros Artistas

1. Haz clic en **"Wiki"** en el navbar
2. Ve a la sección **"Artistas"**
3. Busca por nombre o disciplina
4. Haz clic en un artista para ver su perfil completo

---

## 🎨 Gestión de Obras

### Crear una Nueva Obra

#### Paso a paso:

1. **Ve a "Mis Obras"** en tu panel
2. Haz clic en **"+ Nueva Obra"** o **"Crear Obra"**
3. Rellena el formulario:

| Campo | Descripción | Tipo |
|-------|-------------|------|
| **Título** | Nombre de la obra | Texto |
| **Descripción** | Detalle sobre la obra | Rich Text |
| **Categoría** | Selecciona: Pintura, Escultura, Fotografía, etc. | Select |
| **Año** | Año de creación | Año |
| **Localidad** | Donde fue creada | Select |
| **Imagen** | Foto de la obra (JPG/PNG, máx 5MB) | Archivo |
| **Disponibilidad** | ¿Está disponible? | Checkbox |

4. Haz clic en **"Guardar como Borrador"** o **"Enviar a Validación"**

### Estados de una Obra

```
📝 Borrador         → Solo visible para ti
⏳ En Validación    → Esperando revisión
✅ Publicada        → Visible en la plataforma
❌ Rechazada        → Necesita cambios
🗑️ Eliminada        → Removida
```

### Editar una Obra

1. Ve a **"Mis Obras"**
2. Busca la obra
3. Haz clic en **"Editar"** (icono de lápiz)
4. Realiza cambios
5. Guarda cambios

### Publicar una Obra

1. La obra debe estar en estado **"Borrador"**
2. Haz clic en **"Enviar a Validación"**
3. Se enviará a los validadores
4. Espera la aprobación
5. Una vez aprobada, aparecerá en la wiki

---

## ✏️ Panel Editor

### Acceder al Panel Editor

- **Rol requerido:** Editor o Admin
- **URL:** `/public/src/views/pages/editor/panel_editor.php`

### Funciones del Editor

#### 1. Editar Página Principal (`gestion_inicio.php`)

Personaliza el contenido que ven los visitantes:

**Sección de Bienvenida:**
- 📝 **Título Principal** - Encabezado de la página
- 📄 **Párrafo de Bienvenida** - Descripción con fondo decorativo
- 💬 **Eslogan** - Frase destacada

**Editor Quill:**
- Todas las secciones usan **Editor WYSIWYG Quill**
- Puedes aplicar:
  - **Formatos:** Negrita, cursiva, subrayado, tachado
  - **Fuentes:** 8 fuentes web-safe disponibles
  - **Alineación:** Izquierda, centro, derecha
  - **Colores:** Texto y fondo personalizables
  - **Listas:** Numeradas y viñetas
  - **Elementos:** Citas, código, enlaces, imágenes

**Carrusel de Imágenes:**
- Sube hasta 3 imágenes para el carrusel principal
- Formatos: JPG, PNG, GIF, WebP (máx 5MB)
- Las imágenes aparecerán en la página de inicio

#### 2. Guardar Cambios

1. Edita el contenido en los editores
2. Haz clic en **"Guardar Cambios"**
3. ✅ Los cambios se actualizan inmediatamente en la página pública

#### 3. Vista Previa

- Los cambios se guardan en tiempo real
- Abre una nueva pestaña con la URL de inicio para ver cambios

---

## ✔️ Panel Validador

### Acceder al Panel Validador

- **Rol requerido:** Validador o Admin
- **URL:** `/public/src/views/pages/validador/gestion_pendientes.php`

### Funciones del Validador

#### 1. Revisar Perfiles Pendientes

**Tabla de Artistas:**
- Ver lista de artistas en revisión
- Ver información: Nombre, categoría, fecha de registro
- Filtrar por: Búsqueda, categoría, municipio

**Acciones:**
- ✅ **Aprobar** - El perfil se hace público
- ❌ **Rechazar** - Se devuelve al artista con comentarios
- 👁️ **Ver Detalles** - Revisar información completa

#### 2. Revisar Obras Pendientes

**Tabla de Obras:**
- Lista de obras enviadas a validación
- Información: Obra, categoría, ubicación, fecha
- Filtros disponibles

**Revisar Obra:**
1. Haz clic en la obra
2. Ve: Título, descripción, imagen, categoría
3. Lee el contenido completo
4. Decide: ✅ Aprobar o ❌ Rechazar

**Aprobar Obra:**
```
1. Haz clic en "Aprobar"
2. (Opcional) Agrega comentario
3. Confirma
4. ✅ La obra se publica
```

**Rechazar Obra:**
```
1. Haz clic en "Rechazar"
2. OBLIGATORIO: Ingresa motivo del rechazo
3. Confirma
4. ❌ Se devuelve al artista
```

#### 3. Filtros y Búsqueda

- **Búsqueda:** Busca por nombre de artista u obra
- **Categoría:** Filtra por tipo de arte
- **Municipio:** Filtra por localidad
- **Estado:** Ver solo pendientes o todos

---

## 🛠️ Panel Admin

### Acceder al Panel Admin

- **Rol requerido:** Admin
- **URL:** `/public/src/views/pages/admin/estado_solicitud.php`

### Funciones del Admin

#### 1. Vista Unificada

El panel Admin tiene acceso a:
- ✅ Gestión de artistas
- ✅ Gestión de obras
- ✅ Gestión de usuarios
- ✅ Gestión de validadores
- ✅ Reportes y estadísticas

#### 2. Gestión de Usuarios

**Ver Usuarios:**
1. Ve a la sección de "Usuarios"
2. Ve lista de todos los usuarios registrados
3. Información: Email, nombre, rol, fecha registro

**Cambiar Rol:**
1. Selecciona un usuario
2. Haz clic en "Cambiar Rol"
3. Selecciona nuevo rol: Artista, Validador, Editor, Admin
4. Confirma

**Desactivar Usuario:**
1. Selecciona usuario
2. Haz clic en "Desactivar"
3. ⚠️ El usuario no podrá entrar hasta reactivación

#### 3. Estadísticas

En el Dashboard ves:
- 📊 Total de artistas validados
- 🎨 Total de obras publicadas
- 📰 Total de noticias
- 📈 Gráficos de actividad

---

## 📱 Wiki - Explorar Contenido

### Acceder a la Wiki

1. Haz clic en **"Wiki"** en el navbar
2. O ve a: `/wiki.php`

### Secciones de la Wiki

#### 1. Artistas

```
🔍 BUSCAR ARTISTAS
├── Todos los artistas validados
├── Filtrar por disciplina
├── Filtrar por municipio
└── Perfil completo de cada artista
```

**En cada perfil de artista:**
- 👤 Foto y nombre
- 📝 Biografía
- 🎭 Disciplina
- 📍 Ubicación
- 🔗 Redes sociales
- 🎨 Sus obras

#### 2. Obras

```
🎨 GALERÍA DE OBRAS
├── Todas las obras publicadas
├── Filtrar por categoría
├── Filtrar por artista
└── Vista en galería
```

#### 3. Noticias

```
📰 ÚLTIMAS NOTICIAS
├── Noticias recientes
├── Archivo completo
├── Compartir en redes
└── Comentarios (si está habilitado)
```

---

## ⚙️ Configuración y Preferencias

### Cambiar Contraseña

1. Ve a **"Configuración"** o **"Cambiar Contraseña"**
2. Ingresa tu **contraseña actual**
3. Ingresa tu **nueva contraseña** (mínimo 8 caracteres)
4. Confirma la contraseña
5. Haz clic en **"Actualizar Contraseña"**

### Actualizar Datos Personales

1. Ve a **"Mi Perfil"**
2. Edita los campos que desees
3. Haz clic en **"Guardar"**

### Preferencias de Notificaciones

(Característica próximamente disponible)

---

## ❓ Preguntas Frecuentes

### **P: ¿Cuánto tiempo tarda la validación de mi perfil?**
**R:** Entre 24-48 horas. Los validadores revisan regularmente los perfiles pendientes.

### **P: ¿Puedo tener múltiples perfiles?**
**R:** No. Solo puedes tener un perfil por email. Si necesitas cambiar datos, edita tu perfil existente.

### **P: ¿Qué formatos de imagen acepta?**
**R:** JPG, PNG, GIF y WebP. Máximo 5MB por archivo.

### **P: ¿Puedo editar una obra después de publicarla?**
**R:** Sí. Si ya está publicada, puedes editarla. Los cambios se verán inmediatamente.

### **P: ¿Qué pasa si rechazanMi obra?**
**R:** Se devuelve a estado "Borrador" con comentarios del validador. Puedes editarla y reenviar.

### **P: ¿Puedo descargar mis obras?**
**R:** Las imágenes están disponibles en la plataforma. Puedes copiarlas o contactar al admin.

### **P: ¿Cómo recupero mi cuenta si la olvidé?**
**R:** Usa "¿Olvidaste tu contraseña?" en login. Si no funciona, contacta a admin@idcultural.gob.ar

### **P: ¿Puedo ver datos de otros artistas?**
**R:** Sí. En la Wiki puedes ver todos los artistas validados y sus obras públicas.

### **P: ¿Se puede eliminar una obra?**
**R:** Sí, si está en estado Borrador o Rechazada. Obras publicadas deben ser rechazadas primero.

### **P: ¿Hay límite de obras que puedo subir?**
**R:** No. Puedes subir cuantas obras desees.

---

## 🛠️ Troubleshooting

### Problemas de Acceso

#### **Error: "Página no encontrada (404)"**
- **Causa:** URL incorrecta o archivo eliminado
- **Solución:** Intenta desde el navbar o panel principal

#### **Error: "Acceso denegado"**
- **Causa:** No tienes permiso para esa sección
- **Solución:** Verifica tu rol. Solo ciertos roles acceden a ciertas áreas

#### **Error: "No puedo iniciar sesión"**
- **Causa:** Email/contraseña incorrectos
- **Solución:** Verifica las credenciales. Usa "Olvidé contraseña"

### Problemas con Formularios

#### **Error: "Email ya registrado"**
- **Causa:** Ya existe una cuenta con ese email
- **Solución:** Usa otro email o recupera tu contraseña

#### **Error: "Archivo demasiado grande"**
- **Causa:** Imagen > 5MB
- **Solución:** Comprime la imagen antes de subirla

#### **Error: "Validación fallida"**
- **Causa:** Faltan campos obligatorios o formato incorrecto
- **Solución:** Verifica todos los campos (*) obligatorios

### Problemas de Rendimiento

#### **Página carga lentamente**
- **Soluciones:**
  - Actualiza la página (Ctrl+F5 o Cmd+Shift+R)
  - Limpia caché del navegador
  - Intenta con otro navegador
  - Verifica tu conexión a internet

#### **Las imágenes no cargan**
- **Soluciones:**
  - Recarga la página
  - Verifica formato (JPG, PNG, GIF, WebP)
  - Intenta subir nuevamente
  - Contacta al administrador

### Contactar Soporte

Si tienes problemas que no se resuelven:

📧 **Email:** soporte@idcultural.gob.ar  
💬 **Formulario:** `/contacto.html`  
🏛️ **Oficina:** Subsecretaría de Cultura, Santiago del Estero

---

## 📚 Recursos Adicionales

- 📄 [Términos y Condiciones](/terminos_condiciones.php)
- 🔒 [Política de Privacidad](/privacidad.html)
- 📖 [Documentación técnica](./DATABASE_SYNC.md)
- 🐛 [Reportar Bug](https://github.com/runatechdev/ID-Cultural/issues)

---

## 📝 Notas Importantes

✅ **Datos Seguros:** Tu información está protegida con encriptación  
✅ **Privacidad:** Tus datos no se comparten sin consentimiento  
⚠️ **Contenido Apropiado:** Solo puedes subir contenido cultural/artístico  
⚠️ **Respeto:** Trata a otros usuarios con respeto  
🔄 **Backups:** La plataforma realiza backups regulares de tus datos

---

## 📞 Información de Contacto

**ID Cultural - Subsecretaría de Cultura**

- 🌐 Sitio Web: `http://idcultural.gob.ar`
- 📧 Email: `info@idcultural.gob.ar`
- 📱 Teléfono: (Próximamente)
- 📍 Dirección: Santiago del Estero, Argentina

---

**¡Gracias por ser parte de ID Cultural! 🎭✨**

*Para más información, visita nuestra [Página Principal](/) o contacta a nuestro equipo de soporte.*
