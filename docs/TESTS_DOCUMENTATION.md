# 🧪 Tests Unitarios - ID Cultural

## ✅ Estado de los Tests

```
PHPUnit 9.6.29
Runtime: PHP 8.3.6

Total de Tests: 29
Tests Pasados: 29 ✅
Tests Fallidos: 0
Assertions: 37
Coverage: Ejecutándose...
```

## 📋 Tests Implementados

### 1. **ArtistasTest.php** - Tests de Validación de Artistas

```php
✅ testArtistNameNotEmpty() - Verifica que el nombre del artista no esté vacío
✅ testArtistCategoryValid() - Valida que la categoría esté en lista válida
✅ testArtistEmailFormat() - Verifica formato válido de email
✅ testArtistPhoneFormat() - Valida formato de teléfono internacional
✅ testArtistBiographyMinLength() - Verifica longitud mínima de biografía (20 caracteres)
✅ testArtistStatusValidation() - Valida estados: pendiente, validado, rechazado
✅ testArtistMunicipalityRequired() - Verifica que municipio sea obligatorio
```

**Categorías Válidas Testeadas:**
- Música
- Pintura
- Escultura
- Danza
- Teatro

**Estados Válidos Testeados:**
- pendiente
- validado
- rechazado

---

### 2. **AuthTest.php** - Tests de Autenticación

```php
✅ testValidEmailFormat() - Formato válido de email
✅ testInvalidEmailFormat() - Rechazo de email inválido
✅ testPasswordMinLength() - Contraseña mínimo 8 caracteres
✅ testPasswordStrength() - Validación de contraseña fuerte
✅ testLoginValidation() - Validación de credenciales
✅ testSessionCreation() - Creación de sesión tras login
✅ testLogoutClearsSession() - Logout limpia sesión
```

**Requisitos de Contraseña Validados:**
- Mínimo 8 caracteres
- Al menos 1 mayúscula
- Al menos 1 número

---

### 3. **ValidacionTest.php** - Tests de Validación de Perfiles

```php
✅ testValidacionStateRequired() - Estado es obligatorio
✅ testValidacionCommentOptional() - Comentario es opcional
✅ testValidacionDateTracking() - Seguimiento de fechas
✅ testValidadorRoleRequired() - Rol validador es obligatorio
```

**Estados de Validación:**
- pendiente
- validado
- rechazado

---

### 4. **BorradoresTest.php** - Tests de Borradores

```php
✅ testBorradorCreation() - Creación de borrador
✅ testBorradorContent() - Contenido de borrador
✅ testBorradorUpdate() - Actualización de borrador
✅ testBorradorDelete() - Eliminación de borrador
✅ testBorradorVersioning() - Versionado de borradores
```

---

### 5. **ErrorHandlerTest.php** - Tests de Manejo de Errores

```php
✅ testErrorLogging() - Logging de errores
✅ testCustomErrorMessages() - Mensajes de error personalizados
✅ testErrorResponseFormat() - Formato de respuesta de error
✅ testExceptionHandling() - Manejo de excepciones
```

---

## 🚀 Cómo Ejecutar los Tests

### Ejecutar todos los tests:
```bash
php vendor/bin/phpunit tests/Unit/
```

### Ejecutar test específico:
```bash
php vendor/bin/phpunit tests/Unit/ArtistasTest.php
```

### Ejecutar con cobertura:
```bash
php vendor/bin/phpunit tests/Unit/ --coverage-html coverage/
```

### Ejecutar con salida verbose:
```bash
php vendor/bin/phpunit tests/Unit/ --verbose
```

### Ejecutar con reporte en XML:
```bash
php vendor/bin/phpunit tests/Unit/ --log-junit test-results.xml
```

---

## 📊 Cobertura de Tests

**Casos cubiertos:**
- ✅ Validación de datos de entrada
- ✅ Formatos de email y teléfono
- ✅ Longitud mínima de campos
- ✅ Valores permitidos (enums)
- ✅ Requisitos de contraseña
- ✅ Estados de validación
- ✅ Manejo de errores

**Casos por cubrir:**
- ⏳ Integración con BD real
- ⏳ Autenticación con tokens
- ⏳ Permisos y autorización
- ⏳ Rate limiting
- ⏳ Multimedia uploads
- ⏳ Búsqueda y filtros

---

## 🔧 Configuración

### phpunit.xml.dist
```xml
- Bootstrap: tests/bootstrap.php
- Suites: Unit, Feature
- Coverage: backend/, public/api/
- Error Reporting: E_ALL
```

### tests/bootstrap.php
```php
- Define BASE_PATH y constantes
- Carga Composer autoloader
- Configuración para testing
- No conecta a BD real en tests
```

---

## 📈 Próximos Pasos

### PRIORIDAD ALTA
1. **Agregar Feature Tests**
   - Tests de integración con BD
   - Tests de API endpoints
   - Tests de flujos completos

2. **Aumentar Cobertura**
   - Tests para helpers
   - Tests para controllers
   - Tests para validadores

3. **Tests de Seguridad**
   - SQL Injection
   - XSS
   - CSRF
   - Rate Limiting

### PRIORIDAD MEDIA
4. **Mock Objects**
   - Mockear conexión BD
   - Mockear envío de emails
   - Mockear servicios externos

5. **Performance Tests**
   - Benchmark de queries
   - Load testing
   - Memory usage

---

## 🐛 Troubleshooting

### Error: "dom", "xml", "xmlwriter" extensions not found
```bash
sudo apt-get install php8.3-xml
```

### Error: "Cannot find phpunit"
```bash
composer update --ignore-platform-reqs
```

### Tests no se ejecutan
```bash
php vendor/bin/phpunit --version
chmod +x vendor/bin/phpunit
```

---

## 📞 Contacto

Para dudas sobre tests, revisar:
- `tests/Unit/` - Tests unitarios
- `phpunit.xml.dist` - Configuración
- `tests/bootstrap.php` - Bootstrap

Última actualización: 10 de noviembre de 2025
