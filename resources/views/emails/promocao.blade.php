@extends('emails.layout')
@section('titulo', 'Parabéns pela promoção!')
@section('conteudo')
    <p>Olá, {{ optional($promocao->colaborador)->nome }},</p>
    <p>Temos uma ótima notícia: você foi promovido(a) em {{ optional($promocao->data_promocao)->format('d/m/Y') }}.</p>
    <ul>
        <li><strong>Novo cargo:</strong> {{ optional($promocao->cargoNovo)->nome ?? optional($promocao->novoCargoObj)->nome ?? '—' }}</li>
        @if($promocao->salario_novo)<li><strong>Novo salário:</strong> R$ {{ number_format($promocao->salario_novo, 2, ',', '.') }}</li>@endif
    </ul>
    @if($promocao->motivo)<p><em>{{ $promocao->motivo }}</em></p>@endif
    <p>Continue com o ótimo trabalho! 🎉</p>
@endsection
