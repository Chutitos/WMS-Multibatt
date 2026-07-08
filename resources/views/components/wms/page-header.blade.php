@props(['title', 'subtitle' => null, 'paso' => null, 'pasoColor' => 'bg-blue-600'])

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3">
            @if ($paso)
            <span class="w-10 h-10 rounded-full {{ $pasoColor }} text-white font-bold flex items-center justify-center text-xl flex-shrink-0">{{ $paso }}</span>
            @endif
            <h2 class="text-3xl font-bold text-slate-900">{{ $title }}</h2>
        </div>
        @if ($subtitle)
        <p class="mt-2 text-lg text-slate-600">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
    <div class="flex items-center gap-3 flex-wrap">
        {{ $actions }}
    </div>
    @endisset
</div>
