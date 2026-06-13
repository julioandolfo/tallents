<!DOCTYPE html><html lang="pt-BR"><head><meta charset="utf-8">
<style>
  body{font-family:DejaVu Sans,sans-serif;font-size:12px;color:#111;line-height:1.6}
  .cab{border-bottom:2px solid #4f46e5;padding-bottom:10px;margin-bottom:18px}
  h1{font-size:18px;margin:0}
  .meta{color:#666;font-size:10px;margin-top:4px}
  .conteudo{white-space:pre-line;text-align:justify}
  .assin{margin-top:40px;border-top:1px solid #ddd;padding-top:12px;font-size:10px;color:#444}
  .ok{color:#047857;font-weight:bold}
</style></head><body>
  <div class="cab">
    <h1>{{ $contrato->titulo }}</h1>
    <div class="meta">{{ $contrato->tipoLabel() }} · {{ optional($contrato->colaborador)->nome }} · Emitido em {{ now()->format('d/m/Y') }}</div>
  </div>
  <div class="conteudo">{!! nl2br(e($contrato->conteudoRenderizado())) !!}</div>
  <div class="assin">
    @if($contrato->status === 'ASSINADO' && $contrato->metodo_assinatura === 'INTERNO')
      <p class="ok">✓ Assinado eletronicamente em {{ optional($contrato->assinado_em)->format('d/m/Y H:i') }} — IP {{ $contrato->assinatura_ip }}</p>
    @elseif($contrato->metodo_assinatura === 'AUTENTIQUE')
      <p>Documento encaminhado para assinatura eletrônica via Autentique.</p>
    @else
      <p>__________________________________________<br>{{ optional($contrato->colaborador)->nome }}</p>
    @endif
  </div>
</body></html>
