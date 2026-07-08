<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="text-2xl font-bold text-slate-900 text-center mb-6">Bienvenido al WMS Multibatt</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-lg font-semibold text-slate-700 mb-2">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="block w-full rounded-xl border-2 border-slate-300 text-lg px-4 py-3 focus:border-blue-500 focus:ring-blue-500">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-5">
            <label for="password" class="block text-lg font-semibold text-slate-700 mb-2">Contraseña</label>
            <input id="password" type="password" name="password" required
                class="block w-full rounded-xl border-2 border-slate-300 text-lg px-4 py-3 focus:border-blue-500 focus:ring-blue-500">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember -->
        <div class="block mt-5">
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-blue-600 shadow-sm" name="remember">
                <span class="ms-3 text-base text-gray-700">Mantener la sesión iniciada</span>
            </label>
        </div>

        <button type="submit"
            class="mt-6 w-full px-6 py-4 bg-blue-600 text-white text-xl font-bold rounded-2xl hover:bg-blue-700 shadow-sm">
            Ingresar
        </button>

        @if (Route::has('password.request'))
        <div class="mt-5 text-center">
            <a class="underline text-base text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                ¿Olvidaste tu contraseña?
            </a>
        </div>
        @endif
    </form>
</x-guest-layout>
