---
name: test-data-seeder
description: "Crea seeders/factories seguros para datos locales del WMS: roles, usuarios, productos, órdenes and stock de prueba."
argument-hint: "[datos requeridos]"
---

# Test Data Seeder

Datos: `$ARGUMENTS`

Crea datos locales repetibles.

Incluye cuando aplique:
- Roles mínimos.
- Usuarios de prueba.
- Productos/SKUs.
- Ubicaciones.
- Stock inicial.
- Órdenes en distintos estados.

Reglas:
- No usar contraseñas reales.
- No meter datos sensibles.
- Mantener seeders idempotentes si es posible.
- Documentar cómo ejecutarlos.

