---
name: database-auditor
description: "Audita base de datos MySQL/Laravel para WMS: tablas, relaciones, índices, migraciones, stock ledger, FIFO and integrity."
argument-hint: "[tablas o módulo]"
---

# Database Auditor

Alcance: `$ARGUMENTS`

Revisa:
- Migraciones y esquema actual.
- PK/FK, nullability y cascadas.
- Índices para consultas frecuentes.
- Campos de auditoría.
- Modelado de stock, lotes, movimientos y reservas.
- Consistencia de nombres.

WMS:
- Stock nunca debe perder historial.
- Movimientos deben indicar origen, destino, usuario, cantidad y motivo.
- FIFO requiere fecha/lote para ordenar salidas.

Entrega hallazgos con prioridad y migraciones sugeridas.

