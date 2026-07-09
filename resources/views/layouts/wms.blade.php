<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'WMS Multibatt' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('icono-multibatt.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-200 text-slate-800 min-h-screen">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white border-b border-slate-300 shadow-sm">
            <div class="max-w-7xl mx-auto px-3 md:px-6 py-4">

                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('logo-multibatt.png') }}" class="h-10 w-auto" alt="Multibatt">

                        <div class="leading-tight">
                            <div class="text-lg font-bold text-slate-900">
                                WMS Multibatt
                            </div>
                            <div class="text-sm text-slate-500">
                                Sistema de gestión logística
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="text-right">
                            <div class="text-sm font-semibold text-slate-900">
                                {{ auth()->user()->name ?? 'Usuario' }}
                            </div>
                            <div class="text-xs text-slate-500">
                                {{ auth()->user()->role->name ?? '' }}
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg font-semibold border border-red-200">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>

                @php
                // Navegación por rol definida en un solo lugar para que todas
                // las pestañas se vean y se comporten igual.
                $tabs = match (auth()->user()->role->name ?? '') {
                    'admin' => [
                        ['route' => 'dashboard', 'match' => 'dashboard', 'label' => 'Inicio'],
                        ['route' => 'orders.index', 'match' => 'orders.*', 'label' => 'Órdenes'],
                        ['route' => 'bodega.index', 'match' => 'bodega.index', 'label' => 'Por preparar'],
                        ['route' => 'bodega.preparando', 'match' => 'bodega.preparando', 'label' => 'Preparando'],
                        ['route' => 'bodega.listo', 'match' => 'bodega.listo', 'label' => 'Para entregar'],
                        ['route' => 'users.index', 'match' => 'users.*', 'label' => 'Usuarios'],
                        ['route' => 'products.index', 'match' => 'products.*', 'label' => 'Productos'],
                        ['route' => 'locations.index', 'match' => 'locations.*', 'label' => 'Mapa de bodega'],
                        ['route' => 'product-locations.index', 'match' => 'product-locations.*', 'label' => 'Existencias'],
                        ['route' => 'erp.index', 'match' => 'erp.*', 'label' => 'ERP'],
                    ],
                    'jefe_bodega' => [
                        ['route' => 'dashboard', 'match' => 'dashboard', 'label' => 'Inicio'],
                        ['route' => 'orders.index', 'match' => 'orders.*', 'label' => 'Órdenes'],
                        ['route' => 'locations.index', 'match' => 'locations.*', 'label' => 'Mapa de bodega'],
                        ['route' => 'product-locations.index', 'match' => 'product-locations.*', 'label' => 'Existencias'],
                    ],
                    'bodeguero' => [
                        ['route' => 'dashboard', 'match' => 'dashboard', 'label' => 'Inicio'],
                        ['route' => 'bodega.index', 'match' => 'bodega.index', 'label' => 'Por preparar'],
                        ['route' => 'bodega.preparando', 'match' => 'bodega.preparando', 'label' => 'Preparando'],
                        ['route' => 'bodega.listo', 'match' => 'bodega.listo', 'label' => 'Para entregar'],
                        ['route' => 'product-locations.index', 'match' => 'product-locations.*', 'label' => 'Existencias'],
                        ['route' => 'locations.index', 'match' => 'locations.*', 'label' => 'Mapa de bodega'],
                    ],
                    default => [],
                };
                @endphp

                <nav class="mt-4 flex items-center gap-2 flex-wrap">
                    @foreach ($tabs as $tab)
                    <a href="{{ route($tab['route']) }}"
                        class="px-4 py-2.5 rounded-xl text-base font-semibold transition whitespace-nowrap
                            {{ request()->routeIs($tab['match'])
                                ? 'bg-blue-600 text-white shadow-sm'
                                : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        {{ $tab['label'] }}
                    </a>
                    @endforeach
                </nav>

            </div>
        </header>

        <main class="flex-1 py-4 md:py-8">
            <div class="max-w-7xl mx-auto px-2 md:px-6">
                <div class="bg-white border border-slate-300 rounded-2xl shadow-sm p-4 md:p-8">
                    @if (session('success'))
                    <div class="mb-6 rounded-xl border-2 border-green-200 bg-green-50 px-5 py-4 text-green-800 text-base font-semibold">
                        ✅ {{ session('success') }}
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="mb-6 rounded-xl border-2 border-red-200 bg-red-50 px-5 py-4 text-red-800 text-base font-semibold">
                        ⚠ {{ session('error') }}
                    </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>
</body>

</html>
