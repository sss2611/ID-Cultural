#!/bin/bash

# 🔍 Script de Verificación Rápida del Sistema ID-Cultural
# Ejecuta las principales verificaciones desde la nueva estructura organizada

echo "🎭 ID-Cultural - Verificación del Sistema"
echo "========================================"
echo

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para ejecutar verificaciones
run_check() {
    local script_path=$1
    local description=$2
    
    echo -e "${BLUE}🔍 Ejecutando: ${description}${NC}"
    
    if [ -f "$script_path" ]; then
        php "$script_path"
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✅ ${description} - OK${NC}"
        else
            echo -e "${RED}❌ ${description} - ERROR${NC}"
        fi
    else
        echo -e "${RED}❌ Archivo no encontrado: $script_path${NC}"
    fi
    echo "----------------------------------------"
}

echo -e "${YELLOW}📊 Verificaciones de Sistema${NC}"
echo

# Verificaciones principales
run_check "utils/checks/check_users.php" "Verificación de Usuarios"
run_check "utils/checks/check_obras.php" "Verificación de Obras"
run_check "utils/checks/check_session.php" "Verificación de Sesiones"
run_check "utils/checks/check_multimedia.php" "Verificación de Multimedia"

echo
echo -e "${YELLOW}🐛 Inspección de Base de Datos${NC}"
echo

run_check "utils/debug/inspect_db.php" "Inspección General de BD"
run_check "utils/debug/inspect_artistas.php" "Inspección de Artistas"

echo
echo -e "${YELLOW}🧪 Tests Rápidos${NC}"
echo

# Tests rápidos de API
echo -e "${BLUE}🌐 Probando APIs...${NC}"
if [ -f "testing/manual/test_apis.sh" ]; then
    cd testing/manual && ./test_apis.sh
    cd ../..
else
    echo -e "${RED}❌ Script test_apis.sh no encontrado${NC}"
fi

echo
echo -e "${GREEN}🎉 Verificación completada!${NC}"
echo
echo "📋 Para más opciones:"
echo "  - Ver utils/README.md para herramientas disponibles"
echo "  - Ver testing/README.md para tests manuales"
echo "  - Ejecutar 'composer test' para tests automatizados"