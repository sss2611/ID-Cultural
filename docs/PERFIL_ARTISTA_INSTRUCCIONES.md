# Perfil de Artista - Instrucciones de Uso

## Descripción

`perfil_artista.php` es ahora una plantilla dinámica que muestra el perfil de cualquier artista validado en la plataforma ID Cultural.

## Reglas de Acceso

- **Solo artistas validados** pueden tener perfil público
- Un artista se considera "validado" cuando:
  - Ha enviado una obra (publicación)
  - La obra ha sido validada/aprobada
  - Una vez validado, el perfil es público (sin importar si obras posteriores son rechazadas)

## URL y Parámetros

```
/public/perfil_artista.php?id=ARTISTA_ID
```

### Ejemplo:
```
https://tudominio.com/perfil_artista.php?id=5
```

## Estructura de la URL

- **`id`** (obligatorio, integer): ID del artista en la base de datos (tabla `artistas`)

## Comportamiento

### Si el ID es válido:
- Muestra el perfil completo del artista con:
  - Foto de perfil
  - Nombre y apellido
  - Disciplina/profesión
  - Enlaces a redes sociales (si están configurados)
  - Biografía
  - Botón "Editar Perfil" (solo visible si es el artista logueado)

### Si el ID es inválido o el artista no está validado:
- Redirige a `/index.php`

### Si el artista logueado es el dueño del perfil:
- Muestra un botón "Editar Perfil" que lo lleva a `/src/views/pages/editar-perfil.php`

## Campos Necesarios en la Tabla `artistas`

```sql
- id (int, primary key)
- nombre (varchar)
- apellido (varchar)
- email (varchar)
- disciplina (varchar) -- profesión o disciplina artística
- status (enum: 'pendiente', 'validado', 'rechazado')
- foto_perfil (varchar) -- ruta a la imagen
- biografia (text) -- descripción del artista
- instagram (varchar) -- URL
- twitter (varchar) -- URL
- facebook (varchar) -- URL
- sitio_web (varchar) -- URL
```

## Características Dinámicas

### Redes Sociales
- Solo se muestran los botones de redes sociales configurados
- Soporta: Instagram, Twitter, Facebook, Sitio Web
- Los enlaces se abren en una nueva pestaña

### Editar Perfil
- Solo visible si:
  - El usuario está logueado
  - El usuario es un artista
  - El usuario es el propietario del perfil

### Estadísticas
- Cuenta automáticamente obras validadas y total de obras
- Disponibles en la variable `$artista['obras_validadas']` y `$artista['total_obras']`

## Integración con el Sistema

### Desde la página de Wiki:
```php
// Mostrar enlace al perfil de un artista
<a href="/perfil_artista.php?id=<?php echo $artista['id']; ?>">
    <?php echo htmlspecialchars($artista['nombre'] . ' ' . $artista['apellido']); ?>
</a>
```

### Desde listados de artistas:
```php
foreach ($artistas as $artista) {
    echo '<a href="/perfil_artista.php?id=' . $artista['id'] . '">';
    echo htmlspecialchars($artista['nombre']);
    echo '</a>';
}
```

## Seguridad

- ✅ Validación de ID (solo números)
- ✅ Prevención de SQL Injection (prepared statements)
- ✅ Escapado de HTML (htmlspecialchars)
- ✅ Solo artistas validados pueden tener perfil público
- ✅ Verificación de permisos para editar

## Próximas mejoras

- [ ] Agregar galería de obras (filtradas por estado)
- [ ] Mostrar estadísticas de visualizaciones
- [ ] Permitir comentarios o valoraciones
- [ ] Compartir perfil en redes sociales
- [ ] Sistema de seguimiento (follow/unfollow)
