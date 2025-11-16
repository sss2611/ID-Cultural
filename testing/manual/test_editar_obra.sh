#!/bin/bash

# Test directo del endpoint de edición de obra

curl -X POST "http://localhost:8080/api/borradores.php" \
  -H "Cookie: PHPSESSID=$(uuidgen)" \
  -F "action=update" \
  -F "id=2" \
  -F "titulo=Test Edición" \
  -F "descripcion=Descripción actualizada" \
  -F "categoria=musica" \
  2>&1 | jq .

