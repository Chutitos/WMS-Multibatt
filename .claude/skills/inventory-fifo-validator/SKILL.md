---
name: inventory-fifo-validator
description: "Valida reglas FIFO, lotes, fechas de recepción y salida de stock. Use for picking, despacho, stock reservation or batch logic."
argument-hint: "[flujo de stock]"
---

# Inventory FIFO Validator

Flujo: `$ARGUMENTS`

Revisa si el sistema respeta FIFO.

Checklist:
- Existe fecha de recepción o lote ordenable.
- Picking prioriza stock antiguo disponible.
- Reserva y descuento usan el mismo criterio.
- Stock insuficiente se detecta antes de confirmar.
- Operación corre en transacción.
- Eventos registran lote/cantidad cuando aplique.

Entrega:
- Estado actual.
- Brechas FIFO.
- Algoritmo recomendado.
- Casos de prueba.

