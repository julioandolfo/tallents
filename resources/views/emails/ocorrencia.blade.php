@extends('emails.layout')
@section('titulo', 'Registro de ocorrência')
@section('conteudo')
    <p>Olá, {{ optional($ocorrencia->colaborador)->nome }},</p>
    <p>Foi registrada uma ocorrência vinculada ao seu cadastro:</p>
    <ul>
        <li><strong>Tipo:</strong> {{ optional($ocorrencia->tipoOcorrencia)->nome ?? '—' }}</li>
        <li><strong>Data:</strong> {{ optional($ocorrencia->data_ocorrencia)->format('d/m/Y') }}{{ $ocorrencia->hora_ocorrencia ? ' às ' . \Illuminate\Support\Str::substr($ocorrencia->hora_ocorrencia, 0, 5) : '' }}</li>
        @if(!is_null($ocorrencia->tempo_atraso_minutos))<li><strong>Tempo de atraso:</strong> {{ $ocorrencia->tempo_atraso_minutos }} min</li>@endif
        @if($ocorrencia->gravidade)<li><strong>Gravidade:</strong> {{ ucfirst($ocorrencia->gravidade) }}</li>@endif
    </ul>
    @if($ocorrencia->descricao)<p>{{ $ocorrencia->descricao }}</p>@endif
    <p>Em caso de dúvidas, procure o RH.</p>
@endsection
