@extends('emails.layout')
@section('titulo', 'E-mail de teste')
@section('conteudo')
    <p>Se você recebeu esta mensagem, a configuração de SMTP do {{ config('app.name', 'Tallents Gestão') }} está <strong>funcionando corretamente</strong>. ✅</p>
    <p>Enviado em {{ now()->format('d/m/Y H:i') }}.</p>
@endsection
