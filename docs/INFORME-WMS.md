# Informe de estado — WMS Multibatt

**Fecha:** 09-07-2026 · **Rama:** WMS-0.3 · **Suite:** 86 tests / 307 aserciones, todos pasando

Sistema de gestión de bodega para Comercial e Industrial Multibatt Ltda., construido en Laravel 12 + Blade + Tailwind + MySQL. Los usuarios finales son operarios de bodega de 50–60 años; toda la interfaz está diseñada tipo kiosco/tablet (botones grandes, un paso a la vez, textos directos).

---

## 1. Qué tenemos hoy

### Flujo operativo completo (el corazón del WMS)

| Etapa | Cómo funciona |
|---|---|
| Crear orden | Admin/jefe la crea y **nace liberada**: aparece al instante en "Por preparar" de bodega |
| Preparar | El bodeguero la toma con un botón; queda "Preparando" con barra de avance |
| Picking | Escaneo con pistola **o botón "Confirmar 1 a mano"**; el sistema descuenta FIFO (lote más antiguo primero) y dice de qué rack y puesto tomar cada batería, con salto directo al mapa |
| Confirmar | El botón "Confirmar que está lista" **solo aparece cuando todo está escaneado** (validado también en servidor) |
| Entregar | Registra quién retira (nombre/RUT) o el transportista y guía; comprobante imprimible con firmas |
| Cancelar | Disponible según rol y estado (jefe solo antes de preparación) |

Cada transición queda registrada en el historial de la orden (usuario, fecha, descripción). Concurrencia protegida: escaneos simultáneos no pueden sobre-confirmar ni dejar stock negativo (transacciones con lock).

### Bodega física

- **Mapa visual** de la bodega: estantes que se crean con un clic (código autoincremental E-01, E-02…), se arrastran, renombran y activan/desactivan desde su popover.
- **Racks con estructura real**: columnas × niveles configurables; cada puesto guarda **un pallet de un solo tipo de batería** (validado en servidor). Grilla visual por estante con asignación directa en el puesto libre.
- **Existencias** (capa física local): qué batería hay en cada puesto, con lote y fecha de ingreso; buscador por batería o estante; edición y eliminación (solo admin) **con historial completo de quién cambió qué**.

### Catálogo de baterías

- Ficha técnica: marca, tipo (auto/camioneta/camión/moto/náutica/solar/industrial), voltaje, capacidad Ah — visible en catálogo, picking, racks e impresos.
- Stock mínimo físico por batería, con alerta roja en catálogo y dashboard.
- Buscador por texto y tipo.

### Seguridad y usuarios

- 3 roles (admin, jefe_bodega, bodeguero) con middleware + policies finas.
- Usuarios se activan/desactivan (nunca se borran); imposible dejar el sistema sin admin activo (ni desactivándolo ni cambiándole el rol).
- Registro público eliminado; login simple con campos grandes.

### Preparación para Defontana (sin conexión aún)

- Tablas `erp_documents`/`erp_document_items` con idempotencia (external_id único) y rastro de sincronización (intentos, último error).
- Contrato `ErpClient` + cliente `DefontanaClient`: cuando lleguen las credenciales, la API se implementa en **un solo archivo**.
- Página **ERP** (admin): estado de conexión, qué habilitará la integración y qué credenciales pedir a Defontana (se cargan por `.env`, nunca al código).
- Comando `php artisan defontana:sync` listo para agendarse en cron.

### Calidad

- 86 tests automatizados cubriendo el ciclo completo, FIFO, concurrencia, permisos por rol, racks, trazabilidad e integración.
- Sistema de diseño propio (componentes `x-wms.*`): toda pantalla nueva sale consistente.
- Todo versionado en Git: `master` (base), `desarrollo` (estable), `WMS-0.3` (actual).

---

## 2. Qué NO hace todavía (a propósito)

- **No maneja stock contable**: eso es de Defontana. La capa local es física (dónde está cada cosa), no un inventario paralelo.
- **No hay recepción de mercadería**: crear stock nuevo sin el respaldo del documento de compra del ERP generaría descuadres. Se activa junto con la integración.
- **No hay conexión real con Defontana**: falta que Multibatt obtenga credenciales de API.

---

## 3. Qué deberíamos seguir haciendo (orden recomendado)

### Corto plazo — sin depender de Defontana

1. **Poner el sistema en un servidor real** (hoy corre en un PC de desarrollo con XAMPP). Definir dónde vivirá (servidor local de la empresa o hosting), con HTTPS, respaldos automáticos de la BD y `.env` de producción. *Es el paso más importante: sin esto no hay operación real.*
2. **Marcha blanca con los operarios**: cargar el catálogo real de baterías y el mapa real de la bodega, y correr 1–2 semanas en paralelo al proceso actual. La interfaz está pensada para ellos, pero la prueba real la dan ellos.
3. **Pistola escaneadora**: comprar/probar una (las USB funcionan como teclado — el sistema ya está listo). Imprimir etiquetas de código de barras para los SKU que no traen código legible.
4. Ajustes que salgan de la marcha blanca (textos, tamaños, algún paso que sobre o falte).

### Mediano plazo — cuando lleguen las credenciales de Defontana

5. **Conexión real** (implementar `DefontanaClient`): descarga automática de documentos de venta → órdenes de bodega sin digitación.
6. **Recepción de mercadería**: las compras del ERP generan el ingreso guiado a racks (elegir puesto, imprimir etiqueta de pallet).
7. **Conciliación**: reporte que compara stock contable Defontana vs existencia física WMS y muestra diferencias.

### Más adelante — cuando la operación lo pida

8. Número de serie por batería (trazabilidad de garantías).
9. Reportes de gestión: rotación por batería, tiempos de preparación, entregas por día/semana.
10. Notificaciones (correo/WhatsApp) cuando una orden queda lista para retiro.

---

## 4. Riesgos actuales

| Riesgo | Mitigación |
|---|---|
| El sistema corre solo en un PC de desarrollo | Prioridad 1 del roadmap: servidor + respaldos |
| Existencias se cargan a mano hasta que exista recepción por ERP | El historial de existencias deja rastro de toda corrección |
| Un solo desarrollador/canal de cambios | Todo está en GitHub con tests; cualquier desarrollador Laravel puede continuar |
