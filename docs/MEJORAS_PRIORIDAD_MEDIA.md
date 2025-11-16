# 📚 DOCUMENTACIÓN - MEJORAS DE PRIORIDAD MEDIA

**Fecha:** 9 de Noviembre de 2025  
**Versión:** 2.0

## 📋 Tabla de Contenidos

1. Tests Automatizados
2. Validación Mejorada de Uploads
3. Optimización de Performance
4. SDK de JavaScript para APIs

---

## 1️⃣ TESTS AUTOMATIZADOS CON PHPUNIT

### Instalación

```bash
# Instalar PHPUnit vía Composer
composer require --dev phpunit/phpunit ^9

# Verificar instalación
vendor/bin/phpunit --version
```

### Estructura de Tests

```
tests/
├── phpunit.xml              # Configuración de PHPUnit
├── bootstrap.php            # Setup inicial
├── Unit/
│   ├── AuthTest.php         # Tests de autenticación
│   ├── ArtistasTest.php     # Tests de artistas
│   ├── BorradoresTest.php   # Tests de publicaciones
│   ├── ValidacionTest.php   # Tests de validación
│   └── ErrorHandlerTest.php # Tests de manejo de errores
└── Integration/
    └── APIsTest.php         # Tests de integración de APIs
```

### Ejecutar Tests

```bash
# Ejecutar todos los tests
vendor/bin/phpunit tests/

# Ejecutar un archivo de test específico
vendor/bin/phpunit tests/Unit/AuthTest.php

# Ejecutar con cobertura (genera reporte HTML)
vendor/bin/phpunit tests/ --coverage-html coverage/

# Ejecutar con verbosidad
vendor/bin/phpunit tests/ --verbose

# Ejecutar y parar en primer error
vendor/bin/phpunit tests/ --stop-on-failure
```

### Ejemplo de Test

```php
<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    /**
     * @test
     */
    public function testValidEmailFormat()
    {
        $email = 'test@example.com';
        
        // Arrange (preparar)
        $expected = true;
        
        // Act (actuar)
        $result = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        
        // Assert (afirmar)
        $this->assertEquals($expected, $result);
    }
}
```

### Métricas de Cobertura

- **Objetivo mínimo:** 70% de cobertura
- **Ver reporte:** Abrir `coverage/index.html` en navegador

---

## 2️⃣ VALIDACIÓN MEJORADA DE UPLOADS

### Ubicación

```
backend/helpers/MultimediaValidator.php
```

### Mejoras Implementadas

✅ Validación de tipo MIME (strict)  
✅ Validación de tamaño de archivo  
✅ Validación de dimensiones de imagen  
✅ Sanitización de nombres de archivo  
✅ Detección de archivos corruptos  

### Uso en Backend

```php
<?php
require_once 'backend/helpers/MultimediaValidator.php';

// Validar imagen
$validation = MultimediaValidator::validarImagen($_FILES['imagen']);

if (!$validation['valido']) {
    echo json_encode(['error' => $validation['mensaje']]);
    exit;
}

// Sanitizar nombre
$filename = MultimediaValidator::sanitizarNombreArchivo($_FILES['imagen']['name']);

// Guardar archivo seguro
$safePath = MultimediaValidator::obtenerRutaSegura(
    'public/uploads/images/',
    $filename
);

if (move_uploaded_file($_FILES['imagen']['tmp_name'], $safePath)) {
    echo json_encode(['success' => 'Archivo guardado']);
}
```

### Limits por Defecto

| Tipo | Tamaño Máximo | Resolución |
|------|---------------|-----------|
| Imagen | 10 MB | 100x100 hasta 4000x4000 px |
| Video | 500 MB | Sin límite |
| Audio | 100 MB | Sin límite |

### Validación en Frontend

```javascript
// Usar el SDK (ver sección 4)
const validation = IdCulturalAPI.validarImagen(file);

if (!validation.valid) {
    alert(validation.error);
    return;
}
```

---

## 3️⃣ OPTIMIZACIÓN DE PERFORMANCE

### Índices en Base de Datos

**Ubicación:** `database/optimizacion_indices.sql`

**Ejecutar:**

```bash
# Desde terminal
mysql -u runatechdev -p1234 idcultural < database/optimizacion_indices.sql

# O desde Docker
docker exec -i idcultural_db mysql -u runatechdev -p1234 idcultural < database/optimizacion_indices.sql
```

### Índices Agregados

✅ **Búsqueda:** nombre, municipio, estado en artistas  
✅ **Relaciones:** artista_id, validador_id en publicaciones  
✅ **Fulltext:** búsqueda de texto completo en biografia  

### Verificar Índices

```sql
SHOW INDEXES FROM artistas;
SHOW INDEXES FROM publicaciones;
```

### Impacto Esperado

| Query | Antes | Después | Mejora |
|-------|-------|---------|--------|
| Buscar artista por nombre | ~500ms | ~50ms | 10x |
| Filtrar por municipio | ~400ms | ~30ms | 13x |
| Obtener borradores | ~300ms | ~20ms | 15x |

### Otros Optimizaciones Recomendadas

#### Caché (Redis)
```php
// Instalar Redis
composer require predis/predis

// Usar en API
$redis = new Predis\Client();
$artistas = $redis->get('artistas_validados');

if (!$artistas) {
    $artistas = obtenerArtistasDelDB();
    $redis->setex('artistas_validados', 3600, json_encode($artistas));
}
```

#### Lazy Loading Frontend
```html
<img src="image.jpg" loading="lazy" alt="Artista">
```

#### Compresión de Imágenes
```bash
# Instalar ImageMagick
sudo apt-get install imagemagick

# Comprimir en PHP
exec('convert input.jpg -quality 85 output.jpg');
```

---

## 4️⃣ SDK JAVASCRIPT PARA APIs

### Ubicación

```
public/static/js/api-sdk.js
```

### Instalación

**En HTML:**

```html
<script src="/static/js/api-sdk.js"></script>

<script>
    const api = new IdCulturalAPI();
    
    // Usar API
    api.getArtistas().then(data => console.log(data));
</script>
```

**Con URL base personalizada:**

```javascript
const api = new IdCulturalAPI('http://example.com/');
```

### Ejemplos de Uso

#### 1. Obtener Artistas

```javascript
api.getArtistas()
    .then(data => {
        console.log('Artistas:', data);
        data.forEach(artista => {
            console.log(artista.nombre);
        });
    })
    .catch(error => console.error(error));
```

#### 2. Registrar Artista

```javascript
const nuevoArtista = {
    nombre: 'Juan Pérez',
    email: 'juan@example.com',
    municipio: 'Medellín',
    categoria: 'Música',
    biografia: 'Artista con 20 años de experiencia'
};

api.registrarArtista(nuevoArtista)
    .then(result => alert('Artista registrado'))
    .catch(error => alert('Error: ' + error.message));
```

#### 3. Guardar Borrador

```javascript
const borrador = {
    titulo: 'Mi Obra Maestra',
    descripcion: 'Descripción de la obra',
    categoria: 'Música',
    anio: 2024
};

api.guardarBorrador(borrador)
    .then(result => console.log('Borrador guardado:', result))
    .catch(error => console.error('Error:', error));
```

#### 4. Login

```javascript
api.login('user@example.com', 'password123')
    .then(result => {
        console.log('Login exitoso');
        // Token se guarda automáticamente
        return api.getEstadisticas();
    })
    .then(stats => console.log('Estadísticas:', stats))
    .catch(error => alert('Error de login'));
```

#### 5. Validar Perfil (Admin/Validador)

```javascript
api.validarPerfil(artistaId, 'validado', 'Perfil aprobado')
    .then(result => console.log('Validación completada'))
    .catch(error => console.error('Error:', error));
```

### Métodos Disponibles

#### Artistas
- `getArtistas()` - Obtener todos
- `getArtista(id)` - Obtener uno
- `registrarArtista(data)` - Crear
- `actualizarArtista(data)` - Actualizar

#### Borradores
- `getBorradores()` - Obtener mis borradores
- `guardarBorrador(data)` - Crear/actualizar
- `eliminarBorrador(id)` - Eliminar

#### Validación
- `getSolicitudes()` - Ver solicitudes
- `validarPerfil(id, estado, comentario)` - Validar

#### Autenticación
- `login(email, password)` - Iniciar sesión
- `logout()` - Cerrar sesión
- `cambiarPassword(actual, nueva)` - Cambiar contraseña
- `solicitarRecuperacion(email)` - Recuperar contraseña

#### Estadísticas
- `getEstadisticas()` - Stats generales
- `getEstadisticasValidador()` - Stats validador

#### Notificaciones
- `getNotificaciones()` - Obtener todas
- `marcarNotificacionLeida(id)` - Marcar como leída

### Funciones de Validación Estática

```javascript
// Validar email
if (!IdCulturalAPI.validarEmail('test@example.com')) {
    alert('Email inválido');
}

// Validar teléfono
if (!IdCulturalAPI.validarTelefono('+573001234567')) {
    alert('Teléfono inválido');
}

// Validar contraseña
if (!IdCulturalAPI.validarPassword('SecurePass123!')) {
    alert('Contraseña débil');
}

// Validar imagen
const validation = IdCulturalAPI.validarImagen(file);
if (!validation.valid) {
    alert(validation.error);
}

// Validar video
const videoValidation = IdCulturalAPI.validarVideo(file);
if (!videoValidation.valid) {
    alert(videoValidation.error);
}
```

### Manejo de Errores

```javascript
api.getArtistas()
    .catch(error => {
        console.error('Error:', error.message);
        
        // Hacer algo según el tipo de error
        if (error.message.includes('401')) {
            // Redirigir a login
            window.location.href = '/login.php';
        } else if (error.message.includes('500')) {
            // Error del servidor
            alert('Error en el servidor. Intenta más tarde.');
        }
    });
```

### Async/Await

```javascript
async function obtenerYMostrarArtistas() {
    try {
        const artistas = await api.getArtistas();
        console.log('Artistas:', artistas);
    } catch (error) {
        console.error('Error:', error);
    }
}

// Llamar función
obtenerYMostrarArtistas();
```

---

## 🚀 PRÓXIMOS PASOS

### Fase 1 (Completado)
- ✅ Tests unitarios con PHPUnit
- ✅ Validación mejorada de uploads
- ✅ Índices en base de datos
- ✅ SDK JavaScript para APIs

### Fase 2 (Recomendado)
- 🔲 Implementar caché con Redis
- 🔲 Agregar tests de integración
- 🔲 Optimizar imágenes automáticamente
- 🔲 Implementar CDN

### Fase 3 (Futuro)
- 🔲 Tests E2E con Cypress/Selenium
- 🔲 Monitoring y alertas
- 🔲 Analytics avanzado
- 🔲 API Pública con Swagger

---

## 📞 SOPORTE

Para preguntas o issues:
1. Revisar la documentación de PHPUnit: https://phpunit.de/
2. Revisar MDN Web Docs: https://developer.mozilla.org/
3. Contactar al equipo de desarrollo

---

*Documento actualizado: 9 de Noviembre de 2025*
