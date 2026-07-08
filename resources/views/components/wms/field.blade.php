@props(['label', 'name' => null, 'optional' => false, 'hint' => null])

<div>
    <label class="block text-base font-semibold text-slate-700 mb-2">
        {{ $label }}
        @if ($optional)
        <span class="font-normal text-slate-400">(opcional)</span>
        @endif
    </label>

    {{ $slot }}

    @if ($hint)
    <p class="mt-1 text-sm text-slate-500">{{ $hint }}</p>
    @endif

    @if ($name)
    @error($name)
    <p class="mt-2 text-base text-red-600">{{ $message }}</p>
    @enderror
    @endif
</div>
