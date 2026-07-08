---
name: concurrency-transaction-auditor
description: "Audita concurrencia y transacciones en stock/órdenes: race conditions, atomic updates, locks and rollback consistency."
argument-hint: "[operación crítica]"
---

# Concurrency Transaction Auditor

Operación: `$ARGUMENTS`

Revisa:
- Uso de DB::transaction().
- Lecturas y escrituras de stock.
- Posibles dobles confirmaciones.
- Race conditions en picking/despacho.
- Validación antes y dentro de transacción.
- Rollback ante error.
- Eventos escritos junto con cambios principales.

Entrega riesgos y patrón seguro recomendado.

