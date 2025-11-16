#!/bin/bash

# Script para exportar la base de datos desde Docker
# Uso: ./scripts/export_database.sh

set -e

echo "🔄 Exportando base de datos desde Docker..."

# Obtener el nombre del contenedor
DB_CONTAINER="idcultural_db"
DB_USER="runatechdev"
DB_PASSWORD="1234"
DB_NAME="idcultural"
EXPORT_FILE="database/idcultural_export.sql"

# Crear el directorio si no existe
mkdir -p "$(dirname "$EXPORT_FILE")"

# Exportar la base de datos
docker exec $DB_CONTAINER mysqldump \
    -u $DB_USER \
    -p$DB_PASSWORD \
    $DB_NAME > $EXPORT_FILE

echo "✅ Base de datos exportada exitosamente a: $EXPORT_FILE"
echo "📊 Tamaño del archivo: $(du -h $EXPORT_FILE | cut -f1)"

# Mostrar los últimos cambios
echo ""
echo "📝 Últimas líneas del archivo:"
tail -5 $EXPORT_FILE
