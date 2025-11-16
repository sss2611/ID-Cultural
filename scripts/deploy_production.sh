#!/bin/bash

###############################################################################
# Script de Despliegue Automático - ID Cultural
# Autor: ID Cultural Team
# Descripción: Despliega la aplicación en servidor de producción
###############################################################################

set -e  # Detener si hay errores

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}"
echo "╔═══════════════════════════════════════════════════════╗"
echo "║     ID CULTURAL - DESPLIEGUE EN PRODUCCIÓN           ║"
echo "╚═══════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Verificar si estamos en el servidor correcto
echo -e "${YELLOW}[1/8] Verificando entorno...${NC}"
if [ ! -d "/home/idcult" ]; then
    echo -e "${RED}❌ No estás en el servidor correcto${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Entorno verificado${NC}"

# Verificar Docker
echo -e "${YELLOW}[2/8] Verificando Docker...${NC}"
if ! command -v docker &> /dev/null; then
    echo -e "${RED}❌ Docker no está instalado${NC}"
    echo "Instalando Docker..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    sudo usermod -aG docker $USER
    echo -e "${GREEN}✓ Docker instalado${NC}"
else
    echo -e "${GREEN}✓ Docker ya está instalado${NC}"
fi

# Verificar Git
echo -e "${YELLOW}[3/8] Verificando Git...${NC}"
if ! command -v git &> /dev/null; then
    echo -e "${RED}❌ Git no está instalado${NC}"
    sudo apt update && sudo apt install -y git
fi
echo -e "${GREEN}✓ Git verificado${NC}"

# Clonar o actualizar repositorio
echo -e "${YELLOW}[4/8] Obteniendo código fuente...${NC}"
cd /home/idcult

if [ -d "ID-Cultural" ]; then
    echo "Repositorio existe, actualizando..."
    cd ID-Cultural
    git fetch origin
    git checkout FINAL
    git pull origin FINAL
else
    echo "Clonando repositorio..."
    git clone https://github.com/runatechdev/ID-Cultural.git
    cd ID-Cultural
    git checkout FINAL
fi
echo -e "${GREEN}✓ Código fuente actualizado${NC}"

# Crear archivo .env
echo -e "${YELLOW}[5/8] Configurando variables de entorno...${NC}"
cat > .env << EOF
DB_HOST=db
DB_USER=runatechdev
DB_PASS=1234
DB_NAME=idcultural
MYSQL_ROOT_PASSWORD=root
BASE_URL=http://100.83.50.21:8080/
ENVIRONMENT=production
EOF
echo -e "${GREEN}✓ Variables de entorno configuradas${NC}"

# Dar permisos
echo -e "${YELLOW}[6/8] Configurando permisos...${NC}"
chmod +x scripts/*.sh
chmod -R 755 public/uploads 2>/dev/null || mkdir -p public/uploads && chmod -R 755 public/uploads
echo -e "${GREEN}✓ Permisos configurados${NC}"

# Detener contenedores anteriores si existen
echo -e "${YELLOW}[7/8] Preparando Docker...${NC}"
docker compose down 2>/dev/null || true

# Levantar contenedores
echo -e "${YELLOW}[8/8] Levantando aplicación...${NC}"
docker compose up -d --build

# Esperar a que la BD esté lista
echo "Esperando a que la base de datos esté lista..."
sleep 10

# Verificar estado
echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✓ DESPLIEGUE COMPLETADO EXITOSAMENTE${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════${NC}"
echo ""

# Mostrar información
echo -e "${YELLOW}📊 Estado de los contenedores:${NC}"
docker compose ps

echo ""
echo -e "${YELLOW}🌐 URLs de acceso:${NC}"
echo -e "  ${GREEN}→ Aplicación:${NC}    http://100.83.50.21:8080"
echo -e "  ${GREEN}→ PhpMyAdmin:${NC}    http://100.83.50.21:8081"
echo ""

echo -e "${YELLOW}📋 Información de la base de datos:${NC}"
echo -e "  ${GREEN}→ Usuario:${NC}        runatechdev"
echo -e "  ${GREEN}→ Contraseña:${NC}     1234"
echo -e "  ${GREEN}→ Base de datos:${NC}  idcultural"
echo ""

echo -e "${YELLOW}🔧 Comandos útiles:${NC}"
echo -e "  ${GREEN}→ Ver logs:${NC}           docker compose logs -f"
echo -e "  ${GREEN}→ Reiniciar:${NC}          docker compose restart"
echo -e "  ${GREEN}→ Detener:${NC}            docker compose down"
echo -e "  ${GREEN}→ Ver BD:${NC}             docker exec -it idcultural_db mysql -u runatechdev -p1234 idcultural"
echo ""

# Verificar tablas
echo -e "${YELLOW}🗄️  Verificando base de datos...${NC}"
TABLES=$(docker exec idcultural_db mysql -u runatechdev -p1234 -D idcultural -e "SHOW TABLES;" 2>/dev/null | wc -l)
if [ $TABLES -gt 1 ]; then
    echo -e "${GREEN}✓ Base de datos importada correctamente ($((TABLES-1)) tablas)${NC}"
else
    echo -e "${YELLOW}⚠️  Base de datos vacía, importando...${NC}"
    docker exec -i idcultural_db mysql -u runatechdev -p1234 idcultural < database/idcultural_export.sql
    echo -e "${GREEN}✓ Base de datos importada${NC}"
fi

echo ""
echo -e "${GREEN}🎉 ¡Todo listo! Accede a http://100.83.50.21:8080${NC}"
echo ""
