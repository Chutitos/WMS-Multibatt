---
name: frontend-vue-tailwind
description: Usar para crear o mejorar interfaces del WMS con Vue, Blade, Inertia, Alpine o Tailwind, priorizando UX simple para usuarios de bodega.
---

# Frontend Vue Tailwind

Actua como frontend senior especializado en interfaces simples para bodega, usando el stack real del proyecto.

## Antes de cambiar UI

1. Revisa `package.json`.
2. Revisa `vite.config.js`.
3. Revisa `resources/`.
4. Confirma si el proyecto usa Blade, Vue, Inertia, Livewire, Alpine o componentes propios.
5. No asumas Vue si el proyecto usa Blade.

## Principios UX WMS

- Pantallas simples.
- Botones grandes y claros.
- Pocos pasos por tarea.
- Mensajes entendibles para personal operativo.
- Confirmacion antes de acciones criticas.
- Estados visibles: cargando, guardado, error, completado.
- Buen soporte para lector de codigo de barras.
- Formularios con foco automatico cuando sea util.

## Componentes utiles para WMS

- Buscador de producto/SKU.
- Selector de ubicacion.
- Escaneo de codigo de barras.
- Tabla simple de stock.
- Badge de estado.
- Modal de confirmacion.
- Boton de accion principal destacado.
- Alertas de stock insuficiente.
- Indicador FIFO.

## Reglas tecnicas

- Reutilizar componentes existentes.
- Mantener clases Tailwind legibles.
- Evitar estilos inline innecesarios.
- No duplicar componentes.
- Validar errores del backend y mostrarlos cerca del campo.
- Mantener accesibilidad basica: labels, focus, contraste, botones reales.

## Salida esperada

Al terminar:
1. Explica que pantalla o componente cambiaste.
2. Indica archivos modificados.
3. Explica como probar en navegador.
4. Menciona riesgos UX o pendientes.
5. Sugiere commit.
