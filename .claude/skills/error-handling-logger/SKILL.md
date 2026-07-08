---
name: error-handling-logger
description: "Mejora manejo de errores y logging en Laravel para flujos WMS críticos without leaking sensitive data."
argument-hint: "[flujo o error]"
---

# Error Handling Logger

Alcance: `$ARGUMENTS`

Revisa:
- Excepciones esperadas vs inesperadas.
- Mensajes para usuario.
- Logs para diagnóstico.
- Contexto: usuario, orden, producto, acción.
- No registrar contraseñas, tokens ni datos sensibles.

Entrega estrategia de errores y cambios mínimos.

