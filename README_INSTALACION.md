# Kit Claude Code Pro para WMS Multibatt

Este kit instala un contexto de proyecto, subagentes y skills para trabajar el WMS con enfoque de programador senior.

## Contenido

```text
CLAUDE.md
AGENTS.md
.claude/
  agents/
  skills/
  rules/
  settings.example.json
CHEATSHEET.md
```

## Instalación

Copia todo el contenido de esta carpeta en la raíz del proyecto Laravel, donde están `artisan`, `composer.json`, `app`, `routes` y `resources`.

Debe quedar así:

```text
WMS-Multibatt/
  app/
  routes/
  resources/
  artisan
  composer.json
  CLAUDE.md
  AGENTS.md
  .claude/
```

Después abre Claude Code desde la raíz del proyecto.

Si Claude Code ya estaba abierto, reinicia la sesión o abre una nueva para asegurar que cargue el primer directorio `.claude/`.

## Verificación

En Claude Code prueba:

```text
/memory
```

Debe aparecer `CLAUDE.md` como memoria del proyecto.

Luego escribe:

```text
/
```

Deberías ver skills como:

```text
/full-review
/laravel-architect
/database-auditor
/security-reviewer
/qa-tester
/git-reviewer
```

## Primer uso recomendado

```text
@senior-wms-engineer analiza este proyecto sin modificar archivos y dame un mapa de arquitectura actual
```

Luego:

```text
/full-review módulo de órdenes: revisa arquitectura, base de datos, seguridad, UX, pruebas y Git
```

## Flujo diario recomendado

1. Analizar antes de tocar:

```text
@senior-wms-engineer revisa el cambio que quiero hacer y propón un plan sin editar archivos
```

2. Implementar:

```text
/implement-feature descripción del cambio
```

3. Revisar:

```text
/full-review archivos modificados
```

4. Preparar commit:

```text
/git-reviewer revisa mis cambios y sugiere commit message
```

## Nota sobre settings.example.json

El archivo `.claude/settings.example.json` es solo ejemplo. No lo renombres a `settings.json` hasta revisar las reglas. Está pensado para bloquear comandos peligrosos comunes, no para automatizar permisos amplios.
