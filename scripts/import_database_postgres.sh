#!/bin/bash

# Script para importar la base de datos en Render (PostgreSQL)
# Uso: ./scripts/import_database_postgres.sh

set -e

echo "🔄 Importando base de datos en Render (PostgreSQL)..."

IMPORT_FILE="database/idcultural_export.sql"

if [ ! -f "$IMPORT_FILE" ]; then
    echo "❌ Error: El archivo $IMPORT_FILE no existe"
    exit 1
fi

if [ -z "$DATABASE_URL" ]; then
    echo "❌ Error: DATABASE_URL no está configurada"
    exit 1
fi

echo "📥 Importando datos..."
psql "$DATABASE_URL" < "$IMPORT_FILE"

echo "✅ Base de datos importada exitosamente"
