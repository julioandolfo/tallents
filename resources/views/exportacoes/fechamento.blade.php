<!DOCTYPE html><html lang="pt-BR"><head><meta charset="utf-8">
<style>
  body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#111}
  h1{font-size:16px;margin:0 0 2px}.sub{color:#666;font-size:10px;margin-bottom:12px}
  table{width:100%;border-collapse:collapse}th,td{border:1px solid #ddd;padding:5px 7px}
  th{background:#f3f4f6;text-align:left}td.num{text-align:right}tfoot td{font-weight:bold;background:#f9fafb}
</style></head><body>
  <h1>Demonstrativo de Fechamento — {{ str_pad($fechamento->mes,2,'0',STR_PAD_LEFT) }}/{{ $fechamento->ano }}</h1>
  <div class="sub">{{ optional($fechamento->empresa)->nome }} — gerado em {{ now()->format('d/m/Y H:i') }}</div>
  <table>
    <thead><tr><th>Colaborador</th><th>Salário</th><th>H. Extras</th><th>Bônus</th><th>Descontos</th><th>Adicionais</th><th>Total</th></tr></thead>
    <tbody>
      @foreach($fechamento->itens as $i)
        <tr>
          <td>{{ optional($i->colaborador)->nome }}</td>
          <td class="num">{{ number_format($i->salario_base,2,',','.') }}</td>
          <td class="num">{{ number_format($i->total_horas_extras,2,',','.') }}</td>
          <td class="num">{{ number_format($i->total_bonus,2,',','.') }}</td>
          <td class="num">{{ number_format($i->descontos,2,',','.') }}</td>
          <td class="num">{{ number_format($i->adicionais,2,',','.') }}</td>
          <td class="num">{{ number_format($i->total,2,',','.') }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot><tr><td colspan="6">Total Geral</td><td class="num">R$ {{ number_format($fechamento->itens->sum('total'),2,',','.') }}</td></tr></tfoot>
  </table>
</body></html>
