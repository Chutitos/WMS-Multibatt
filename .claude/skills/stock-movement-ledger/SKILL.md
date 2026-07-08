---
name: stock-movement-ledger
description: "Diseña un ledger de movimientos de inventario para trazabilidad robusta. Use when stock changes, adjustments or audit trail are involved."
argument-hint: "[operación de stock]"
---

# Stock Movement Ledger

Operación: `$ARGUMENTS`

Revisa o diseña el historial de stock.

Cada movimiento debe tener:
- Producto/SKU.
- Lote/ubicación si aplica.
- Cantidad positiva/negativa o tipo entrada/salida.
- Tipo de movimiento.
- Referencia: orden, recepción, ajuste, despacho.
- Usuario.
- Fecha.
- Observación.

Regla: el stock actual debe poder explicarse desde movimientos o quedar sincronizado con ellos.

