# Guía de demo — WMS Multibatt

Cómo montar y correr la demo interactiva sin pagar nada.

---

## 1. Preparar el equipo (una vez, antes de la demo)

```bash
# 1. XAMPP: MySQL debe estar corriendo (panel de XAMPP o mysqld)

# 2. Base de datos fresca con datos de demostración
php artisan migrate:fresh --seed
php artisan db:seed --class=DemoSeeder

# 3. Assets compilados
npm run build

# 4. Levantar el servidor (accesible desde otros equipos de la red)
php artisan serve --host=0.0.0.0 --port=8000
```

> ⚠ `migrate:fresh` **borra todo**. Úsalo solo en la BD local de demo,
> nunca sobre datos reales.

Los datos de demo incluyen: 5 baterías con ficha técnica, 3 racks con
pallets en sus puestos, una alerta de stock mínimo activa y 5 órdenes,
una en cada estado del flujo.

---

## 2. Cómo mostrarla (opciones gratis)

### Opción A — Jefe presente: tablet/notebook en la misma red WiFi (recomendada)

1. Corre `php artisan serve --host=0.0.0.0 --port=8000`.
2. Averigua la IP del PC: `ipconfig` → "Dirección IPv4" (ej: 192.168.1.50).
3. En la tablet o notebook del jefe, abrir `http://192.168.1.50:8000`.

Es la mejor demo posible: la interfaz es táctil y tipo app, así que en
una tablet se luce tal como la usarán los operarios. Sin internet, sin
cuentas, sin costo.

### Opción B — Jefe remoto: túnel gratuito de Cloudflare

No requiere cuenta ni pago; genera una URL pública HTTPS temporal que
apunta al PC mientras el túnel esté abierto.

1. Descargar `cloudflared.exe` (una sola vez):
   https://github.com/cloudflare/cloudflared/releases/latest
2. Con `php artisan serve` corriendo, en otra terminal:

```bash
cloudflared tunnel --url http://localhost:8000
```

3. Copiar la URL `https://xxxxx.trycloudflare.com` que imprime y
   enviársela al jefe. Muere al cerrar la terminal (nada queda expuesto).

> Alternativas equivalentes: ngrok (pide cuenta gratis y muestra una
> página intermedia) o localtunnel (menos estable). Cloudflare es la
> más limpia de las gratuitas.

---

## 3. Guión sugerido (15 minutos)

**Usuarios:** admin@multibatt.cl · jefe@multibatt.cl · bodeguero@multibatt.cl

1. **Entrar como jefe** → el inicio muestra la operación al día y la
   alerta roja de stock mínimo (Batería Camión 180Ah).
2. **Crear una orden** (2 baterías Bosch S4) → aparece al instante en
   "Por preparar".
3. **Cambiar a bodeguero** → tomar la orden ("Comenzar a preparar").
4. **Picking**: mostrar "📍 Tómalo de: Rack A" y "Ver en el mapa" (el
   estante se ilumina). Confirmar unidades con "✔ Confirmar 1 a mano"
   (simula la pistola). Al agotarse el lote del Rack A, el sistema
   redirige solo al Rack B — ese es el FIFO en vivo.
5. **Confirmar que está lista** → **Entregar** anotando quién retira →
   imprimir el comprobante 🖨.
6. **Volver como jefe** → abrir el mapa, crear un estante con un clic,
   abrir la grilla de un rack y asignar un pallet a un puesto libre.
7. **Cierre con el admin**: historial de existencias (quién cambió qué),
   cancelar una orden a medio escanear y mostrar que las unidades
   vuelven solas a su estante, y la pestaña ERP con la integración
   Defontana lista para recibir credenciales.

---

## 4. Checklist del día

- [ ] MySQL corriendo
- [ ] `php artisan serve --host=0.0.0.0` corriendo
- [ ] Datos de demo cargados (`DemoSeeder`)
- [ ] Probar login de los 3 usuarios
- [ ] Tablet conectada a la misma WiFi (opción A) o túnel abierto (opción B)
- [ ] Impresora disponible si se quiere mostrar el comprobante en papel
- [ ] No usar "¿Olvidaste tu contraseña?" (no hay servidor de correo configurado)
