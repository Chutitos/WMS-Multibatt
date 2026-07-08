---
name: security-reviewer
description: "Revisión de seguridad Laravel: auth, authorization, validation, mass assignment, CSRF, secrets, uploads and dangerous commands."
argument-hint: "[módulo o cambios]"
---

# Security Reviewer

Alcance: `$ARGUMENTS`

Checklist:
- Auth en rutas privadas.
- Autorización en servidor, no solo Blade.
- Validación de inputs.
- Protección CSRF en formularios.
- Mass assignment seguro.
- No exponer .env, dumps, claves o tokens.
- Errores no revelan datos sensibles.
- Acciones críticas tienen confirmación y auditoría.

Entrega hallazgos por severidad y fixes concretos.

