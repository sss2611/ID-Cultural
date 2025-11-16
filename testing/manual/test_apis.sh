#!/bin/bash

echo "🧪 TESTING DE APIs - ID Cultural"
echo "=================================="
echo ""

BASE_URL="http://localhost:8080/api"

# Test 1: Obtener artistas
echo "1️⃣ TEST: Obtener artistas (GET /artistas.php)"
curl -s "$BASE_URL/artistas.php?action=get" | jq . | head -20
echo ""
echo "✅ Artistas obtenidos correctamente"
echo ""

# Test 2: Obtener estadísticas de inicio
echo "2️⃣ TEST: Estadísticas de inicio"
curl -s "$BASE_URL/get_estadisticas_inicio.php" | jq . 
echo ""

# Test 3: Obtener obras wiki
echo "3️⃣ TEST: Obtener obras wiki"
curl -s "$BASE_URL/get_obras_wiki.php" | jq . | head -30
echo ""

# Test 4: Obtener publicaciones
echo "4️⃣ TEST: Obtener publicaciones"
curl -s "$BASE_URL/get_publicaciones.php" | jq . 2>/dev/null | head -20
echo ""

echo "✅ Testing completado"
