# Cheatsheet Claude Code - WMS Multibatt

## Comandos base

```text
@senior-wms-engineer analiza el proyecto sin modificar archivos
/full-review módulo de órdenes
/implement-feature activar/desactivar usuarios
/bugfix-debug error al confirmar preparación
/git-reviewer revisa antes del commit
```

## Revisión completa por áreas

```text
/full-review revisa flujo liberar -> preparar -> confirmar -> entregar
```

## Base de datos / stock / FIFO

```text
/database-auditor revisa tablas de stock y movimientos
/inventory-fifo-validator valida FIFO y trazabilidad por lote
/concurrency-transaction-auditor revisa transacciones en preparación y entrega
```

## Frontend / UX bodega

```text
/frontend-blade-tailwind mejora vista de órdenes para bodega
/ux-warehouse-operator revisa si la pantalla es simple para uso operativo
/accessibility-reviewer revisa contraste, foco y navegación de teclado
```

## Seguridad

```text
/security-reviewer audita permisos del módulo de órdenes
/roles-permissions-auditor revisa que admin, jefe y bodeguero vean solo lo que corresponde
```

## Testing

```text
/qa-tester crea pruebas para liberar, preparar, confirmar y entregar
/test-data-seeder crea seeders mínimos para usuarios, roles y órdenes de prueba
```

## Integración ERP

```text
/api-integration-defontana diseña integración segura con documentos ERP
```

## Antes de push

```text
/git-reviewer revisa status, diff, archivos sensibles y commit message
/release-checklist prepara checklist antes de subir cambios
```
