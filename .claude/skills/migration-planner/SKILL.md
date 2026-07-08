---
name: migration-planner
description: "Planifica migraciones Laravel seguras para cambios de schema, datos existentes, rollback and deployment order."
argument-hint: "[cambio de base de datos]"
---

# Migration Planner

Cambio: `$ARGUMENTS`

Diseña migración segura:
- Impacto en datos existentes.
- Nullable/defaults.
- Índices.
- Foreign keys.
- Backfill si aplica.
- Rollback realista.
- Orden de despliegue.

No propongas `migrate:fresh` como solución normal.

