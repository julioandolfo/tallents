<?php
namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\Empresa;
use App\Models\HoraExtra;
use Illuminate\Http\Request;

class HoraExtraController extends Controller
{
    public function index(Request $request)
    {
        $horasExtras = HoraExtra::with(['colaborador.empresa'])
            ->when($request->empresa_id, fn($q, $v) => $q->whereHas('colaborador', fn($sub) => $sub->where('empresa_id', $v)))
            ->when($request->colaborador_id, fn($q, $v) => $q->where('colaborador_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->data_inicio, fn($q, $v) => $q->whereDate('data', '>=', $v))
            ->when($request->data_fim, fn($q, $v) => $q->whereDate('data', '<=', $v))
            ->orderByDesc('data')
            ->paginate(20)
            ->withQueryString();

        $totalValor  = HoraExtra::when($request->empresa_id, fn($q, $v) => $q->whereHas('colaborador', fn($sub) => $sub->where('empresa_id', $v)))
            ->when($request->colaborador_id, fn($q, $v) => $q->where('colaborador_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->sum('valor');

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('horas-extras.index', compact('horasExtras', 'empresas', 'totalValor'));
    }

    public function create()
    {
        $colaboradores = Colaborador::with('empresa')
            ->where('status', 'ATIVO')
            ->orderBy('nome')
            ->get();

        $empresas = Empresa::where('ativa', true)->orderBy('nome')->get();

        return view('horas-extras.create', compact('colaboradores', 'empresas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'colaborador_id' => 'required|exists:colaboradores,id',
            'data'           => 'required|date',
            'horas'          => 'required|numeric|min:0.5|max:24',
            'percentual'     => 'required|in:50,100',
            'descricao'      => 'nullable|string|max:1000',
        ]);

        $colaborador = Colaborador::findOrFail($data['colaborador_id']);

        // Valor hora = salário / 220; valor extra = valor_hora * (1 + percentual/100) * horas
        $valorHora = ($colaborador->salario ?? 0) / 220;
        $fator     = 1 + ($data['percentual'] / 100);
        $data['valor'] = round($valorHora * $fator * $data['horas'], 2);
        $data['status'] = 'PENDENTE';
        $data['registrado_por'] = auth()->id();

        $horaExtra = HoraExtra::create($data);

        return redirect()
            ->route('horas-extras.show', $horaExtra)
            ->with('success', 'Hora extra registrada com sucesso!');
    }

    public function show(HoraExtra $horasExtra)
    {
        $horasExtra->load(['colaborador.empresa', 'registradoPor']);

        return view('horas-extras.show', compact('horasExtra'));
    }

    public function edit(HoraExtra $horasExtra)
    {
        $colaboradores = Colaborador::where('status', 'ATIVO')->orderBy('nome')->get();

        return view('horas-extras.edit', compact('horasExtra', 'colaboradores'));
    }

    public function update(Request $request, HoraExtra $horasExtra)
    {
        $data = $request->validate([
            'colaborador_id' => 'required|exists:colaboradores,id',
            'data'           => 'required|date',
            'horas'          => 'required|numeric|min:0.5|max:24',
            'percentual'     => 'required|in:50,100',
            'descricao'      => 'nullable|string|max:1000',
            'status'         => 'required|in:PENDENTE,APROVADO,RECUSADO',
        ]);

        $colaborador   = Colaborador::findOrFail($data['colaborador_id']);
        $valorHora     = ($colaborador->salario ?? 0) / 220;
        $fator         = 1 + ($data['percentual'] / 100);
        $data['valor'] = round($valorHora * $fator * $data['horas'], 2);

        $horasExtra->update($data);

        return redirect()
            ->route('horas-extras.show', $horasExtra)
            ->with('success', 'Hora extra atualizada com sucesso!');
    }

    public function destroy(HoraExtra $horasExtra)
    {
        $horasExtra->delete();

        return redirect()
            ->route('horas-extras.index')
            ->with('success', 'Hora extra removida com sucesso!');
    }
}
