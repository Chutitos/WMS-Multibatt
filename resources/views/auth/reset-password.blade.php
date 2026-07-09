<x-guest-layout>
    <h1 class="text-2xl font-bold text-slate-900 text-center mb-6">Crear contraseña nueva</h1>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-lg font-semibold text-slate-700 mb-2">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                class="block w-full rounded-xl border-2 border-slate-300 text-lg px-4 py-3 focus:border-blue-500 focus:ring-blue-500">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-5">
            <label for="password" class="block text-lg font-semibold text-slate-700 mb-2">Contraseña nueva</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="block w-full rounded-xl border-2 border-slate-300 text-lg px-4 py-3 focus:border-blue-500 focus:ring-blue-500">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-5">
            <label for="password_confirmation" class="block text-lg font-semibold text-slate-700 mb-2">Repite la contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="block w-full rounded-xl border-2 border-slate-300 text-lg px-4 py-3 focus:border-blue-500 focus:ring-blue-500">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit"
            class="mt-6 w-full px-6 py-4 bg-blue-600 text-white text-xl font-bold rounded-2xl hover:bg-blue-700 shadow-sm">
            Guardar contraseña
        </button>
    </form>
</x-guest-layout>
