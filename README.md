# 🎭 ID Cultural

Proyecto desarrollado para la Subsecretaría de Cultura de Santiago del Estero como parte de las Prácticas Profesionalizantes del ITSE.

---

## 📚 Descripción

**ID Cultural** es una plataforma web tipo "Wikipedia local", destinada a **centralizar, validar y exhibir** información sobre artistas y expresiones culturales de Santiago del Estero. El sistema permite a los artistas **crear y gestionar borradores de perfiles culturales**, que luego son sometidos a un proceso de **validación por parte de moderadores**. Una vez aprobados, estos perfiles se publican en una **Wiki de Artistas** abierta al público, conformando una valiosa biblioteca digital de contenido artístico local.

---

## 🗂️ Estructura del Proyecto
```
```
ID_Cultural/
│
├── backend/ 
│       ├── config/ 
│       │     └── connection.php
│       ├── controllers/
│       │     └── actualizar_datos_contacto.php/
│       │     ├── actualizar_estado.php/
│       │     ├── actualizar_perfil_artista.php/
│       │     ├── actualizar_perfil_publico.php/
│       │     ├── aprobar_perfil.php/
│       │     ├── blanquear_clave.php/
│       │     ├── cambiar_clave_token.php/
│       │     ├── cambiar_clave.php/
│       │     ├── enviar_validacion.php/
│       │     ├── guardar_borrador.php/
│       │     ├── guardar_intereses.php/
│       │     ├── logout.php/
│       │     ├── procesar_registro.php/
│       │     ├── solicitar_recuperacion_clave.php/
│       │     └── verificar_usuario.php/
│       └── helpers/ # Utilidades y validadores
│              ├── EmailHelper.php/
│              ├── ErrorHandler.php/
│              ├── MultimediaValidator.php/
│              ├── Pagination.php/
│              └── RateLimiter.php/
├── public/ # 🌐 Frontend y archivos públicos
│ ├── api/ # APIs REST del sistema
│ ├── src/views/ # Vistas HTML organizadas
│ ├── static/ # CSS, JS, imágenes
│ └── [páginas principales] # index.php, wiki.php, etc.
│
├── components/ # 🧩 Componentes reutilizables
│ ├── header.php # Header común
│ ├── navbar.php # Barra de navegación
│ └── footer.php # Footer común
│
├── database/ # 🗄️ Esquemas y backups de BD
│ └── migrations/ # Scripts de migración
│
├── tests/ # 🧪 Tests automatizados (PHPUnit)
│ ├── Unit/ # Tests unitarios
│ │ ├── ArtistasTest.php
│ │ ├── AuthTest.php
│ │ └── [más tests...]
│ └── phpunit.xml # Configuración PHPUnit
│
├── testing/ # 🔍 Testing manual y scripts de prueba
│ └── manual/ # Scripts de testing manual
│ ├── test_apis.sh
│ ├── test_db.php
│ └── [más tests manuales...]
│
├── utils/ # 🛠️ Herramientas de mantenimiento
│ ├── checks/ # Scripts de verificación
│ │ ├── check_users.php
│ │ └── check_obras.php
│ ├── debug/ # Herramientas de debugging
│ │ ├── inspect_db.php
│ │ └── debug_session.php
│ └── fixes/ # Scripts de corrección
│ ├── cleanup_bd.php
│ └── prepare_test.php
│
├── docs/ # 📚 Documentación técnica completa
│ ├── README.md # Índice de toda la documentación
│ ├── Manual_ID-Cultural.md # Guía completa para usuarios
│ ├── ANALISIS_PLATAFORMA_COMPLETO.md # Análisis técnico
│ ├── IMPLEMENTACION_COMPLETADA.md # Registro de implementaciones
│ └── [más documentos técnicos...] # Ver docs/README.md para lista completa
│
└── scripts/ # 📜 Scripts de deployment y utilidades
├── export_database.sh
└── import_database.sh
```
```
---

## ⚙️ Tecnologías Utilizadas

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Base de Datos:** MySQL/MariaDB
- **Contenedores:** Docker, Docker Compose (para orquestación del entorno de desarrollo)

---

## ✅ Funcionalidades Clave

- **Registro y Autenticación:** Sistema robusto para artistas, validadores, editores y administradores.
- **Gestión de Perfiles por Artistas:**
    - Creación y edición de **borradores** de perfiles culturales.
    - Envío de borradores a **validación**.
    - Visualización del **estado** de sus envíos (borrador, pendiente, validado, rechazado).
- **Proceso de Validación y Moderación:**
    - Panel específico para **validadores** para revisar y aprobar/rechazar perfiles pendientes.
    - Panel para **editores** con capacidad de gestionar y modificar cualquier perfil.
- **Wiki de Artistas Pública:** Exhibición de perfiles culturales **validados**, con opciones de búsqueda y filtrado.
- **Carga de Contenido Multimedia:** Soporte para incluir obras, eventos, biografías, documentos y otros materiales asociados a los artistas.
- **Buscador Avanzado:** Filtros por género, localidad, tipo de expresión artística y año.
- **Panel Administrativo:** Gestión completa de usuarios (artistas, validadores, editores, administradores) y contenidos.

---

## � Documentación

### 📖 **Documentación Completa**
Toda la documentación técnica y de usuario se encuentra organizada en la carpeta **[`/docs`](./docs/)**:

- **[📋 Índice de Documentación](./docs/README.md)** - Lista completa y organizada de todos los documentos
- **[📖 Manual de Usuario](./docs/Manual_ID-Cultural.md)** - Guía completa para usuarios de la plataforma
- **[🔧 Análisis Técnico](./docs/ANALISIS_PLATAFORMA_COMPLETO.md)** - Documentación técnica detallada
- **[🧪 Guía de Testing](./docs/TESTS_DOCUMENTATION.md)** - Documentación de pruebas y testing

### 🚀 **Enlaces Rápidos**
- **API Documentation**: [`/public/api/API_DOCUMENTATION.md`](./public/api/API_DOCUMENTATION.md)
- **Implementaciones**: [`/docs/IMPLEMENTACION_COMPLETADA.md`](./docs/IMPLEMENTACION_COMPLETADA.md)
- **Base de Datos**: [`/docs/DATABASE_SYNC.md`](./docs/DATABASE_SYNC.md)

### 🧪 **Testing y Desarrollo**
- **Tests Automatizados**: [`/tests/`](./tests/) - PHPUnit tests para CI/CD
- **Testing Manual**: [`/testing/`](./testing/) - Scripts de prueba manual
- **Utilidades**: [`/utils/`](./utils/) - Herramientas de debugging y mantenimiento

---

## �👥 Equipo de Desarrollo

**Runatech** – Estudiantes del ITSE Santiago del Estero

- Maximiliano Fabián Padilla
- Marcos Ariel Romano
- Mario Sebastián Ruiz
- Sandra Soledad Sánchez

Colaboración: Subsecretaría de Cultura de Santiago del Estero

---

## 📄 Licencia

Este proyecto fue realizado con fines educativos y de contribución cultural. Derechos reservados al equipo **Runatech** y a la **Subsecretaría de Cultura de Santiago del Estero**.
