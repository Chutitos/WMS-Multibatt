---
name: release-checklist
description: "Checklist final antes de entregar cambios WMS: build, tests, migrations, env, docs, git status and manual validation."
argument-hint: "[release o cambio]"
---

# Release Checklist

Cambio: `$ARGUMENTS`

Verifica:
- `composer install` no requerido o documentado.
- `npm run build` OK si cambió frontend.
- `php artisan test` o pruebas manuales documentadas.
- Migraciones revisadas.
- `.env` no modificado accidentalmente.
- `git status` limpio o entendido.
- README actualizado si cambió instalación/uso.
- Rollback o riesgo conocido.

Entrega checklist con OK/Pendiente y recomendación final.

