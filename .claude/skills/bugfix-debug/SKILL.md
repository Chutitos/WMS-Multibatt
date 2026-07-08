---
name: bugfix-debug
description: "Diagnostica y corrige bugs Laravel/PHP/Blade/Vite/MySQL. Use with stack traces, failing flows, 500 errors or unexpected behavior."
argument-hint: "[error o flujo que falla]"
---

# Bugfix Debug

Problema: `$ARGUMENTS`

## Proceso
1. Leer el error exacto y ubicar archivo/línea.
2. Revisar el flujo que dispara el error.
3. Formular causa raíz probable.
4. Proponer corrección mínima.
5. Editar solo lo necesario.
6. Indicar cómo verificar.

## Evita
- Silenciar excepciones sin resolver la causa.
- Cambiar configuración global para tapar un bug local.
- Ejecutar migraciones destructivas para resolver errores de datos.

