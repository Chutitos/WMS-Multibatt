<x-guest-layout>
    <h1 class="text-2xl font-bold text-slate-900 text-center mb-3">Recuperar contraseña</h1>

    <p class="mb-6 text-base text-slate-600 text-center">
        Escribe tu correo y te enviaremos un enlace para crear una contraseña nueva.
    </p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-lg font-semibold text-slate-700 mb-2">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="block w-full rounded-xl border-2 border-slate-300 text-lg px-4 py-3 focus:border-blue-500 focus:ring-blue-500">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit"
            class="mt-6 w-full px-6 py-4 bg-blue-600 text-white text-xl font-bold rounded-2xl hover:bg-blue-700 shadow-sm">
            Enviar enlace
        </button>

        <div class="mt-5 text-center">
            <a class="underline text-base text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                ← Volver al inicio de sesión
            </a>
        </div>
    </form>
</x-guest-layout>
