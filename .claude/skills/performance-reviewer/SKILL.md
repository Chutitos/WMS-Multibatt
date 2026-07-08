---
name: performance-reviewer
description: "Revisa rendimiento Laravel/WMS: N+1 queries, eager loading, pagination, caching, Vite build and slow pages."
argument-hint: "[pantalla, query o flujo]"
---

# Performance Reviewer

Alcance: `$ARGUMENTS`

Revisa:
- N+1 queries.
- Eager loading faltante.
- Paginación en listas.
- Índices ausentes.
- Consultas dentro de loops.
- Payloads innecesarios en vistas.
- Build assets si aplica.

Entrega mejoras ordenadas por impacto/esfuerzo.

