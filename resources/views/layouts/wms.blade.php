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
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

                <div class="flex items-center gap-12">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('logo-multibatt.png') }}" class="h-10 w-auto">

                        <div class="leading-tight">
                            <div class="text-lg font-bold text-slate-900">
                                WMS Multibatt
                            </div>
                            <div class="text-sm text-slate-500">
                                Sistema de gestión logística
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-8 ml-8">

                        {{-- ADMIN --}}
                        @if (auth()->user()->role->name === 'admin')
                        <a href="{{ route('dashboard') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                                {{ request()->routeIs('dashboard')
                                    ? 'border-blue-600 text-blue-700'
                                    : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Inicio
                        </a>

                        <a href="{{ route('orders.index') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                                {{ request()->routeIs('orders.*')
                                    ? 'border-blue-600 text-blue-700'
                                    : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Órdenes
                        </a>

                        <a href="{{ route('bodega.index') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                                {{ request()->routeIs('bodega.index')
                                    ? 'border-blue-600 text-blue-700'
                                    : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Por preparar
                        </a>

                        <a href="{{ route('bodega.preparando') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                                {{ request()->routeIs('bodega.preparando')
                                    ? 'border-blue-600 text-blue-700'
                                    : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Preparando
                        </a>

                        <a href="{{ route('bodega.listo') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                                {{ request()->routeIs('bodega.listo')
                                    ? 'border-blue-600 text-blue-700'
                                    : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Para entregar
                        </a>
                        <a href="{{ route('users.index') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                             {{ request()->routeIs('users.*')
                                ? 'border-blue-600 text-blue-700'
                                : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Usuarios
                        </a>

                        <a href="{{ route('products.index') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                             {{ request()->routeIs('products.*')
                                ? 'border-blue-600 text-blue-700'
                                : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Productos
                        </a>

                        <a href="{{ route('locations.index') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                             {{ request()->routeIs('locations.*')
                                ? 'border-blue-600 text-blue-700'
                                : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Mapa de bodega
                        </a>

                        <a href="{{ route('product-locations.index') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                             {{ request()->routeIs('product-locations.*')
                                ? 'border-blue-600 text-blue-700'
                                : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Existencias
                        </a>
                        @endif

                        {{-- JEFE DE BODEGA --}}
                        @if (auth()->user()->role->name === 'jefe_bodega')
                        <a href="{{ route('dashboard') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                                {{ request()->routeIs('dashboard')
                                    ? 'border-blue-600 text-blue-700'
                                    : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Inicio
                        </a>

                        <a href="{{ route('orders.index') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                                {{ request()->routeIs('orders.*')
                                    ? 'border-blue-600 text-blue-700'
                                    : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Órdenes
                        </a>

                        <a href="{{ route('locations.index') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                                {{ request()->routeIs('locations.*')
                                    ? 'border-blue-600 text-blue-700'
                                    : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Mapa de bodega
                        </a>
                        @endif

                        {{-- BODEGUERO --}}
                        @if (auth()->user()->role->name === 'bodeguero')
                        <a href="{{ route('dashboard') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                                {{ request()->routeIs('dashboard')
                                    ? 'border-blue-600 text-blue-700'
                                    : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Inicio
                        </a>

                        <a href="{{ route('bodega.index') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                                {{ request()->routeIs('bodega.index')
                                    ? 'border-blue-600 text-blue-700'
                                    : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Por preparar
                        </a>

                        <a href="{{ route('bodega.preparando') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                                {{ request()->routeIs('bodega.preparando')
                                    ? 'border-blue-600 text-blue-700'
                                    : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Preparando
                        </a>

                        <a href="{{ route('bodega.listo') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                                {{ request()->routeIs('bodega.listo')
                                    ? 'border-blue-600 text-blue-700'
                                    : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Para entregar
                        </a>

                        <a href="{{ route('product-locations.index') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                                {{ request()->routeIs('product-locations.*')
                                    ? 'border-blue-600 text-blue-700'
                                    : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Existencias
                        </a>

                        <a href="{{ route('locations.index') }}"
                            class="pb-3 text-lg font-semibold border-b-2 transition tracking-wide
                                {{ request()->routeIs('locations.*')
                                    ? 'border-blue-600 text-blue-700'
                                    : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
                            Mapa de bodega
                        </a>
                        @endif

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
                            class="text-sm text-red-600 hover:text-red-700 font-medium">
                            Cerrar sesión
                        </button>
                    </form>
                </div>

            </div>
        </header>

        <main class="flex-1 py-8">
            <div class="max-w-7xl mx-auto px-6">
                <div class="bg-white border border-slate-300 rounded-2xl shadow-sm p-8">
                    @if (session('success'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                        {{ session('error') }}
                    </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>
</body>

</html>