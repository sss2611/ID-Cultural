#!/bin/bash

# Script para importar la base de datos en Docker
# Uso: ./scripts/import_database.sh

set -e

echo "🔄 Importando base de datos en Docker..."

DB_CONTAINER="idcultural_db"
DB_USER="runatechdev"
DB_PASSWORD="1234"
DB_NAME="idcultural"
IMPORT_FILE="database/idcultural_export.sql"

# Verificar que el archivo existe
if [ ! -f "$IMPORT_FILE" ]; then
    echo "❌ Error: El archivo $IMPORT_FILE no existe"
    exit 1
fi

# Esperar a que MySQL esté listo
echo "⏳ Esperando a que MySQL esté listo..."
docker exec $DB_CONTAINER mysqladmin ping -u $DB_USER -p$DB_PASSWORD --silent 2>/dev/null || sleep 5

# Importar la base de datos
echo "📥 Importando datos..."
docker exec -i $DB_CONTAINER mysql \
    -u $DB_USER \
    -p$DB_PASSWORD \
    $DB_NAME < $IMPORT_FILE

echo "✅ Base de datos importada exitosamente"
echo "📊 Registros en la BD:"
docker exec $DB_CONTAINER mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME -e "SHOW TABLES; SELECT TABLE_NAME, TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$DB_NAME';"
