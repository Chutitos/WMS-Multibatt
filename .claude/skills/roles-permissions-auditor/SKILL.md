---
name: roles-permissions-auditor
description: "Audita roles y permisos de admin, jefe, bodeguero u otros roles. Use when routes/actions/views must be role-aware."
argument-hint: "[módulo o rutas]"
---

# Roles Permissions Auditor

Alcance: `$ARGUMENTS`

Revisa:
- Rutas protegidas por auth.
- Middleware por rol.
- Condiciones en Blade.
- Controladores con validación server-side.
- Acciones visibles vs acciones permitidas.
- Último admin no eliminable si aplica.

Entrega matriz:
- Acción
- Admin
- Jefe
- Bodeguero
- Riesgo
- Corrección sugerida

