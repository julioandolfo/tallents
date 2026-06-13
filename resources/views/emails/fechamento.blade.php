@extends('emails.layout')
@section('titulo', 'Demonstrativo de pagamento')
@section('conteudo')
    <p>Olá, {{ optional($item->colaborador)->nome }},</p>
    <p>Segue o resumo do seu pagamento referente ao período {{ str_pad(optional($item->fechamento)->mes, 2, '0', STR_PAD_LEFT) }}/{{ optional($item->fechamento)->ano }}:</p>
    <table cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
        <tr><td>Salário base</td><td align="right">R$ {{ number_format($item->salario, 2, ',', '.') }}</td></tr>
        <tr><td>Horas extras</td><td align="right">R$ {{ number_format($item->horas_extras, 2, ',', '.') }}</td></tr>
        <tr><td>Bônus</td><td align="right">R$ {{ number_format($item->bonus, 2, ',', '.') }}</td></tr>
        @if(isset($item->descontos))<tr><td>Descontos</td><td align="right">- R$ {{ number_format($item->descontos, 2, ',', '.') }}</td></tr>@endif
        @if(isset($item->adicionais))<tr><td>Adicionais</td><td align="right">R$ {{ number_format($item->adicionais, 2, ',', '.') }}</td></tr>@endif
        <tr style="border-top:2px solid #111827;font-weight:bold;"><td>Total</td><td align="right">R$ {{ number_format($item->total, 2, ',', '.') }}</td></tr>
    </table>
    <p>Em caso de divergência, procure o RH.</p>
@endsection
