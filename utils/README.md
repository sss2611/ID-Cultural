# 🛠️ Utilidades del Sistema

Esta carpeta contiene herramientas auxiliares para el mantenimiento y diagnóstico del sistema ID-Cultural.

## 📂 Estructura

### 🔍 `/checks/` - Verificaciones del Sistema
Scripts para verificar el estado y funcionamiento de diferentes componentes:

- **`check_multimedia.php`** - Verificación de archivos multimedia subidos
- **`check_obras.php`** - Verificación del estado de las obras en BD
- **`check_obras_detail.php`** - Verificación detallada de obras específicas
- **`check_validation_status.php`** - Verificación del estado de validación
- **`check_users.php`** - Verificación de usuarios y perfiles
- **`check_session.php`** - Verificación de sesiones activas

### 🐛 `/debug/` - Herramientas de Depuración
Scripts para debugging y análisis detallado:

- **`debug_session.php`** - Depuración de problemas de sesión
- **`inspect_artistas.php`** - Inspección detallada de datos de artistas
- **`inspect_db.php`** - Inspección general de la base de datos

### 🔧 `/fixes/` - Correcciones y Utilidades
Scripts para reparar problemas y mantener la integridad:

- **`fix_obra_4.php`** - Corrección específica para obra ID 4
- **`cleanup_bd.php`** - Limpieza y optimización de la base de datos
- **`prepare_test.php`** - Preparación del entorno de testing

## 💡 **Uso Recomendado**

### Para Diagnóstico Rápido:
```bash
# Verificar estado general del sistema
php utils/checks/check_users.php
php utils/checks/check_obras.php
php utils/debug/inspect_db.php
```

### Para Mantenimiento:
```bash
# Limpiar y optimizar
php utils/fixes/cleanup_bd.php
```

### Para Debugging:
```bash
# Depurar problemas específicos
php utils/debug/debug_session.php
php utils/debug/inspect_artistas.php
```

## ⚠️ **Advertencias**

- **Uso en Producción**: Algunos scripts pueden afectar datos. Revisar código antes de ejecutar.
- **Backups**: Realizar backup antes de ejecutar scripts de `/fixes/`
- **Permisos**: Verificar que los scripts tengan acceso a la base de datos

---

> **Nota**: Estos scripts son herramientas de desarrollo y mantenimiento. No están destinados para uso de usuarios finales.