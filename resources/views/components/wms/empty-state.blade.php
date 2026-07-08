@props(['icon' => '📦', 'title', 'message' => null])

<div class="bg-slate-50 border-2 border-dashed border-slate-300 rounded-2xl py-16 px-6 text-center">
    <div class="text-5xl">{{ $icon }}</div>
    <p class="mt-4 text-2xl font-bold text-slate-700">{{ $title }}</p>
    @if ($message)
    <p class="mt-2 text-lg text-slate-500">{{ $message }}</p>
    @endif
    {{ $slot }}
</div>
