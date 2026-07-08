---
name: deployment-env-checker
description: "Verifica entorno local/despliegue: PHP extensions, Composer, Node/npm, Vite manifest, .env, MySQL and Laravel caches."
argument-hint: "[problema de entorno]"
---

# Deployment Env Checker

Problema: `$ARGUMENTS`

Checklist:
- `php -v`, `php --ini`, extensiones PHP necesarias.
- `composer install`.
- `.env` existe y APP_KEY está presente.
- DB_CONNECTION/DB_HOST/DB_DATABASE correctos.
- XAMPP/MySQL iniciado si local.
- `npm install` y `npm run build` para Vite.
- `php artisan optimize:clear` después de cambios de config.

Entrega diagnóstico paso a paso.

