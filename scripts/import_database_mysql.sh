#!/bin/bash

# Script para importar la base de datos en Render (MySQL)
# Uso: ./scripts/import_database_mysql.sh

set -e

echo "🔄 Importando base de datos en Render (MySQL)..."

IMPORT_FILE="database/idcultural_export.sql"

if [ ! -f "$IMPORT_FILE" ]; then
    echo "❌ Error: El archivo $IMPORT_FILE no existe"
    exit 1
fi

if [ -z "$DATABASE_URL" ]; then
    echo "❌ Error: DATABASE_URL no está configurada"
    exit 1
fi

# Parsear DATABASE_URL (mysql://user:pass@host:port/dbname)
DB_USER=$(echo $DATABASE_URL | sed -E 's/^mysql:\/\/([^:]+):.*$/\1/')
DB_PASS=$(echo $DATABASE_URL | sed -E 's/^mysql:\/\/[^:]+:([^@]+)@.*$/\1/')
DB_HOST=$(echo $DATABASE_URL | sed -E 's/^mysql:\/\/[^@]+@([^:]+):.*$/\1/')
DB_PORT=$(echo $DATABASE_URL | sed -E 's/^mysql:\/\/[^@]+@[^:]+:([0-9]+)\/.*$/\1/')
DB_NAME=$(echo $DATABASE_URL | sed -E 's/^mysql:\/\/[^@]+@[^:]+:[0-9]+\/([^?]+).*$/\1/')

echo "📥 Importando datos..."
mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -p$DB_PASS $DB_NAME < "$IMPORT_FILE"

echo "✅ Base de datos importada exitosamente"
