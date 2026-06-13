<!DOCTYPE html><html lang="pt-BR"><head><meta charset="utf-8">
<style>
  body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#111}
  h1{font-size:16px;margin:0 0 4px}
  .sub{color:#666;font-size:10px;margin-bottom:12px}
  table{width:100%;border-collapse:collapse}
  th,td{border:1px solid #ddd;padding:5px 7px;text-align:left}
  th{background:#f3f4f6}
  tr:nth-child(even) td{background:#fafafa}
</style></head><body>
  <h1>Colaboradores</h1>
  <div class="sub">{{ config('app.name') }} — gerado em {{ now()->format('d/m/Y H:i') }} — {{ $colaboradores->count() }} registro(s)</div>
  <table>
    <thead><tr><th>Nome</th><th>CPF</th><th>Empresa</th><th>Setor</th><th>Cargo</th><th>Status</th><th>Salário</th></tr></thead>
    <tbody>
      @foreach($colaboradores as $c)
        <tr>
          <td>{{ $c->nome }}</td><td>{{ $c->cpf }}</td><td>{{ optional($c->empresa)->nome }}</td>
          <td>{{ optional($c->setor)->nome }}</td><td>{{ optional($c->cargo)->nome }}</td><td>{{ $c->status }}</td>
          <td>{{ $c->salario ? 'R$ '.number_format($c->salario,2,',','.') : '—' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body></html>
