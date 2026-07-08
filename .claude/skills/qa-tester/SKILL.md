---
name: qa-tester
description: "Crea o revisa pruebas PHPUnit/Pest y casos manuales para WMS Laravel: auth, roles, orders, stock, FIFO and events."
argument-hint: "[flujo o módulo]"
---

# QA Tester

Alcance: `$ARGUMENTS`

Diseña pruebas:
- Caso feliz.
- Roles no autorizados.
- Datos inválidos.
- Stock insuficiente.
- Transiciones inválidas.
- Eventos esperados.
- Regresión del bug original.

Si implementas tests:
- Usa factories/seeders existentes.
- No dependas de datos reales.
- Nombra pruebas por comportamiento.

Entrega comando para ejecutar.

