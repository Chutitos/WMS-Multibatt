---
name: laravel-architect
description: "Arquitectura Laravel para módulos WMS: rutas, controllers, models, migrations, requests, services, policies and conventions."
argument-hint: "[módulo o refactor]"
---

# Laravel Architect

Tarea: `$ARGUMENTS`

Revisa y diseña cambios Laravel con estructura limpia.

Checklist:
- Rutas agrupadas por middleware y rol.
- Controladores delgados.
- Form Requests para validación no trivial.
- Services/Actions para lógica de negocio.
- Modelos con relaciones y casts.
- Policies/Gates para permisos.
- Migraciones reversibles.
- Tests de feature para flujos críticos.

Salida:
- Diseño propuesto.
- Archivos a tocar.
- Riesgos.
- Plan de implementación.

