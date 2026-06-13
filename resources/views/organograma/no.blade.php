@php $filhos = $porLider[$colaborador->id] ?? collect(); @endphp
<li>
    <div class="inline-flex items-center gap-3 bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-2.5">
        <div class="h-9 w-9 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-semibold shrink-0">
            {{ strtoupper(\Illuminate\Support\Str::substr($colaborador->nome, 0, 1)) }}
        </div>
        <div>
            <a href="{{ route('colaboradores.show', $colaborador) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">{{ $colaborador->nome }}</a>
            <p class="text-xs text-gray-500">{{ optional($colaborador->cargo)->nome ?? '—' }}{{ $colaborador->setor ? ' · ' . $colaborador->setor->nome : '' }}</p>
        </div>
    </div>

    @if($filhos->isNotEmpty())
        <ul class="mt-3 ml-6 border-l border-gray-200 pl-6 space-y-3">
            @foreach($filhos as $filho)
                @include('organograma.no', ['colaborador' => $filho, 'porLider' => $porLider])
            @endforeach
        </ul>
    @endif
</li>
