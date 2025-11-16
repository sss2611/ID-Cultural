# 🧪 Testing Manual

Esta carpeta contiene scripts y archivos para pruebas manuales del sistema ID-Cultural.

## 📋 **Archivos de Testing**

### 🚀 **Scripts de API Testing**
- **`test_apis.sh`** - Script bash para probar todas las APIs del sistema
- **`test_editar_obra.sh`** - Test específico para la funcionalidad de edición de obras

### 🐘 **Testing de Base de Datos**
- **`test_db.php`** - Pruebas de conectividad y consultas a la base de datos
- **`test_obras.php`** - Testing específico de operaciones con obras

### 🎨 **Testing de UI/Frontend**
- **`test_footer.html`** - Test del componente footer

## 🎯 **Cómo Ejecutar los Tests**

### Tests de API:
```bash
# Ejecutar todos los tests de API
cd testing/manual
./test_apis.sh

# Test específico de edición
./test_editar_obra.sh
```

### Tests de Base de Datos:
```bash
# Test de conectividad
php testing/manual/test_db.php

# Test de obras
php testing/manual/test_obras.php
```

### Tests de Frontend:
```bash
# Abrir en navegador
open testing/manual/test_footer.html
```

## 📊 **Tipos de Tests Incluidos**

- ✅ **Conectividad** - Verificación de conexiones a BD
- 🔌 **APIs** - Testing de endpoints y respuestas
- 🎨 **UI Components** - Verificación de componentes visuales
- 📝 **CRUD Operations** - Testing de operaciones Create/Read/Update/Delete
- 🔐 **Authentication** - Pruebas de sistema de autenticación

## 📝 **Notas Importantes**

### Configuración Requerida:
- Docker containers ejecutándose
- Base de datos con datos de prueba
- Variables de entorno configuradas

### Resultados:
- Los tests generan output en consola
- Errores se reportan con detalles específicos
- Algunos tests requieren inspección manual

## 🔧 **Tests Automatizados**

Para tests automatizados con PHPUnit, ver la carpeta [`/tests`](../../tests/) en el directorio raíz.

---

> **Nota**: Estos son tests manuales para verificación durante desarrollo. Para CI/CD usar los tests automatizados.