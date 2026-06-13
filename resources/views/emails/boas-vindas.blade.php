@extends('emails.layout')
@section('titulo', 'Bem-vindo(a), ' . $colaborador->nome . '!')
@section('conteudo')
    <p>É com prazer que damos as boas-vindas à equipe da <strong>{{ optional($colaborador->empresa)->nome ?? config('app.name') }}</strong>.</p>
    <p>Seguem seus dados de cadastro:</p>
    <ul>
        <li><strong>Cargo:</strong> {{ optional($colaborador->cargo)->nome ?? '—' }}</li>
        <li><strong>Setor:</strong> {{ optional($colaborador->setor)->nome ?? '—' }}</li>
        @if($emailLogin)<li><strong>Acesso ao portal:</strong> {{ $emailLogin }}</li>@endif
    </ul>
    @if($emailLogin)
        <p>Você já pode acessar o portal do colaborador com o e-mail acima e a senha definida pelo RH.</p>
    @endif
    <p>Seja muito bem-vindo(a)!</p>
@endsection
