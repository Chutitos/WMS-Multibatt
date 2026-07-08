---
name: picking-packing-dispatch
description: "Diseña o revisa picking, empaque y despacho WMS con stock, trazabilidad, estados and UX de bodega."
argument-hint: "[flujo de preparación/despacho]"
---

# Picking Packing Dispatch

Alcance: `$ARGUMENTS`

Revisa flujo operativo:
1. Orden liberada.
2. Bodeguero inicia preparación.
3. Picking de ítems.
4. Confirmación de cantidades.
5. Estado listo.
6. Entrega/despacho.

Validaciones:
- Stock disponible.
- Cantidad solicitada vs preparada.
- Usuario responsable.
- Eventos por transición.
- Mensajes claros.

Entrega mejoras por backend, frontend y pruebas.

