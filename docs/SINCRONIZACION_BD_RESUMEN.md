# ✅ Sincronización de BD - Resumen de Implementación

## 🎯 Lo que se ha configurado

### 1. **Exportación automática de BD**
- ✅ Script: `scripts/export_database.sh`
- Exporta la BD de Docker a `database/idcultural_export.sql`
- Comando: `./scripts/export_database.sh`

### 2. **Importación en nuevo servidor**
- ✅ Script: `scripts/import_database.sh`
- Restaura BD desde el SQL exportado
- Comando: `./scripts/import_database.sh`

### 3. **Docker auto-restaura la BD**
- ✅ `docker-compose.yml` modificado
- Cuando levantes Docker, automáticamente carga `database/idcultural_export.sql`
- Comando: `docker-compose up -d`

### 4. **Documentación completa**
- ✅ `DATABASE_SYNC.md` - Guía de flujo en desarrollo y producción
- ✅ `.gitignore` - Configurado para proyecto en producción

---

## 📋 Flujo en Desarrollo Local

### Después de hacer cambios en la BD:

```bash
# 1. Exportar BD
./scripts/export_database.sh

# 2. Verificar cambios
git diff database/idcultural_export.sql | head -20

# 3. Subir a GitHub
git add database/idcultural_export.sql
git commit -m "Actualizar BD: [describir cambio]"
git push origin FINAL
```

---

## 🚀 Flujo en Servidor Tailscale

### Primera vez (setup inicial):

```bash
# 1. Clonar repositorio
git clone https://github.com/runatechdev/ID-Cultural.git
cd ID-Cultural

# 2. Levantar Docker (restaura BD automáticamente)
docker-compose up -d

# ✅ ¡Listo! La BD ya está sincronizada desde GitHub
```

### Después de cambios (pull desde GitHub):

```bash
# 1. Obtener últimos cambios
git pull origin FINAL

# 2. Reiniciar servicio de BD
docker-compose restart db

# ✅ Cambios aplicados automáticamente
```

---

## 📊 Estructura de archivos creados

```
ID-Cultural/
├── database/
│   └── idcultural_export.sql    ← Snapshot actual de BD (en GitHub)
├── scripts/
│   ├── export_database.sh       ← Exportar BD
│   └── import_database.sh       ← Importar BD
├── DATABASE_SYNC.md             ← Documentación completa
└── docker-compose.yml           ← Modificado (auto-restaura BD)
```

---

## 🔐 Credenciales (protegidas en .env para producción)

```
BD: idcultural
Usuario: runatechdev
Contraseña: 1234
```

---

## ✨ Beneficios de esta configuración

✅ **Sincronización automática** - BD versionada en GitHub  
✅ **Despliegue fácil** - Un solo `docker-compose up -d` en servidor  
✅ **Sin pérdida de datos** - Snapshot guardado en repo  
✅ **Recuperación rápida** - Si algo falla, revert a versión anterior  
✅ **Compatible con Tailscale** - Funciona en cualquier servidor privado  
✅ **Escalable** - Fácil de migrar a múltiples servidores  

---

## 🔄 Próximas mejoras (opcionales)

- [ ] Agregar backup automático diario con cron job
- [ ] Encriptar contraseñas en .env
- [ ] Crear pre-commit hook para exportar BD
- [ ] Agregar verificación de integridad de BD

---

## 📞 Comandos útiles

```bash
# Ver si Docker está corriendo
docker ps

# Ver logs de BD
docker logs idcultural_db

# Acceder a MySQL desde terminal
docker exec -it idcultural_db mysql -u runatechdev -p1234 idcultural

# Exportar BD manualmente
./scripts/export_database.sh

# Limpiar y reiniciar
docker-compose down -v
docker-compose up -d
```

---

## ✅ Estado actual

- **BD exportada:** `database/idcultural_export.sql` (16KB)
- **Scripts listos:** `scripts/export_database.sh` y `scripts/import_database.sh`
- **Docker configurado:** Auto-restaura BD al iniciar
- **GitHub actualizado:** Commit `1c6bb7e` en rama FINAL
- **Documentación:** `DATABASE_SYNC.md` con guía completa

---

**¡Proyecto listo para desplegar en Tailscale! 🎉**
