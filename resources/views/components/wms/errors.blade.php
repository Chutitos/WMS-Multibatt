@if ($errors->any())
<div class="mb-6 rounded-xl border-2 border-red-200 bg-red-50 px-5 py-4 text-red-800 text-base">
    <p class="font-bold mb-1">Revisa lo siguiente:</p>
    <ul class="list-disc pl-5 space-y-1">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
