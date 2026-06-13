<!DOCTYPE html><html lang="pt-BR"><head><meta charset="utf-8">
<style>
  body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#111}
  h1{font-size:16px;margin:0 0 4px}.sub{color:#666;font-size:10px;margin-bottom:12px}
  table{width:100%;border-collapse:collapse}th,td{border:1px solid #ddd;padding:5px 7px;text-align:left}
  th{background:#f3f4f6}tr:nth-child(even) td{background:#fafafa}
</style></head><body>
  <h1>Ocorrências</h1>
  <div class="sub">{{ config('app.name') }} — gerado em {{ now()->format('d/m/Y H:i') }} — {{ $ocorrencias->count() }} registro(s)</div>
  <table>
    <thead><tr><th>Colaborador</th><th>Empresa</th><th>Tipo</th><th>Data</th><th>Gravidade</th></tr></thead>
    <tbody>
      @foreach($ocorrencias as $o)
        <tr><td>{{ optional($o->colaborador)->nome }}</td><td>{{ optional(optional($o->colaborador)->empresa)->nome }}</td>
        <td>{{ optional($o->tipoOcorrencia)->nome }}</td><td>{{ optional($o->data_ocorrencia)->format('d/m/Y') }}</td><td>{{ $o->gravidade }}</td></tr>
      @endforeach
    </tbody>
  </table>
</body></html>
