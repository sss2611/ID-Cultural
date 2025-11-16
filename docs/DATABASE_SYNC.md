# 🗄️ Sincronización de Base de Datos - ID Cultural

## Descripción

Este proyecto implementa un sistema de sincronización de base de datos entre el entorno local (Docker) y repositorio de GitHub, optimizado para despliegues en servidores via Tailscale.

## 📋 Estructura

- **`database/idcultural_export.sql`** - Snapshot actual de la BD (se actualiza manualmente)
- **`scripts/export_database.sh`** - Exporta BD desde Docker a SQL
- **`scripts/import_database.sh`** - Importa BD desde SQL en Docker
- **`docker-compose.yml`** - Configura restauración automática al iniciar

## 🚀 Flujo de Sincronización

### 1️⃣ En Desarrollo Local (Linux)

#### Exportar BD actual:
```bash
chmod +x scripts/export_database.sh
./scripts/export_database.sh
```

**Resultado:** Se crea/actualiza `database/idcultural_export.sql`

#### Agregar al repositorio:
```bash
git add database/idcultural_export.sql
git commit -m "Sincronizar BD: [DESCRIPCIÓN DE CAMBIOS]"
git push origin FINAL
```

### 2️⃣ En Servidor (Tailscale/Producción)

#### Clonar el repositorio:
```bash
git clone https://github.com/runatechdev/ID-Cultural.git
cd ID-Cultural
```

#### Levantar Docker (restaura BD automáticamente):
```bash
docker-compose up -d
```

> ✅ Docker automaticamente detectará `database/idcultural_export.sql` y restaurará la BD

#### (Opcional) Si necesitas importar manualmente:
```bash
chmod +x scripts/import_database.sh
./scripts/import_database.sh
```

## 📊 Credenciales por Defecto

```
BD: idcultural
Usuario: runatechdev
Contraseña: 1234
Host: db (en Docker) o IP_TAILSCALE:3306 (en servidor)
```

## 🔧 Configuración en Servidor Tailscale

### 1. Actualizar config.php para producción:

```php
<?php
// config.php - Producción
define('BASE_URL', 'http://IP_TAILSCALE/');
// o
define('BASE_URL', 'http://id-cultural.nombre-red-tailscale/');
?>
```

### 2. Configurar acceso a MySQL:

```php
// backend/config/connection.php
$host = $_ENV['DB_HOST'] ?? 'db'; // 'db' en Docker, o IP Tailscale
$user = $_ENV['DB_USER'] ?? 'runatechdev';
$pass = $_ENV['DB_PASS'] ?? '1234';
$db = 'idcultural';
```

### 3. (Producción) Usar variables de entorno:

```bash
# En servidor Tailscale - Crear .env
DB_HOST=localhost
DB_USER=runatechdev
DB_PASS=1234
MYSQL_ROOT_PASSWORD=root
```

## ⚠️ Flujo de Cambios en BD

### Escenario: Agregar una tabla nueva

1. **En local:** 
   - Crear tabla en phpmyadmin (http://localhost:8081)
   - Exportar: `./scripts/export_database.sh`
   - Hacer commit: `git add database/idcultural_export.sql && git commit -m "Agregar tabla xxx"`

2. **En servidor:**
   - Pull: `git pull origin FINAL`
   - Reiniciar Docker: `docker-compose restart db`
   - O reimportar: `./scripts/import_database.sh`

## 📅 Recomendaciones

✅ **Exportar la BD:**
- Después de cambios importantes en estructura
- Antes de pushear a GitHub
- Regularmente (ej: daily cron job)

❌ **NO exportar:**
- Datos de prueba sin limpiar
- Información sensible sin ofuscar

## 🔄 Backup Automático (Opcional)

Para agregar backup automático diario, crear cron job:

```bash
crontab -e
# Agregar línea:
0 2 * * * cd /home/runatechdev/Documentos/Github/ID-Cultural && ./scripts/export_database.sh && git add database/idcultural_export.sql && git commit -m "Backup automático BD $(date +\%Y-\%m-\%d)" && git push origin FINAL
```

## 🆘 Troubleshooting

### Error: "El archivo idcultural_export.sql no existe"
```bash
# Solución: Exportar primero
./scripts/export_database.sh
```

### Error: "mysqladmin: command not found"
```bash
# Solución: Docker debe tener MySQL client
# Ya viene incluido en mariadb:10.5
```

### BD vacía después de restaurar
```bash
# Verificar que el volumen de Docker se inicializó:
docker volume ls | grep db_data

# Si es necesario, limpiar y reintentar:
docker-compose down -v
docker-compose up -d
```

## 📞 Contacto

Para preguntas sobre sincronización de BD, revisar logs:
```bash
docker logs idcultural_db
docker logs idcultural_web
```
