@extends('layouts.app')

@section('title', 'Ocorrência')
@section('page-title', 'Detalhes da Ocorrência')

@section('content')
<div class="space-y-6 py-4">

    <div class="flex items-center gap-3">
        <a href="{{ route('ocorrencias.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div class="flex-1"></div>
        <a href="{{ route('advertencias.create', ['ocorrencia_id' => $ocorrencia->id]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition">
            Gerar advertência
        </a>
        <a href="{{ route('ocorrencias.edit', $ocorrencia) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
            Editar
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Colaborador</dt><dd class="text-gray-900">{{ optional($ocorrencia->colaborador)->nome ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Tipo</dt><dd class="text-gray-900">{{ optional($ocorrencia->tipoOcorrencia)->nome ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Data</dt><dd class="text-gray-900">{{ optional($ocorrencia->data_ocorrencia)->format('d/m/Y') }}{{ $ocorrencia->hora_ocorrencia ? ' às ' . \Illuminate\Support\Str::substr($ocorrencia->hora_ocorrencia, 0, 5) : '' }}</dd></div>
            <div><dt class="text-gray-500">Gravidade</dt><dd class="text-gray-900">{{ $ocorrencia->gravidade ? ucfirst($ocorrencia->gravidade) : '—' }}</dd></div>
            @if(!is_null($ocorrencia->tempo_atraso_minutos))
                <div><dt class="text-gray-500">Tempo de atraso</dt><dd class="text-gray-900">{{ $ocorrencia->tempo_atraso_minutos }} min</dd></div>
            @endif
            @if($ocorrencia->tipo_ponto)
                <div><dt class="text-gray-500">Tipo de ponto</dt><dd class="text-gray-900">{{ \App\Models\Ocorrencia::TIPOS_PONTO[$ocorrencia->tipo_ponto] ?? $ocorrencia->tipo_ponto }}</dd></div>
            @endif
            <div class="sm:col-span-2"><dt class="text-gray-500">Descrição</dt><dd class="text-gray-900 whitespace-pre-line">{{ $ocorrencia->descricao ?: '—' }}</dd></div>
            <div><dt class="text-gray-500">Registrado por</dt><dd class="text-gray-900">{{ optional($ocorrencia->registradoPor)->name ?: '—' }}</dd></div>
        </dl>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Anexos --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Anexos</h3>
                    <form method="POST" action="{{ route('ocorrencias.anexos.store', $ocorrencia) }}" enctype="multipart/form-data" class="flex items-center gap-2"
                          x-data @change="$el.submit()">
                        @csrf
                        <label class="cursor-pointer text-sm font-medium text-indigo-600 hover:text-indigo-700">
                            <input type="file" name="arquivo" class="hidden">
                            + Enviar arquivo
                        </label>
                    </form>
                </div>
                <div class="p-4">
                    @forelse($ocorrencia->anexos as $anexo)
                        <div class="flex items-center justify-between py-2 px-2 rounded-lg hover:bg-gray-50">
                            <a href="{{ Storage::url($anexo->caminho) }}" target="_blank" class="flex items-center gap-3 min-w-0">
                                <svg class="h-5 w-5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                <span class="text-sm text-gray-800 truncate">{{ $anexo->nome_original }}</span>
                                <span class="text-xs text-gray-400 shrink-0">{{ number_format($anexo->tamanho / 1024, 0) }} KB</span>
                            </a>
                            <form method="POST" action="{{ route('ocorrencias.anexos.destroy', $anexo) }}"
                                  x-data @submit.prevent="if(confirm('Remover anexo?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-gray-400 hover:text-red-600 rounded">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Nenhum anexo</p>
                    @endforelse
                </div>
            </div>

            {{-- Comentários --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">Comentários</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($ocorrencia->comentarios as $comentario)
                        <div class="px-6 py-4 flex gap-3">
                            <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center shrink-0">
                                <span class="text-white text-xs font-semibold">{{ strtoupper(substr(optional($comentario->usuario)->name ?? '?', 0, 1)) }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-900">{{ optional($comentario->usuario)->name ?? 'Usuário' }}</span>
                                    <span class="text-xs text-gray-400">{{ $comentario->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-line mt-0.5">{{ $comentario->comentario }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Nenhum comentário ainda</p>
                    @endforelse
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    <form method="POST" action="{{ route('ocorrencias.comentarios.store', $ocorrencia) }}" class="flex gap-2">
                        @csrf
                        <input type="text" name="comentario" required placeholder="Escreva um comentário..."
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Enviar</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Histórico --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Histórico</h3>
            </div>
            <div class="p-6">
                <ol class="relative border-l border-gray-200 ml-2 space-y-5">
                    @forelse($ocorrencia->historico as $h)
                        <li class="ml-4">
                            <span class="absolute -left-1.5 mt-1 h-3 w-3 rounded-full bg-indigo-500"></span>
                            <p class="text-sm font-medium text-gray-900">{{ $h->acao }}</p>
                            @if($h->detalhe)<p class="text-xs text-gray-500">{{ $h->detalhe }}</p>@endif
                            <p class="text-xs text-gray-400">{{ optional($h->usuario)->name ?? 'Sistema' }} · {{ $h->created_at->format('d/m/Y H:i') }}</p>
                        </li>
                    @empty
                        <li class="ml-4 text-sm text-gray-400">Sem histórico</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
