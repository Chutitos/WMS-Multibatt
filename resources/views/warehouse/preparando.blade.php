@extends('layouts.wms')

@section('content')
<h2 class="text-2xl font-bold mb-6">Órdenes en preparación</h2>

<table class="min-w-full text-sm">
    @foreach ($orders as $order)
    <tr class="border-b">
        <td class="p-4">#{{ $order->id }}</td>
        <td class="p-4">{{ $order->cliente_nombre }}</td>
        <td class="p-4">
            <a href="{{ route('orders.show', $order) }}"
                class="px-3 py-2 bg-blue-600 text-white rounded-lg">
                Ver
            </a>
        </td>
    </tr>
    @endforeach
</table>
@endsection