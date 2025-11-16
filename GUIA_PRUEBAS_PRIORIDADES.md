# 🧪 Guía de Pruebas - Prioridades Altas

## 1. 📧 **Prueba: Envío de Emails**

### Requisitos:
- MailHog debe estar corriendo en Docker
- Acceder a: http://localhost:8025

### Test Registro:
```bash
curl -X POST http://localhost:8080/backend/controllers/procesar_registro.php \
  -d "nombre=Juan&apellido=Pérez&email=juan@test.com&password=123456&fechaNacimiento=1990-01-01&genero=M&pais=Argentina&provincia=Santiago&municipio=Capital"
```

Verificar en MailHog que se recibió email de bienvenida.

---

## 2. 🔐 **Prueba: Recuperación de Contraseña**

### Test Solicitar Recuperación:
```bash
curl -X POST http://localhost:8080/backend/controllers/solicitar_recuperacion_clave.php \
  -d "email=juan@test.com"
```

Verificar en MailHog el email con enlace de recuperación.

### Test Cambiar Contraseña:
1. Copiar token del email
2. Acceder a: `http://localhost:8080/recuperar-clave.php?token=TOKEN_AQUI`
3. Ingresar nueva contraseña
4. Verificar que se puede loguear con la nueva contraseña

---

## 3. 📄 **Prueba: Paginación en Búsqueda**

### Test sin paginación:
```bash
curl -s "http://localhost:8080/busqueda.php?q=remix" | grep -c "page-item"
```

Debería mostrar números de página si hay más de 12 resultados.

### Test con categoría:
```bash
curl -s "http://localhost:8080/busqueda.php?categoria=Música&pagina=1" | grep "titulo-resultados"
```

---

## 4. 🛡️ **Prueba: Validación de Multimedia**

### Test Validador:
```php
<?php
require_once 'backend/helpers/MultimediaValidator.php';

// Simular upload
if ($_FILES['imagen']) {
    $resultado = MultimediaValidator::guardarArchivo($_FILES['imagen'], 'imagen');
    echo json_encode($resultado);
}
```

### Casos de Prueba:
- ✅ Subir imagen válida (JPG)
- ❌ Subir archivo > 5MB (debe fallar)
- ❌ Subir archivo < 200x200px (debe fallar)
- ❌ Subir archivo no imagen (debe fallar)

---

## 5. ✅ **Prueba: Aprobación de Perfil con Email**

### Requisitos:
- Tener un artista en estado 'pendiente'
- Session de admin/validador activa

### Simular aprobación:
```bash
curl -X POST http://localhost:8080/backend/controllers/aprobar_perfil.php \
  -d "id=8" \
  -H "Cookie: PHPSESSID=TU_SESSION_ID"
```

Verificar en MailHog que se recibió email de aprobación.

---

## 6. 📋 **Prueba: Actualización de Estado de Obra**

### Test Publicar Obra:
```bash
curl -X POST http://localhost:8080/backend/controllers/actualizar_estado.php \
  -d "id=3&estado=publicada" \
  -H "Cookie: PHPSESSID=TU_SESSION_ID"
```

Verificar en MailHog que se recibió email de obra publicada.

### Test Rechazar Obra:
```bash
curl -X POST http://localhost:8080/backend/controllers/actualizar_estado.php \
  -d "id=3&estado=rechazada" \
  -H "Cookie: PHPSESSID=TU_SESSION_ID"
```

Verificar en MailHog que se recibió email de rechazo.

---

## 🐛 **Debugging**

### Ver logs de PHP:
```bash
docker logs idcultural_web 2>&1 | tail -50
```

### Ver emails en MailHog:
http://localhost:8025/

### Verificar tabla:
```bash
docker exec idcultural_db mysql -u root -proot idcultural -e "SELECT * FROM password_reset_tokens LIMIT 5;"
```

---

## ✅ **Checklist de Validación**

- [ ] Email de bienvenida se envía al registrar
- [ ] Enlace de recuperación funciona y expira correctamente
- [ ] Contraseña se actualiza correctamente
- [ ] Paginación muestra resultados correctamente
- [ ] Validador rechaza archivos inválidos
- [ ] Email de aprobación se envía
- [ ] Email de obra publicada se envía
- [ ] Email de obra rechazada se envía
- [ ] Búsqueda por texto funciona
- [ ] Búsqueda por categoría funciona
- [ ] Búsqueda con paginación funciona

---

**Nota:** Todos los emails se pueden ver en http://localhost:8025
