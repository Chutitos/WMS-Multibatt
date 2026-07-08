---
name: order-workflow-designer
description: "Diseña y revisa flujo de órdenes WMS: creado, liberado, preparando, listo, entregado, eventos and permissions."
argument-hint: "[flujo o cambio]"
---

# Order Workflow Designer

Flujo: `$ARGUMENTS`

Revisa el ciclo de vida de órdenes.

Estados sugeridos si existen en el proyecto:
- creado
- liberado
- preparando
- listo
- entregado

Para cada transición define:
- Rol autorizado.
- Precondiciones.
- Cambios de estado.
- Eventos de trazabilidad.
- Cambios de stock si aplica.
- Validaciones y errores.

Entrega diagrama textual y plan de implementación.

