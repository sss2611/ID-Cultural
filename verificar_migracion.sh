#!/bin/bash

# ╔═══════════════════════════════════════════════════════════════════════════╗
# ║        CHECKLIST DE MIGRACIÓN DE APIS - ID CULTURAL                      ║
# ║        Script para verificar el estado de la migración                    ║
# ╚═══════════════════════════════════════════════════════════════════════════╝

echo "
╔═══════════════════════════════════════════════════════════════════════════╗
║        🔍 VERIFICACIÓN DE INTEGRIDAD DE MIGRACIÓN                         ║
║                                                                           ║
║        Este script verifica que la migración de APIs fue correcta          ║
╚═══════════════════════════════════════════════════════════════════════════╝
"

API_PATH="/home/runatechdev/Documentos/Github/ID-Cultural/public/api"
JS_PATH="/home/runatechdev/Documentos/Github/ID-Cultural/public/static/js"
PROJECT_PATH="/home/runatechdev/Documentos/Github/ID-Cultural"

# Colores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}1. VERIFICANDO CRUDS UNIFICADOS...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

CRUDS=("artistas.php" "personal.php" "borradores.php" "solicitudes.php" "noticias.php" "site_content.php")
MISSING_CRUDS=0

for crud in "${CRUDS[@]}"; do
    if [ -f "$API_PATH/$crud" ]; then
        echo -e "${GREEN}✓${NC} $crud existe"
    else
        echo -e "${RED}✗${NC} $crud FALTA"
        MISSING_CRUDS=$((MISSING_CRUDS+1))
    fi
done

echo ""
echo -e "${BLUE}2. VERIFICANDO APIS MANTIDAS...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

KEPT_APIS=("login.php" "get_estadisticas_inicio.php" "get_estadisticas_validador.php" "get_logs.php" "get_publicaciones.php" "get_publicacion_detalle.php" "validar_publicacion.php")
MISSING_APIS=0

for api in "${KEPT_APIS[@]}"; do
    if [ -f "$API_PATH/$api" ]; then
        echo -e "${GREEN}✓${NC} $api existe"
    else
        echo -e "${RED}✗${NC} $api FALTA"
        MISSING_APIS=$((MISSING_APIS+1))
    fi
done

echo ""
echo -e "${BLUE}3. VERIFICANDO APIS ELIMINADAS (NO deben existir)...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

DELETED_APIS=("add_personal.php" "delete_personal.php" "update_personal.php" "get_personal.php" "add_noticia.php" "edit_noticia.php" "delete_noticia.php" "get_noticias.php" "get_noticia_detalle.php" "delete_artista.php" "update_artista_status.php" "get_artistas.php" "get_artist_stats.php" "get_mis_borradores.php" "get_mis_solicitudes.php" "save_borrador.php" "delete_publicacion.php" "get_solicitudes.php" "update_solicitud.php" "register_artista.php" "update_noticia.php" "get_site_content.php" "update_site_content.php")

FOUND_DELETED=0
for api in "${DELETED_APIS[@]}"; do
    if [ -f "$API_PATH/$api" ]; then
        echo -e "${RED}✗${NC} $api ENCONTRADA (debe ser eliminada)"
        FOUND_DELETED=$((FOUND_DELETED+1))
    fi
done

if [ $FOUND_DELETED -eq 0 ]; then
    echo -e "${GREEN}✓${NC} Todas las APIs antiguas han sido eliminadas"
fi

echo ""
echo -e "${BLUE}4. VERIFICANDO DOCUMENTACIÓN...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

DOCS=("API_DOCUMENTATION.md" "RESUMEN_MIGRACION_APIS.txt" "GUIA_ACTUALIZACION_JS.md" "ESTADO_ACTUAL_APIS.txt")
MISSING_DOCS=0

for doc in "${DOCS[@]}"; do
    if [ -f "$PROJECT_PATH/$doc" ] || [ -f "$API_PATH/$doc" ]; then
        echo -e "${GREEN}✓${NC} $doc existe"
    else
        echo -e "${YELLOW}⚠${NC} $doc no encontrada"
        MISSING_DOCS=$((MISSING_DOCS+1))
    fi
done

echo ""
echo -e "${BLUE}5. CONTEO GENERAL DE ARCHIVOS...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

TOTAL_PHP=$(ls -1 "$API_PATH"/*.php 2>/dev/null | wc -l)
echo -e "Total de archivos .php en /api/: ${BLUE}$TOTAL_PHP${NC}"

if [ $TOTAL_PHP -le 15 ]; then
    echo -e "${GREEN}✓${NC} Cantidad adecuada (debe ser ≤ 15)"
else
    echo -e "${RED}✗${NC} Demasiados archivos (debe ser ≤ 15)"
fi

echo ""
echo -e "${BLUE}6. BUSCANDO REFERENCIAS A APIS ANTIGUAS EN JAVASCRIPT...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

GET_REFS=$(grep -r "api/get_" "$JS_PATH" 2>/dev/null | grep -v "get_estadisticas\|get_logs\|get_publicaciones\|get_publicacion_detalle" | wc -l)
ADD_REFS=$(grep -r "api/add_" "$JS_PATH" 2>/dev/null | wc -l)
DELETE_REFS=$(grep -r "api/delete_" "$JS_PATH" 2>/dev/null | grep -v "action.*delete" | wc -l)
UPDATE_REFS=$(grep -r "api/update_" "$JS_PATH" 2>/dev/null | grep -v "action.*update" | wc -l)
SAVE_REFS=$(grep -r "api/save_" "$JS_PATH" 2>/dev/null | wc -l)

echo "Referencias a 'api/get_*': $GET_REFS"
echo "Referencias a 'api/add_*': $ADD_REFS"
echo "Referencias a 'api/delete_*': $DELETE_REFS"
echo "Referencias a 'api/update_*': $UPDATE_REFS"
echo "Referencias a 'api/save_*': $SAVE_REFS"

OLD_REFS=$((GET_REFS + ADD_REFS + DELETE_REFS + UPDATE_REFS + SAVE_REFS))

if [ $OLD_REFS -eq 0 ]; then
    echo -e "${GREEN}✓${NC} No hay referencias a APIs antiguas"
else
    echo -e "${YELLOW}⚠${NC} Hay $OLD_REFS referencias a APIs antiguas que necesitan actualización"
fi

echo ""
echo -e "${BLUE}7. RESUMEN GENERAL...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

TOTAL_ERRORS=$((MISSING_CRUDS + MISSING_APIS + FOUND_DELETED))

if [ $TOTAL_ERRORS -eq 0 ] && [ $OLD_REFS -eq 0 ]; then
    echo -e "${GREEN}✅ MIGRACIÓN COMPLETADA Y VERIFICADA${NC}"
    echo ""
    echo "Estado: LISTO PARA PROBAR"
    exit 0
else
    echo -e "${YELLOW}⚠️  PENDIENTE DE COMPLETAR${NC}"
    echo ""
    echo "Problemas encontrados:"
    [ $MISSING_CRUDS -gt 0 ] && echo "  - CRUDs faltantes: $MISSING_CRUDS"
    [ $MISSING_APIS -gt 0 ] && echo "  - APIs mantidas faltantes: $MISSING_APIS"
    [ $FOUND_DELETED -gt 0 ] && echo "  - APIs antiguas aún presentes: $FOUND_DELETED"
    [ $OLD_REFS -gt 0 ] && echo "  - Referencias a APIs antiguas en JS: $OLD_REFS"
    exit 1
fi
