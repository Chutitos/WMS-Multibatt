---
name: implement-feature
description: "Implementa una funcionalidad WMS en Laravel de forma planificada y segura. Use for new features or medium changes that require reading, editing and testing."
argument-hint: "[funcionalidad a implementar]"
---

# Implement Feature

Funcionalidad: `$ARGUMENTS`

## Workflow
1. Entender alcance y entidades afectadas.
2. Revisar rutas, controladores, modelos, vistas y migraciones relacionadas.
3. Proponer plan corto antes de editar.
4. Implementar por capas: migración/modelo, lógica, validación, UI, tests.
5. Ejecutar validaciones razonables si el entorno lo permite.
6. Entregar resumen y commit sugerido.

## Reglas
- No cambies la BD sin migración o explicación.
- No mezcles limpieza grande con la funcionalidad.
- Mantén compatibilidad con datos existentes.
- Para stock/órdenes/eventos, usa transacciones.
- Para permisos, agrega middleware/policy/gate según corresponda.

