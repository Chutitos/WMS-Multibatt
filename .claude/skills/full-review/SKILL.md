---
name: full-review
description: "Revisión completa del WMS combinando arquitectura Laravel, base de datos, seguridad, frontend, QA, performance y Git. Use when the user asks for a complete audit or wants to use all relevant skills together."
argument-hint: "[módulo, flujo o cambios a revisar]"
---

# Full Review WMS

Tarea: `$ARGUMENTS`

Actúa como orquestador senior y revisa el alcance indicado desde todas las áreas relevantes. No cargues cambios innecesarios; entrega un informe consolidado.

## Fases
1. Arquitectura Laravel: rutas, controladores, modelos, services, requests, policies.
2. Base de datos: tablas, migraciones, relaciones, índices, integridad.
3. WMS: stock, FIFO, trazabilidad, flujo operativo y roles.
4. Seguridad: auth, permisos, validaciones, secretos, mass assignment.
5. Frontend/UX: claridad para bodega, estados, botones, mensajes.
6. QA: pruebas existentes, pruebas faltantes, casos borde.
7. Performance: consultas, N+1, paginación, índices.
8. Git/release: cambios pendientes, riesgos, commit sugerido.

## Salida obligatoria
- Resumen ejecutivo.
- Hallazgos por prioridad: Crítico, Alto, Medio, Bajo.
- Archivos relevantes.
- Plan de corrección en pasos pequeños.
- Pruebas manuales y automatizadas sugeridas.
- Commit message sugerido.

Si falta contexto, primero inspecciona archivos. Si la revisión puede ser destructiva, detente y pide confirmación.

