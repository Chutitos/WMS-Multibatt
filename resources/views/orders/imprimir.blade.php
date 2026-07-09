<!DOCTYPE html>
<html lang="es">
@php
use App\Enums\OrderStatus;
$esEntrega = $order->estado === OrderStatus::ENTREGADO;
@endphp

<head>
    <meta charset="utf-8">
    <title>{{ $esEntrega ? 'Comprobante de entrega' : 'Papeleta de preparación' }} — Orden #{{ $order->id }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, Helvetica, sans-serif; color: #111; padding: 24px; font-size: 14px; }
        .hoja { max-width: 720px; margin: 0 auto; }
        .encabezado { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #111; padding-bottom: 12px; }
        .empresa { font-size: 20px; font-weight: bold; }
        .documento { text-align: right; }
        .documento h1 { font-size: 18px; }
        .documento .numero { font-size: 26px; font-weight: bold; }
        .datos { margin-top: 16px; width: 100%; border-collapse: collapse; }
        .datos td { padding: 4px 8px 4px 0; vertical-align: top; }
        .datos .etiqueta { font-weight: bold; width: 160px; }
        table.items { margin-top: 20px; width: 100%; border-collapse: collapse; }
        table.items th, table.items td { border: 1px solid #999; padding: 8px 10px; text-align: left; }
        table.items th { background: #eee; }
        table.items td.num { text-align: center; width: 110px; }
        .firmas { margin-top: 56px; display: flex; justify-content: space-between; gap: 40px; }
        .firma { flex: 1; text-align: center; }
        .firma .linea { border-top: 1px solid #111; margin-top: 48px; padding-top: 6px; font-size: 13px; }
        .pie { margin-top: 32px; font-size: 11px; color: #666; text-align: center; }
        .no-imprimir { margin-bottom: 16px; text-align: center; }
        .no-imprimir button { padding: 10px 24px; font-size: 16px; font-weight: bold; background: #2563eb; color: #fff; border: 0; border-radius: 8px; cursor: pointer; }
        @media print {
            .no-imprimir { display: none; }
            body { padding: 0; }
        }
    </style>
</head>

<body>
    <div class="no-imprimir">
        <button type="button" onclick="window.print()">🖨 Imprimir</button>
    </div>

    <div class="hoja">
        <div class="encabezado">
            <div>
                <div class="empresa">Comercial e Industrial Multibatt Ltda.</div>
                <div>Bodega de baterías — WMS Multibatt</div>
            </div>
            <div class="documento">
                <h1>{{ $esEntrega ? 'COMPROBANTE DE ENTREGA' : 'PAPELETA DE PREPARACIÓN' }}</h1>
                <div class="numero">Orden #{{ $order->id }}</div>
                <div>{{ now()->format('d-m-Y H:i') }}</div>
            </div>
        </div>

        <table class="datos">
            <tr>
                <td class="etiqueta">Cliente</td>
                <td>{{ $order->cliente_nombre }}{{ $order->rut_cliente ? " — RUT {$order->rut_cliente}" : '' }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Tipo de entrega</td>
                <td>{{ $order->tipo_entrega === 'retiro' ? 'Retiro en bodega' : 'Despacho a domicilio' }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Estado</td>
                <td>{{ $order->estado->label() }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Fecha de creación</td>
                <td>{{ $order->created_at?->format('d-m-Y H:i') }} ({{ $order->creator->name ?? '—' }})</td>
            </tr>
            @if ($esEntrega)
                @if ($order->tipo_entrega === 'retiro')
                <tr>
                    <td class="etiqueta">Retirado por</td>
                    <td>{{ $order->retirado_por_nombre }}{{ $order->retirado_por_rut ? " — RUT {$order->retirado_por_rut}" : '' }}</td>
                </tr>
                @else
                <tr>
                    <td class="etiqueta">Transportista</td>
                    <td>{{ $order->transportista }}{{ $order->guia_despacho ? " — Guía {$order->guia_despacho}" : '' }}</td>
                </tr>
                @endif
            @endif
            @if ($order->observaciones)
            <tr>
                <td class="etiqueta">Observaciones</td>
                <td>{{ $order->observaciones }}</td>
            </tr>
            @endif
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Detalle</th>
                    <th class="num">Pedidas</th>
                    <th class="num">Confirmadas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->producto_nombre }}</td>
                    <td>{{ $item->product?->fichaCorta() ?? '—' }}</td>
                    <td class="num">{{ $item->cantidad_solicitada }}</td>
                    <td class="num">{{ $item->cantidad_confirmada }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="firmas">
            <div class="firma">
                <div class="linea">Entregado por (bodega)</div>
            </div>
            <div class="firma">
                <div class="linea">{{ $order->tipo_entrega === 'retiro' ? 'Recibido por (cliente)' : 'Recibido por (transportista)' }}</div>
            </div>
        </div>

        <div class="pie">
            Documento interno de bodega generado por WMS Multibatt. No constituye documento tributario.
        </div>
    </div>
</body>

</html>
