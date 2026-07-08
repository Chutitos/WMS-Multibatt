# Reglas Laravel

Aplica a trabajo en `app/`, `routes/`, `database/`, `resources/` y `tests/`.

- Prefiere cambios pequeños y verificables.
- Usa transacciones para operaciones que cambian orden + stock + eventos.
- No mezcles refactor masivo con corrección de bug puntual.
- No modifiques `.env` real sin permiso.
- Antes de proponer `composer update`, explica el riesgo.
